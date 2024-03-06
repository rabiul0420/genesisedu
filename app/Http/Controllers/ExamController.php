<?php

namespace App\Http\Controllers;

use App\BcpsFaculty;
use App\BmDoctors;
use App\DoctorAnswers;
use App\DoctorExam;
use App\DoctorInstituteChoice;
use App\DoctorsCourses;
use App\Exam_question;
use App\Exam_topic;
//use App\courseExam;
use App\InstituteAllocation;
use App\InstituteAllocationCourses;
use App\InstituteDiscipline;
use App\Question;
use App\Question_ans;
use App\QuestionTypes;
use App\Result;
use App\ScheduleDetail;
use App\Subjects;
use Illuminate\Http\Request;
use App\Exam;
use App\ExamQuestion;
use App\ReferenceBookPage;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;
use Carbon\Carbon;

/********* Exam System Master *********/

class ExamController extends Controller
{
    const ANSWER_FILES_ROOT_DIR = 'public/exam_answers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:doctor');
    }

    public static function total_question($exam_id)
    {
        self::get_question($exam_id, 0, $count);
        return $count;
    }


    public static $exam = null;

    protected static function create_file_if_not_exists($exam_id, &$file_path = null)
    {
        if (self::$exam === null) {
            self::$exam = Exam::find($exam_id);
        }

        if (empty(self::$exam->exam_file_link)) {
            self::$exam->exam_file_link = Admin\ExamController::EXAM_FILE_ROOT;
            self::$exam->save();
        }

        $file_path = self::$exam->exam_file_link . '/' . $exam_id . '.json';

        if (file_exists($file_path)) return true;

        $questions = Exam_question::where(['exam_id' => $exam_id])
            ->join('questions', 'questions.id', '=', 'exam_question.question_id')
            ->get(['*', 'exam_question.id as exam_question_id']);

        $data = [];

        foreach ($questions as $question) {
            $data[$question->question_id] = Admin\ExamController::exam_file_item($question);
        }

        Admin\ExamController::save_exam_file(self::$exam, $data);
        return $data;
    }

    public static function get_question($exam_id, $index = 0, &$count = 0)
    {
        $questions = Session::get('__QuestionList__' . $exam_id);
        $question_ids = Session::get('__QuestionIds__' . $exam_id);

        if (!is_array($questions) || !is_array($question_ids)) {
            self::create_file_if_not_exists($exam_id, $file_path);

            if (file_exists($file_path)) {

                $exam_file = file_get_contents($file_path);
                $questions = json_decode($exam_file, true) ?? [];
                $question_ids = array_keys($questions);

                Session::put('__QuestionIndexes__'    .  $exam_id,   $questions);
                Session::put('__QuestionIds__'   .   $exam_id,   $question_ids);
                Session::save();
            }
        }

        if (is_array($questions) && is_array($question_ids)) {

            $count = count($questions);
            $index = $question_ids[$index] ?? 0;
            $data = $questions[$index] ?? null;
            if ($data) {
                $data['id'] = $index;
                return $data;
            }
        }
    }

    public static function reset_answer_file($doctor_course_id, $exam_id)
    {
        $doctor_exam = DoctorExam::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->first();

        if ($doctor_exam) {

            $file_path = empty($doctor_exam->answer_file_link) ? public_path('exam_answers/' . Auth::guard('doctor')->id()) : $doctor_exam->answer_file_link;
            $file_name = $exam_id . '_' . $doctor_course_id;

            if (file_exists($file = $file_path . '/' . $file_name . ".json")) {
                $answer_file = fopen($file, "w") or die("Unable to open file!");
                fwrite($answer_file, '');
                fclose($answer_file);
            }
        }
    }

    public static function get_exam_answers(DoctorExam $doctor_exam, &$file = null)
    {

        if ($doctor_exam) {


            $file_path = !empty($doctor_exam->answer_file_link) ? $doctor_exam->answer_file_link : public_path('exam_answers/' . Auth::guard('doctor')->id());

            $file_name = $doctor_exam->exam_id . '_' . $doctor_exam->doctor_course_id;

            if (!is_dir($file_path)) {
                mkdir($file_path, 0777, true);
            }

            $answers = null;

            //dd( $file );

            if (file_exists($file = $file_path . '/' . $file_name . ".json")) {
                $data = file_get_contents($file);
                $answers = json_decode($data, true);
            }

            return is_array($answers) ? $answers : [];
        }

        return [];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function exam(Request $request, $doctor_course_id, $exam_id, $schedule_id = 0)
    {


        if ($schedule_id) {
            $request->session()->put('__schedule_id', $schedule_id);
        }



        $doctor_exam = DoctorExam::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->first();
        // $last_ex = DoctorExam::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->value('status');

        $exam = Exam::find($exam_id);
        $data['exam'] = $exam;

        $data['options'] = ['A', 'B', 'C', 'D', 'E', 'F'];

        $data['doctor_course'] = DoctorsCourses::where(['id' => $doctor_course_id])->first();

        if ($data['doctor_course']->institute_id == 7) {
            session(['stamp' => 4]);
        } else {
            session(['stamp' => 5]);
        }

        if ($doctor_exam && $doctor_exam->status == "Completed" && Result::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->exists()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Dear doctor you have already taken part in this exam !!! ');
            return redirect('new-schedule/' . $schedule_id . '/' . $doctor_course_id);
        }

        if ($data['exam']->collect_institute_roll && !$data['doctor_course']->roll) {
            return view('collect_doctor_roll', [
                'doctor_course_id' => $doctor_course_id,
                'title' => $exam->institute->name ?? '',
            ]);
        }

        $choice = DoctorInstituteChoice::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id])->exists();

        if ($data['exam']->institute_id == 6 && !$choice) {
            return view('doctor_institute_choice', [
                'instituteAllocations' => InstituteAllocation::orderBy('name')
                    ->whereIn(
                        'id',
                        InstituteAllocationCourses::select('allocation_id')->where('course_id',  $exam->course_id)
                    )->pluck('name', 'id'),
                'instituteDisciplines' => InstituteDiscipline::orderBy('name')->pluck('name', 'id'),
                'exam_id' => $exam_id,
                'doctor_course_id' => $doctor_course_id
            ]);
        }

        if ($doctor_exam && $doctor_exam->status == 'Running' ||  $doctor_exam && $doctor_exam->status == 'Skip-Running') {

            //If the exam need to reset or reopen then a message will set in session flash and redirect to exam page
            if ($this->exam_need_to_reset_or_reopen($doctor_course_id, $exam_id)) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor you did not completed the exam in time !!!<br><br> Please continue to your incomplete exam <a href="' . url('continue-doctor-exam/' . $doctor_course_id . '/' . $exam_id) . '" class="btn btn-xs btn-success">Click Here</a> <br> Or <br>To start the exam again <a href="' . url('/doctor-batch-exam-reopen/' . $doctor_course_id . '/' . $exam_id) . '" class="btn btn-xs btn-info">Click Here</a>');
                return redirect('new-schedule/' . $schedule_id . '/' . $doctor_course_id);
            }

            $last_question = $doctor_exam->last_question ?? 1;

            $data['serial_no'] = $last_question;
            $data['exam_finish'] = "Next";
            $data['total_questions'] = self::total_question($exam_id);

            $num_of_questions  = $data['total_questions'];
            $skips = json_decode($doctor_exam->skips, true) ?? [];

            if ($doctor_exam->status == 'Skip-Running') {
                $data['exam_finish'] = "Finished";
                if (count($skips) > 0) {
                    $data['exam_finish'] = count($skips) === 1 ?  "Finish" : "Next";
                    $last_question = $skips[0];
                    $data['serial_no'] = $last_question;
                }
            } else {
                $data['exam_finish'] = ($num_of_questions == $last_question && count($skips) == 0) ? "Finish" : $data['exam_finish'];
                $data['serial_no'] = $doctor_exam->last_question;
            }

            $current_question = self::get_question($exam_id, $last_question - 1);
            $data['exam_question'] = $current_question;

            $this->update_exam_status($doctor_course_id, $exam_id, $status = $doctor_exam->status);
            $diff_in_seconds = $this->get_difference_in_exam_time($doctor_course_id, $exam_id);
            $data['duration'] = $data['exam']->question_type->duration - $diff_in_seconds;
        } else {

            $data['duration'] = $data['exam']->question_type->duration;
            $data['exam_question'] = self::get_question($data['exam']->id);
            $data['total_questions'] = self::total_question($exam_id);
            $data['serial_no'] = 1;
            $data['exam_finish'] = "Next";

            $current_time = date("Y-m-d H:i:s", time());
            DoctorExam::insert([
                'exam_id' => $exam_id,
                'doctor_course_id' => $doctor_course_id,
                'status' => "Running",
                'answers_file_link' => self::ANSWER_FILES_ROOT_DIR . '/' . Auth::guard('doctor')->id(),
                'created_at' => $current_time,
                'updated_at' => $current_time
            ]);
        }

        $data['schedule_id'] = $schedule_id;

        $data['totalSkip'] = count($skips ?? []) ?? 0;

        return view('exam.exam', $data);
    }

    public function continue_doctor_exam($doctor_course_id, $exam_id)
    {

        $doctorExam = DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id])->first();

        $a = strtotime($doctorExam->updated_at);
        $b = strtotime($doctorExam->created_at);
        $c = time();

        $d = $c - ($a - $b);


        $doctorExam->created_at = date("Y-m-d H:i:s", $d);
        $doctorExam->updated_at = date("Y-m-d H:i:s", $c);

        $doctorExam->save();

        return redirect('doctor-course-exam/' . $doctor_course_id . '/' . $exam_id);
    }

    public function exam_need_to_reset_or_reopen($doctor_course_id, $exam_id)
    {
        $exam = Exam::where(['id' => $exam_id])->first();

        $a = time();
        $b = strtotime(DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id])->value('created_at'));
        $diff_in_seconds = ($a - $b);

        if (isset($exam) && $exam->question_type->duration < $diff_in_seconds) {
            return true;
        } else {
            return false;
        }
    }

    public function update_exam_status($doctor_course_id, $exam_id, $status)
    {
        $time = date('Y-m-d H:i:s', time());
        DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id])->update(['updated_at' => $time, 'status' => $status]);
    }

    public function get_difference_in_exam_time($doctor_course_id, $exam_id)
    {
        $a = strtotime(DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id])->value('updated_at'));
        $b = strtotime(DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id])->value('created_at'));
        return $diff_in_seconds = ($a - $b);
    }

    public static function question_data($exam_id, $question_id = null)
    {
        if (self::$exam === null) {
            self::$exam = Exam::find($exam_id);
        }

        $file_link = isset(self::$exam->exam_file_link) && empty(self::$exam->exam_file_link) ? Admin\ExamController::EXAM_FILE_ROOT : self::$exam->exam_file_link;
        if (file_exists($file_link . '/' . $exam_id . '.json')) {
            $exam_file = file_get_contents($file_link . '/' . $exam_id . '.json');
            $questions = json_decode($exam_file, true) ?? [];

            if ($question_id)
                return $questions[$question_id] ?? [];

            return $questions;
        }

        return [];
    }

    public function course_exam_result_submit($doctor_course_id, $exam_id, $schedule_id = 0)
    {
        $exam = Exam::find($exam_id);
        if (!$exam) return redirect('course-exam-result/' . $doctor_course_id . '/' . $exam_id);;
        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        //$exam = $exam->prepare_result( $doctor_course_id );
        //$exam = new Exam();
        //echo "<pre>";print_r($this->calculate_correct_mark($exam_id,$doctor_course_id));exit;

        $calculated_data = $this->calculate_marks($exam_id, $doctor_course_id);

        // $doctorResultData = [
        //     'exam_id' => $exam->id,
        //     'doctor_course_id'=> $doctor_course_id,
        //     'subject_id'=> $exam->doctor_course->subject_id ?? '',
        //     'batch_id'=> $exam->doctor_course->batch_id ?? '',
        //     'correct_mark'=> $exam->getCorrectMark( ),
        //     'negative_mark'=> $exam->getNegativeMark( ),
        //     'obtained_mark'=> $exam->getObtainedMark( ),
        //     'obtained_mark_percent'=> $exam->getObtainedMarkPercent(),
        //     'obtained_mark_decimal'=> $exam->getObtainedMark( ) * 10,
        //     'wrong_answers'=> $exam->getWrongAnswerCount( ),
        // ];

        $doctorResultData = [
            'exam_id' => $exam->id,
            'doctor_course_id' => $doctor_course_id,
            'subject_id' => $doctor_course->subject_id ?? '',
            'faculty_id' => $doctor_course->faculty_id ?? '',
            'bcps_subject_id' => $doctor_course->bcps_subject_id ?? '',
            'candidate_code' => $doctor_course->candidate_type ?? '',
            'batch_id' => $doctor_course->batch_id ?? '',
            'correct_mark' => $calculated_data['correct_mark'],
            'negative_mark' => $calculated_data['negative_mark'],
            'obtained_mark' => $calculated_data['obtained_mark'],
            'obtained_mark_percent' => $calculated_data['obtained_mark_percent'],
            'obtained_mark_decimal' => $calculated_data['obtained_mark_decimal'],
            'wrong_answers' => $calculated_data['wrong_answer'],
        ];

        $result = Result::where(['exam_id' => $exam->id, 'doctor_course_id' => $doctor_course_id])->first();

        if (!isset($result)) {
            Result::insert($doctorResultData);
        } elseif (isset($result)) {
            Result::where(['exam_id' => $exam->id, 'doctor_course_id' => $doctor_course_id])->update($doctorResultData);
        }

        $this->update_exam_status($doctor_course_id, $exam_id, $status = "Completed");

        return redirect('course-exam-result/' . $doctor_course_id . '/' . $exam->id . '/' . $schedule_id);
    }

    public function calculate_marks($exam_id, $doctor_course_id)
    {
        $exam = Exam::where(['id' => $exam_id])->first();
        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        $doctor_exam = DoctorExam::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->first();

        if (!$doctor_exam) return $this;

        if ($doctor_exam instanceof DoctorExam) {
            $given_answers = $this->get_exam_answers($doctor_exam, $file);
        }

        $mcq_correct_mark =  $mcq2_correct_mark = $sba_correct_mark = 0;
        $mcq_negative_mark = $mcq2_negative_mark = $sba_negative_mark = 0;
        $mcq_wrong_answer = $mcq2_wrong_answer = $sba_wrong_answer = 0;

        foreach ($given_answers as $given_answer) {
            $exam_question = Exam_question::where(['id' => $given_answer['exam_question_id']])->first();
            $count_question_answer = count($exam_question->question->question_answers);

            if ($given_answer['question_type'] == 1 || $given_answer['question_type'] == 3) {
                if ($given_answer['question_type'] == 1) {
                    foreach ($exam_question->question->question_answers as $index => $question_answer) {
                        if (substr($given_answer['answer'], $index, 1) == $question_answer['correct_ans']) {
                            if ($given_answer['question_type'] == 1) {
                                $mcq_correct_mark += $exam_question->exam->question_type->mcq_mark / $count_question_answer;
                            }
                        } else if (substr($given_answer['answer'], $index, 1) != ".") {
                            $mcq_negative_mark += $exam_question->exam->question_type->mcq_negative_mark;
                            $mcq_wrong_answer++;
                        }
                    }
                } else if ($given_answer['question_type'] == 3) {
                    foreach ($exam_question->question->question_answers as $index => $question_answer) {
                        if (substr($given_answer['answer'], $index, 1) == $question_answer['correct_ans']) {
                            if ($given_answer['question_type'] == 3) {
                                $mcq2_correct_mark += $exam_question->exam->question_type->mcq2_mark / $count_question_answer;
                            }
                        } else if (substr($given_answer['answer'], $index, 1) != ".") {
                            $mcq2_negative_mark += $exam_question->exam->question_type->mcq2_negative_mark;
                            $mcq2_wrong_answer++;
                        }
                    }
                }
            } else if ($given_answer['question_type'] == 2 || $given_answer['question_type'] == 4) {
                foreach ($exam_question->question->question_answers as $index => $question_answer) {
                    if (substr($given_answer['answer'], $index, 1) == $question_answer['correct_ans']) {
                        if ($given_answer['question_type'] == 2) {
                            $sba_correct_mark += $exam_question->exam->question_type->sba_mark;
                        }
                    } else if (substr($given_answer['answer'], $index, 1) != ".") {
                        $sba_negative_mark += $exam_question->exam->question_type->sba_negative_mark;
                        $sba_wrong_answer++;
                    }
                    break;
                }
            }
        }

        $data['correct_mark'] = $mcq_correct_mark + $mcq2_correct_mark + $sba_correct_mark;
        $data['negative_mark'] = $mcq_negative_mark + $mcq2_negative_mark + $sba_negative_mark;
        $data['wrong_answer'] = $mcq_wrong_answer + $mcq2_wrong_answer + $sba_wrong_answer;
        $data['obtained_mark'] = $data['correct_mark'] - $data['negative_mark'];
        $data['obtained_mark_percent'] = $data['obtained_mark'] * 100 / $exam->question_type->full_mark;
        $data['obtained_mark_decimal'] = $data['obtained_mark'] * 10;

        return $data;
    }


    private function getSolveClassId(Request $request, $exam_id, $doctor_course_id, $schedule_id = null)
    {


        if (!empty($schedule_id)) {
            $schedule_id = $request->session()->get('__schedule_id');
        }
        //dd( $schedule_id );

        if ($schedule_id) {
            $sql = "
                    SELECT class_or_exam_id FROM schedule_details 
                    WHERE deleted_at IS NULL
                        AND
                        `type` = 'Class'
                        AND
                        parent_id =
                        (   
                            SELECT sd.id FROM schedule_time_slots  sts
                            JOIN schedule_details sd ON sts.id = sd.slot_id
                            WHERE class_or_exam_id = '{$exam_id}'
                            AND sd.deleted_at IS NULL
                            AND sts.deleted_at IS null
                            AND schedule_id = (
                    
                            SELECT bs.id FROM doctors_courses dc
                            JOIN batches_schedules bs  ON bs.batch_id = dc.batch_id
                            WHERE dc.id = {$doctor_course_id} AND bs.id =  '{$schedule_id}'
                        )
                    ORDER BY sd.priority
                    LIMIT 1
                )";
            $d = DB::select($sql);
            return  $d[0]->class_or_exam_id  ?? 0;
        }
        return 0;
    }

    public function course_exam_result($doctor_course_id, $exam_id, $schedule_id = 0)
    {

        $data = [];
        $data['course_exam_result'] = Result::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->first();
        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        //echo "<pre>";print_r($data['course_exam_result']);exit;
        if (isset($data['course_exam_result'])) {
            if ($doctor_course->institute->type == "1") {

                if ($doctor_course->institute->id == "16") {
                    if ($data['course_exam_result']->batch_id == '' || $data['course_exam_result']->faculty_id == '' || $data['course_exam_result']->subject_id == '' || $data['course_exam_result']->bcps_subject_id == '') {
                        return redirect('course-exam-result-submit/' . $doctor_course_id . '/' . $exam_id . '/' . $schedule_id);
                    }
                } else {
                    if ($data['course_exam_result']->batch_id == '' || $data['course_exam_result']->faculty_id == '') {
                        return redirect('course-exam-result-submit/' . $doctor_course_id . '/' . $exam_id . '/' . $schedule_id);
                    }
                }
            } else if ($doctor_course->institute->type == "0") {
                if ($data['course_exam_result']->batch_id == '' || $data['course_exam_result']->subject_id == '') {
                    return redirect('course-exam-result-submit/' . $doctor_course_id . '/' . $exam_id . '/' . $schedule_id);
                }
            }

            $data['solve_class_id'] = $this->getSolveClassId(request(), $exam_id, $doctor_course_id, $schedule_id);

            $data['result'] = $this->result($doctor_course_id, $exam_id); //echo "<pre>";print_r($data['result']);exit;
            $data['schedule_id'] = $schedule_id;
            return view('exam.course_exam_result', $data);
        } else {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Dear doctor you did not take part this exam yet!!!');
            return redirect('doctor-course-online-exam/' . $doctor_course_id);
        }
        //echo "<pre>";print_r($data['course_exam_result']);exit;

    }


    public function set_given_answers($question_type, $answer, $question_id, &$given_answers = [])
    {

        if ($question_type == 1 || $question_type == 3) {
            foreach (['A', 'B', 'C', 'D', 'E'] as $i => $sl_no) {
                $given_answers[$question_id][$sl_no] = substr($answer, $i, 1);
            }
        } else if ($question_type == 2 || $question_type == 4) {
            $given_answers[$question_id] = $answer;
        }
    }

    public function reference_book_details($reference_book_id, $page_no)
    {
        $reference = $previous = $next = '';

        $reference = ReferenceBookPage::query()
            ->where([
                'reference_book_id' => $reference_book_id,
                'page_no'           => $page_no,
            ])
            ->first(['id', 'reference_book_id', 'page_no', 'body']);

        if(!$reference) {
            return abort(404, 'Reference page not available!');
            return back()->with([
                'status' => 'No Link Available'
            ]);    
        }
        
        $previous = ReferenceBookPage::query()
            ->where('reference_book_id', $reference_book_id)
            ->where('page_no', '<', $reference->page_no)
            ->latest('page_no')
            ->first(['id', 'reference_book_id', 'page_no']);

        $next = ReferenceBookPage::query()
            ->where('reference_book_id', $reference_book_id)
            ->where('page_no', '>', $reference->page_no)
            ->oldest('page_no')
            ->first(['id', 'reference_book_id', 'page_no']);


        return view('exam.reference_book_details', compact('reference', 'previous', 'next'));
    }

    public function course_exam_doctor_answer($doctor_course_id, $exam_id, $schedule_id = 0)
    {
        $data = [];

        $data['exam'] = Exam::find($exam_id);
        $data['solve_class_id'] = $data['solve_class_id'] = $this->getSolveClassId(request(), $exam_id, $doctor_course_id, $schedule_id);;

        $exam = Exam::find($exam_id);
        $doctor_course = DoctorsCourses::where('id', $doctor_course_id)->first();
        $doctor_exam = DoctorExam::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->first();

        if (isset($doctor_exam) && $doctor_exam->status == "Completed") {

            if ($doctor_course->institute_id == 7) {
                session(['stamp' => 4]);
            } else {
                session(['stamp' => 5]);
            }

            $doctor_answer_files = self::get_exam_answers($doctor_exam);
            $given_answers = array();

            if (count($doctor_answer_files)) {
                foreach ($doctor_answer_files as $question_id => $doctor_answer) {
                    $this->set_given_answers($doctor_answer['question_type'], $doctor_answer['answer'], $question_id, $given_answers);
                }
            } else {
                $doctor_answers = DoctorAnswers::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id]);
                if ($doctor_answers->exists()) {

                    foreach ($doctor_answers->get() as $doctor_answer) {
                        $question_type = $doctor_answer->exam_question->question->type ?? null;
                        $answer = $doctor_answer->answer;
                        $question_id = $doctor_answer->exam_question->question_id ?? null;

                        $this->set_given_answers($question_type, $answer, $question_id, $given_answers);
                    }
                }
            }

            foreach ($exam->exam_questions as $exam_question) {
                if (isset($exam_question->question->question_title))
                // dd($exam_question);
                {
                    if ($exam_question->question->type == "1" || $exam_question->question->type == "3") {
                        foreach ($exam_question->question->question_answers as $k => $answer) {
                            if (!isset($given_answers[$exam_question->question->id][$answer->sl_no])) {
                                $given_answers[$exam_question->question->id][$answer->sl_no] = '.';
                            }
                        }
                        // dd($exam_question);
                    } else if ($exam_question->question->type == "2" || $exam_question->question->type == "4") {
                        if (!isset($given_answers[$exam_question->question->id])) {
                            $given_answers[$exam_question->question->id] = '.';
                        }
                    }
                }
            }

            $data['given_answers'] = $given_answers;
            $data['doctor_course_id'] = $doctor_course_id;
            $data['exam_id'] = $exam_id;

            return view('exam.course_exam_doctor_answer', $data);
        } else {

            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Dear doctor you did not completed the exam or did not take part this exam yet!!!');
            return redirect('doctor-course-online-exam/' . $doctor_course_id);
        }
    }

    public function only_question_list($doctor_course_id, $exam_id)
    {
        //    $doctor_exam = DoctorExam::where(['doctor_course_id'=>$doctor_course_id, 'exam_id'=>$exam_id])->first();
        //     $question_ids = Exam_Question::where(['exam_id'=>$doctor_exam->exam_id])->get();
        //     $questions =array();
        //     foreach($question_ids as $question_id){
        //        $questions[] = Question::where('id',$question_id->question_id)->get();
        //     }
        $exam = Exam::with('course', 'sessions')->find($exam_id);
        $doctor_course = DoctorsCourses::with('subject', 'faculty', 'batch')->where('id', $doctor_course_id)->first();
        return view('online_exam.only_question_list_pdf', compact('exam', 'doctor_course'));
    }

    public function question_answer_list($doctor_course_id, $exam_id)
    {
        $exam = Exam::with('course', 'sessions')->find($exam_id);
        $doctor_course = DoctorsCourses::with('subject', 'faculty', 'batch')->where('id', $doctor_course_id)->first();
        return view('online_exam.question_answer_list_pdf', compact('exam', 'doctor_course'));
    }

    public function course_exam_doctor_answer_video($id)
    {
        $data = Question::with('question_video_links')->findOrFail($id);
        return view('exam.course_exam_result_video', compact('data'));
    }

    public function doctor_batch_exam_reopen($doctor_course_id, $exam_id)
    {
        ExamController::reset_answer_file($doctor_course_id, $exam_id);

        DoctorExam::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->delete();
        DoctorAnswers::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->delete();
        Result::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->delete();

        Session::flash('class', 'alert-success');
        session()->flash('message', 'Exam Reopened And Started Successfully....');

        return redirect('doctor-course-exam/' . $doctor_course_id . '/' . $exam_id);
    }

    function candidate_position(DoctorsCourses $doctor_course, $exam_id, $obtained_mark_single)
    {

        $subject_id = $doctor_course->subject_id;
        $candidate_type = $doctor_course->candidate_type;
        $subject_name = Subjects::where('id', $subject_id)->value('name');
        $results = Result::with('doctor_course')
            ->join('subjects', 'subjects.id', '=', 'results.subject_id')
            ->where(['subjects.name' => $subject_name,  'exam_id' => $exam_id])
            ->orderBy('obtained_mark', 'desc')->get();
        $candidate_results = $results->where('doctor_course.candidate_type', $candidate_type);
        $total_candidate = count($candidate_results);
        $obtained_mark = 0;
        $possition = 0;
        $i = 0;
        foreach ($candidate_results as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                $i++;
            } else {
                $pos = $possition;
            }
            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;
            if ($row->obtained_mark == $obtained_mark_single) {
                return [$pos, $total_candidate];
            }
        }
    }

    public function result($doctor_course_id, $exam_id)
    {
        $result = array();
        $exam = Exam::find($exam_id);
        $doctor_course = DoctorsCourses::find($doctor_course_id);
        $doctor_course_result = Result::where(['doctor_course_id' => $doctor_course->id, 'exam_id' => $exam_id])->first();
        $result['highest_mark']  = Result::where('exam_id', $exam_id)->orderBy('obtained_mark', 'desc')->value('obtained_mark');
        $result['overall_position'] = $this->overall_position($exam->id, $doctor_course_result->id);
        $result['subject_position'] = $this->subject_position($exam->id, $doctor_course->subject_id, $doctor_course_result->id);
        $result['batch_position'] = $this->batch_position($exam->id, $doctor_course->batch_id, $doctor_course_result->id);
        $result['candidate_position'] =  $this->candidate_position($doctor_course, $exam_id, $doctor_course_result->obtained_mark);
        //$result = $this->view_doctor_result($doctor_course_id,$exam_id);

        return $result;
    }



    function overall_position($exam_id, $doctor_course_result_id)
    {
        $results = Result::where(['exam_id' => $exam_id])->orderBy('obtained_mark', 'desc')->get();
        $doctor_course_result = Result::where(['id' => $doctor_course_result_id])->first();

        foreach ($results as $k => $row) {

            if ($doctor_course_result->obtained_mark == $row->obtained_mark) {

                $p = ($k + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                break;
            }
        }

        return $pos;
    }

    function subject_position($exam_id, $subject_id, $doctor_course_result_id)
    {
        $results = Result::where(['exam_id' => $exam_id, 'subject_id' => $subject_id])->orderBy('obtained_mark', 'desc')->get();
        $doctor_course_result = Result::where(['id' => $doctor_course_result_id])->first();


        $pos = 0;

        foreach ($results as $k => $row) {

            if ($doctor_course_result->obtained_mark == $row->obtained_mark) {

                $p = ($k + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                break;
            }
        }

        return $pos;
    }


    function batch_position($exam_id, $batch_id, $doctor_course_result_id)
    {
        $results = Result::where(['exam_id' => $exam_id, 'batch_id' => $batch_id])->orderBy('obtained_mark', 'desc')->get();
        $doctor_course_result = Result::where(['id' => $doctor_course_result_id])->first();



        $pos = 0;

        foreach ($results as $k => $row) {

            if ($doctor_course_result->obtained_mark == $row->obtained_mark) {

                $p = ($k + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                break;
            }
        }

        return $pos;
    }



    public function view_doctor_result($doctor_course_id, $id)
    {
        $exam = Exam::find($id);
        $exam->highest_mark  = Result::where('exam_id', $id)->orderBy('obtained_mark', 'desc')->value('obtained_mark');
        $doctor_courses = Result::where('exam_id', $id)->orderBy('obtained_mark', 'desc')->get();

        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($doctor_courses as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $row->possition = $p . $th;
                $i++;
            } else {
                $row->possition = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;

            $row->subject_possition = $this->subject_possition($row->subject_id, $id, $row->obtained_mark);
            $row->batch_possition = $this->batch_possition($row->batch_id, $id, $row->obtained_mark);
            $row->faculty = BcpsFaculty::where(['id' => $row->faculty_id])->value('name');
        }

        $result = array();

        $results = $doctor_courses;
        foreach ($results as $result) {
            if ($result->doctor_course_id == $doctor_course_id) {
                $result['highest_mark']  = $exam->highest_mark;
                $result['overall_position'] = $result->possition;
                $result['subject_position'] = $result->subject_possition;
                $result['batch_possition'] = $result->batch_possition;
            }
        }

        return $result;
    }

    public function view_result($id)
    {
        $exam = Exam::find($id);
        $exam->highest_mark  = Result::where('exam_id', $id)->orderBy('obtained_mark', 'desc')->value('obtained_mark');
        $doctor_courses = Result::where('exam_id', $id)->orderBy('obtained_mark', 'desc')->get();

        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($doctor_courses as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $row->possition = $p . $th;
                $i++;
            } else {
                $row->possition = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;

            $row->subject_possition = $this->subject_possition($row->subject_id, $id, $row->obtained_mark);
            $row->batch_possition = $this->batch_possition($row->batch_id, $id, $row->obtained_mark);
            $row->faculty = BcpsFaculty::where(['id' => $row->faculty_id])->value('name');
        }

        // exit;

        $data['doctor_courses'] = $doctor_courses;

        $data['exam'] = $exam;
        $data['title'] = 'Results';

        $data['paper_faculty'] = QuestionTypes::where('id', $exam->question_type_id)->value('paper_faculty');

        $data['examination_code']  = Result::where('exam_id', $id)->value('examination_code');
        $data['candidate_code']  = Result::where('exam_id', $id)->value('candidate_code');

        return view('admin.exam.view_result', $data);
    }

    function subject_possition($subject_id, $exam_id, $obtained_mark_single)
    {
        $subjectresult = Result::where(['subject_id' => $subject_id, 'exam_id' => $exam_id])->orderBy('obtained_mark', 'desc')->get();

        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($subjectresult as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                $i++;
            } else {
                $pos = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;

            if ($row->obtained_mark == $obtained_mark_single) {
                return $pos;
            }



            /*foreach ($subjectresult as $k=>$single){
                if($single->obtained_mark == $obtained_mark){
                    $sp = ($k+1);
                    $th = ($sp==1)?'st':(($sp==2)?'nd':(($sp==3)?'rd':'th'));
                    return $sp.$th;
                }
            }*/
        }
    }


    function batch_possition($batch_id, $exam_id, $obtained_mark_single)
    {
        $batchresult = Result::where(['batch_id' => $batch_id, 'exam_id' => $exam_id])->orderBy('obtained_mark', 'desc')->get();

        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($batchresult as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                $i++;
            } else {
                $pos = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;

            if ($row->obtained_mark == $obtained_mark_single) {
                return $pos;
            }
        }
    }
}
