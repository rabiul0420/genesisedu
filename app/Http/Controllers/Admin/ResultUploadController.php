<?php

namespace App\Http\Controllers\Admin;

use App\DoctorExam;
use App\DoctorsCourses;
use App\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OmrScript;
use App\Result;
use App\SendSms;

class ResultUploadController extends Controller
{
    use SendSms;

    public function form(Exam $exam)
    {
        $front_page_property_id = 7;
        $back_page_property_id = 8;
        $last_page_property_id = 9;

        $omr_scripts = OmrScript::query()
            ->with([
                'properties' => function ($query) {
                    $query->whereIn('omr_script_property_id', [7, 8, 9]);
                },
            ])
            ->where('status', 1)
            ->get();

        $scripts = $omr_scripts->map(function ($omr_script) use ($front_page_property_id, $back_page_property_id, $last_page_property_id) {
            return [
                "id"        => (int) $omr_script->id,
                "name"      => (string) $omr_script->name,
                "has_front" => (boolean) $omr_script->properties->where('omr_script_property_id', $front_page_property_id)->count(),
                "has_back"  => (boolean) $omr_script->properties->where('omr_script_property_id', $back_page_property_id)->count(),
                "has_last"  => (boolean) $omr_script->properties->where('omr_script_property_id', $last_page_property_id)->count(),
            ];
        });

        $is_published = $exam->results()->exists();

        return view('admin.exam.upload_result', compact('scripts', 'is_published'));
    }

    public function store(Request $request)
    {
        $exam = Exam::query()
            ->with([
                'question_type',
                'exam_questions:id,exam_id,question_id,question_type',
                'exam_questions.question:id,type,question_title',
                'exam_questions.question.question_answers',
            ])
            ->findOrFail($request->exam_id);

        // return $exam;

        $file_json_format = [];

        $omr_script = OmrScript::query()
            ->with([
                'properties:id,omr_script_id,omr_script_property_id,start_position,end_position',
                'properties.omr_script_property:id,name',
            ])
            ->findOrFail($request->omr_script_id);

        $exam_answer_script = $this->examAnswerScript($exam, $file_json_format);

        // return $file_json_format;

        $positions = $this->allPropertyPosition($omr_script->properties);
        
        $file_front_part = $request->file('result_front_part');
        $file_back_part = $request->file('result_back_part');
        $file_last_part = $request->file('result_last_part');

        $result_array['front_part'] = $this->resultGroupByRegistration($positions, $file_front_part, $exam, $exam_answer_script, "front_part");

        if($file_back_part) {
            $result_array['back_part'] = $this->resultGroupByRegistration($positions, $file_back_part, $exam, $exam_answer_script, "back_part", $positions["final_back"][0]);
        }

        if($file_last_part) {
            $result_array['last_part'] = $this->resultGroupByRegistration($positions, $file_last_part, $exam, $exam_answer_script, "last_part", $positions["final_last"][0]);
        }

        $final_results = [];

        $counter = 0;

        foreach($result_array['front_part']['calculate'] as $reg_no => $results) {
            $final_results[$reg_no] = [
                'correct_mark'          => $this->sumAllResult($result_array, $reg_no, 'correct_mark'),
                'negative_mark'         => $this->sumAllResult($result_array, $reg_no, 'negative_mark'),
                'obtained_mark'         => $this->sumAllResult($result_array, $reg_no, 'obtained_mark'),
                'obtained_mark_percent' => $this->sumAllResult($result_array, $reg_no, 'obtained_mark_percent'),
                'obtained_mark_decimal' => $this->sumAllResult($result_array, $reg_no, 'obtained_mark_decimal'),
                'correct_answers'       => $this->sumAllResult($result_array, $reg_no, 'correct_answers'),
                'wrong_answers'         => $this->sumAllResult($result_array, $reg_no, 'wrong_answers'),
            ];

            
            $calculated_data = $final_results[$reg_no];
            
            $doctor_course = DoctorsCourses::query()
                ->where([
                    'reg_no'    => $reg_no,
                    'is_trash'  => 0,
                ])
                ->first();
                
            if(!$doctor_course) {
                continue;
            }
                
            $this->concatScripResult($result_array, $reg_no, $file_json_format, $exam, $doctor_course);
                
            $doctor_exam = DoctorExam::updateOrCreate(
                [
                    'exam_id'           => $exam->id,
                    'doctor_course_id'  => $doctor_course->id
                ],
                [
                    'status'            => "Completed",
                    'answers_file_link' => 'exam_answers/' . $doctor_course->doctor_id,
                ]
            );

            $doctorResultData = [
                'exam_id'           => $exam->id,
                'doctor_course_id'  => $doctor_course->id,
                'subject_id'        => $doctor_course->subject_id ?? '',
                'faculty_id'        => $doctor_course->faculty_id ?? '',
                'bcps_subject_id'   => $doctor_course->bcps_subject_id ?? '',
                'candidate_code'    => $doctor_course->candidate_type ?? '',
                'batch_id'          => $doctor_course->batch_id ?? '',
                'correct_mark'      => $calculated_data['correct_mark'],
                'negative_mark'     => $calculated_data['negative_mark'],
                'obtained_mark'     => $calculated_data['obtained_mark'],
                'wrong_answers'     => $calculated_data['wrong_answers'],
                'obtained_mark_percent' => $calculated_data['obtained_mark_percent'],
                'obtained_mark_decimal' => $calculated_data['obtained_mark_decimal'],
            ];

            Result::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'doctor_course_id' => $doctor_course->id
                ],
                $doctorResultData,
            );

            $counter++;

            $website = 'https://genesisedu.info';

            $sms = "Dear Doctor your {$exam->name} exam result published, please visit " . $website;

            if($request->sms) {
                $this->send_custom_sms($doctor_course->doctor, $sms, 'OMR Result Published', $isAdmin = true);
            }
        }

        // return $this->preOutput(313);

        return redirect('admin/upload-result/' . $exam->id)
            ->with('message', "{$counter} Results added successfully !!!");

    }

    public function examAnswerScript($exam, &$file_json_format)
    {
        $exam_questions = $exam->exam_questions;

        foreach($exam_questions as &$exam_question) {
            $options = $exam_question->question->question_answers;

            $answer_script = "";

            foreach($options as $option) {
                $answer_script .= $option->correct_ans ?? ".";
                if($exam_question->question->type == 2) {
                    break;
                }
            }

            $file_json_format[$exam_question->question_id] = [
                'exam_question_id'  => $exam_question->id,
                'answer'            => $exam_question->question->type == 2 ? "." : ".....",
                'question_type'     => $exam_question->question->type,
            ];

            $exam_question->question->script = $answer_script;
        }

        $exam_answer_script = '';

        foreach($exam->exam_questions as $exam_question) {
            $exam_answer_script .= $exam_question->question->script;
        }

        return $exam_answer_script;
    }

    public function resultGroupByRegistration($positions, $file, $exam, $exam_answer_script, $position_key, $offset = 0)
    {
        $line_array = explode("\r\n", file_get_contents($file->getRealPath()));

        $result_array = [];

        $calculate_result_array = [];

        foreach($line_array as $line)
        {                
            if(strlen($line ?? ''))
            {
                $reg_no = substr(trim($exam->year), 2, 4) . substr(trim($line), $positions["reg_no"][0], $positions["reg_no"][1]);

                $result_array[$reg_no] = str_replace('>', '.', substr(str_replace(',', '', trim($line)), $positions[$position_key][0], $positions[$position_key][1]) ?? '');
                
                $calculate_result_array[$reg_no] = $this->checkAnserScript($exam, $exam_answer_script, $result_array[$reg_no], $offset);
            }
        }

        return [
            'script' => $result_array,
            'calculate' => $calculate_result_array,
        ];
    }

    public function allPropertyPosition($properties)
    {
        $positions = [
            'reg_no'        => $this->propertyPosition($properties, "Reg No", 0, 6),
            'examination'   => $this->propertyPosition($properties, "Examination"),
            'set_code'      => $this->propertyPosition($properties, "Set Code"),
            'faculty'       => $this->propertyPosition($properties, "Faculty"),
            'candidate'     => $this->propertyPosition($properties, "Candidate"),
            'subject'       => $this->propertyPosition($properties, "Subject"),
            'front_part'    => $this->propertyPosition($properties, "Front Part Answer"),
            'back_part'     => $this->propertyPosition($properties, "Back Part Answer"),
            'last_part'     => $this->propertyPosition($properties, "Last Part Answer"),
        ];

        $positions["final_front"] = [0, $positions["front_part"][1] - $positions["front_part"][0]];
        $positions["final_back"] = [$positions["final_front"][1], $positions["final_front"][1] + $positions["back_part"][1] - $positions["back_part"][0]];
        $positions["final_last"] = [$positions["final_back"][1], $positions["final_back"][1] + $positions["last_part"][1] - $positions["last_part"][0]];
    
        return $positions;
    }

    public function propertyPosition($properties, $selector, $default_start_position = 0, $default_end_position = 0)
    {
        $property = $properties->where('omr_script_property.name', $selector)->first();

        return $property
            ? [$property->start_position, $property->end_position]
            : [$default_start_position, $default_end_position];
    }
 
    public function checkAnserScript($exam, $exam_answer_script, $doctor_answer, $offset = 0)
    {
        $mcq_number = $exam->question_type->mcq_number ?? 0;
        $sba_number = $exam->question_type->sba_number ?? 0;
        $mcq2_number = $exam->question_type->mcq2_number ?? 0;

        $exam_answer_script = substr($exam_answer_script, $offset);

        $doctor_answer_array = str_split(str_replace(">", ".", $doctor_answer));
        $answer_script_array = str_split($exam_answer_script);

        $correct_mark = $negative_mark = $obtained_mark = $obtained_mark_percent = $obtained_mark_decimal = $correct_answers = $wrong_answers = 0;
        
        foreach($doctor_answer_array as $index => $answer) {
            if(!isset($answer_script_array[$index]) || $answer_script_array[$index] == '.') {
                continue;
            }

            if($index < ($mcq_number * 5)) {
                $mark = abs($exam->question_type->mcq_mark/5);
                $negative = abs($exam->question_type->mcq_negative_mark);
            } elseif ($index < ($mcq_number * 5) + $sba_number) {
                $mark = abs($exam->question_type->sba_mark);
                $negative = abs($exam->question_type->sba_negative_mark);
            } else {
                $mark = abs($exam->question_type->mcq2_mark/5);
                $negative = abs($exam->question_type->mcq2_negative_mark);
            }

            if($answer_script_array[$index] == $answer) {
                $correct_mark += $mark;
                $correct_answers++;
            } else {
                $wrong_answers++;
                $negative_mark += $negative;
            }
        }

        $obtained_mark = round($correct_mark - $negative_mark, 2);
        $obtained_mark_decimal = round($obtained_mark) * 10;
        $obtained_mark_percent = round(($obtained_mark/$exam->question_type->full_mark) * 100, 2);

        return compact(
            'correct_mark',
            'negative_mark',
            'obtained_mark',
            'obtained_mark_percent',
            'obtained_mark_decimal',
            'correct_answers',
            'wrong_answers',
        );
    }

    public function sumAllResult($results, $reg_no, $result_key)
    {
        return ($results['front_part']['calculate'][$reg_no][$result_key] ?? 0)
            + ($results['back_part']['calculate'][$reg_no][$result_key] ?? 0)
            + ($results['last_part']['calculate'][$reg_no][$result_key] ?? 0);
    }

    public function concatScripResult($results, $reg_no, $file_json_format, $exam, $doctor_course)
    {
        $doctor_answer_script = ($results['front_part']['script'][$reg_no] ?? '')
            . ($results['back_part']['script'][$reg_no] ?? '')
            . ($results['last_part']['script'][$reg_no] ?? '');

        $arr = str_split($doctor_answer_script);

        foreach($file_json_format as &$format) {
            if(!count($arr)) {
                break;
            }

            $format['answer'] = $format['question_type'] == 2
                ? array_shift($arr)
                : array_shift($arr) . array_shift($arr) . array_shift($arr) . array_shift($arr) . array_shift($arr);
        }

        $file_path = public_path('exam_answers/' . $doctor_course->doctor_id);

        $file = public_path('exam_answers/' . $doctor_course->doctor_id . '/' . $exam->id . '_' . $doctor_course->id . ".json");

        clearstatcache();

        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }

        clearstatcache();
        $txt = json_encode($file_json_format);
        file_put_contents($file, $txt);

        return true;
    }
}
