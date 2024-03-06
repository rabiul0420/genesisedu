<?php

namespace App\Http\Controllers\Admin;

use App\Batches;
use App\Doctors;
use App\DoctorsCourses;
use App\Exam;
use App\Exports\BatchResultExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Sms;
use App\Result;
use App\SmsLog;
use App\SendSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ResultController extends Controller
{
    use SendSms;
    public function index($batch_id, $onlyData = false)
    {
        $batch = Batches::find($batch_id, ['id', 'name']);

        // return
        $results = $this->getResultByBatch($batch);

        // return
        $exams = $this->getExamByResult($results);

        // return
        $doctor_courses = $this->getDoctorCourseByBatch($batch);

        // return
        $data = $this->getDataFomate($exams, $results);

        $data['batch'] = $batch;

        $data['doctor_courses'] = $doctor_courses;

        $data['identifiers'] = SmsLog::query()
            ->whereNotNull('identifier')
            ->pluck('identifier')
            ->unique()
            ->toArray();

        if ($onlyData) {
            return $data;
        }

        return view('admin.results.index', $data);
    }

    public function excelDownload($batch_id)
    {
        extract($this->index($batch_id, true)); // batch, exams, data, doctor_courses

        // return $batch;
        // return $doctor_courses;
        // return $exams;
        // return $data;


        // Heading
        $heading = [
            'Doctor Name',
            'Phone',
        ];

        foreach ($exams as $exam) {
            array_push($heading, $exam->name);
        }

        // return 
        $row[0] = $heading;

        foreach ($doctor_courses as $index => $doctor_course) {
            $row[$index + 1] = [
                $doctor_course->doctor->name ?? '',
                $doctor_course->doctor->phone ? "88" . substr($doctor_course->doctor->phone, -11, 11) : '',
            ];

            // return $row[$index + 1];

            foreach ($exams as $exam) {
                array_push($row[$index + 1], $data[$exam->id][$doctor_course->id]->obtained_mark ?? 'A');
            }
        }

        // return $row;

        return Excel::download(new BatchResultExport($row), date('Y-m-d') . '_' . str_slug($batch->name, '_')  . '.xlsx');
    }

    private function getResultByBatch($batch)
    {
        return Result::query()
            ->where('batch_id', $batch->id)
            ->get([
                'id',
                'doctor_course_id',
                'exam_id',
                'batch_id',
                'obtained_mark',
                'wrong_answers',
            ]);
    }

    private function getExamByResult($results)
    {
        $exam_ids = $results->pluck('exam_id')->unique();

        return Exam::query()
            ->with('question_type:id,full_mark,pass_mark')
            ->whereIn('id', $exam_ids)
            ->get([
                'id',
                'name',
                'question_type_id'
            ]);
    }

    private function getDoctorCourseByBatch($batch)
    {
        return DoctorsCourses::query()
            ->with('doctor:id,name,bmdc_no,mobile_number')
            ->where([
                'batch_id'          => $batch->id,
                'payment_status'    => 'Completed',
                'status'            => 1,
                'is_trash'          => 0,
            ])
            ->get([
                'id',
                'doctor_id',
                'reg_no',
                'batch_id',
            ]);
    }

    private function getDataFomate($exams, $results)
    {
        $data = [];

        foreach ($exams as $exam) {
            $exam->highest = $results->where('exam_id', $exam->id)->max('obtained_mark');
        }

        foreach ($results as $result) {
            $result->highest = $results->where('exam_id', $result->exam_id)->max('obtained_mark');
            $result->position = $results->where('exam_id', $result->exam_id)->where('obtained_mark', '>', $result->obtained_mark)->count() + 1;

            $data[$result->exam_id][$result->doctor_course_id] = $result;
        }

        return compact('exams', 'data');
    }

    public function sendSmsForAbsentInExam(Batches $batch, Exam $exam, $doctor_id = null)
    {

        $msg = "প্রিয় চিকিৎসক আপনি {$batch->name} ব্যাচের {$exam->name} পরীক্ষায় অংশগ্রহন করেন নি। এই পরীক্ষাটি সুন্দর প্রস্তুতি ও ভালো ফলাফলের জন্য খুবই গুরুত্বপূর্ন। আজই উক্ত পরীক্ষায় অংশগ্রহন করুন এবং নিজের প্রস্তুতিকে আরো শানিত করুন। GENESIS";

        $identifier = "sms_container_{$batch->id}_{$exam->id}_{$doctor_id}";

        // $event = 'Exam Reminder';

        if ($doctor_id && $doctor = Doctors::where('id', $doctor_id)->first()) {
            $this->send_custom_sms($doctor,$msg,'Exam Reminder'); 
            // $this->sendSmsWithSmsLog($doctor, $text, $event, $identifier);
        }
        // if(!$doctor_id) {
        //     $doctors = $this->getDoctorCourseByBatch($batch);

        //     foreach($doctors as $doctor) {
        //         // $this->sendSmsWithSmsLog($doctor, $text, $event, $identifier);
        //     }
        // }

        // return response([
        //     "message"   => "success",
        //     "text"      => $msg,
        // ]);
    }

    public function sendSmsWithSmsLog($doctor, $text, $event, $identifier = null)
    {
        $sms = Sms::init()->setRecipient($doctor->mobile_number)->setText($text);
        $sms->send();
        $sms->save_log($event, $doctor->id, $doctor->mobile_number, Auth::id(), $identifier);
    }
}
