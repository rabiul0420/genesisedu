<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ScheduleDetail extends Model
{

    use SoftDeletes;
    public $timestamps = false;
    protected $guarded = [];

    public function video()
    {
        return $this->hasOne(\App\LectureVideo::class, 'id', 'class_or_exam_id');
    }

    public function batchSchedule()
    {
        return $this->belongsTo(\App\BatchesSchedules::class, 'id', 'class_or_exam_id');
    }

    public function mentor()
    {
        return $this->hasOne(\App\Teacher::class, 'id', 'mentor_id');
    }

    public function exam()
    {
        return $this->hasOne(\App\Exam::class, 'id', 'class_or_exam_id');
    }

    public function parentClass()
    {
        return $this->hasOne('App\ScheduleDetail', 'parent_id', 'id');
    }

    public function lectures()
    {
        return $this->hasMany('App\ScheduleDetail', 'parent_id', 'id')->where('type', 'Class');
    }

    public function schedule()
    {
        return $this->belongsTo('App\BatchesSchedules', 'id', 'schedule_id');
    }

    public function time_slot()
    {
        return $this->hasOne('App\ScheduleTimeSlot', 'id', 'slot_id');
    }

    public function doctor_class_views()
    {

        return $this->hasMany('App\DoctorClassView', 'lecture_video_id', 'class_or_exam_id');
    }

    public function doctor_class_view()
    {
        return $this->hasOne('App\DoctorClassView', 'lecture_video_id', 'class_or_exam_id')
            ->where('doctor_course_id', self::$_doctor_course_id);
    }

    public function doctor_feedbacks()
    {
        return $this->hasMany('App\DoctorClassRating', 'details_id', 'id')->where('doctor_id', Auth::id());
    }


    public static function doctorCourseId($course_id)
    {
        return DoctorsCourses::where('course_id', $course_id)
            ->where('doctor_id', Auth::id())->value('id');
    }

    public static function update_doctor_class_view($doctor_course_id, $lecture_video_id)
    {

        if (DoctorsCourses::where('doctor_id', Auth::id())->where('id', $doctor_course_id)->exists()) {
            if (
                LectureVideo::find($lecture_video_id) &&
                !DoctorClassView::where(['doctor_course_id' => $doctor_course_id, 'lecture_video_id' => $lecture_video_id])->exists()
            ) {

                DoctorClassView::insert([
                    'doctor_course_id' => $doctor_course_id,
                    'lecture_video_id' => $lecture_video_id
                ]);
            }
        }
    }

    // public static function update_doctor_class_view($doctor_course_id, $lecture_video_id)
    // {
    //     $doctor_class_view = DoctorClassView::where(['doctor_course_id' => $doctor_course_id, 'lecture_video_id' => $lecture_video_id])->first();

    //     if (DoctorsCourses::where('doctor_id', Auth::id())->where('id', $doctor_course_id)->exists()) {
    //         if (LectureVideo::find($lecture_video_id) && !$doctor_class_view) {
    //             DoctorClassView::insert(
    //                 [
    //                     'doctor_course_id' => $doctor_course_id,
    //                     'lecture_video_id' => $lecture_video_id,
    //                     'end'              => date('Y-m-d H:i:s', strtotime('+15 days')),
    //                     'status'           => 1
    //                 ]
    //             );
    //         } elseif (LectureVideo::find($lecture_video_id) && ($doctor_class_view->end == null)) {
    //             DoctorClassView::where([
    //                 'doctor_course_id' => $doctor_course_id,
    //                 'lecture_video_id' => $lecture_video_id,
    //             ])->update(
    //                 [
    //                     'end'              => date('Y-m-d H:i:s', strtotime('+15 days')),
    //                     'status'           => 1
    //                 ]
    //             );
    //         }
    //     }
    // }


    public static $_doctor_course_id = null;

    function doctor_exam()
    {
        return  $this->hasOne(\App\DoctorExam::class, 'exam_id', 'class_or_exam_id')
            ->where('doctor_exams.doctor_course_id', self::$_doctor_course_id);
    }

    public function classLink($back_link = '')
    {
        $data = [];


        $data['disabled']  = $link_disabled = false;

        if ($this->parent_id > 0) {
            $dt = $this->time_slot->datetime ?? null;
            $data['disabled'] = $link_disabled = $dt ? $dt->gt(date('Y-m-d H:i:s')) : false;
        }


        $link_disabled = $this->is_link_disabled();

        if (!$link_disabled) {
            $video = $this->video ?? null;
            $link_disabled = ($video && !$video->lecture_address);
        }

        $data['disabled'] = $link_disabled;
        $data['url'] =  $link_disabled ? 'javascript:void(0)' : url('/doctor-course-class/' . $this->class_or_exam_id . '/' . self::$_doctor_course_id
            . ($back_link ? '?back=' . urlencode($back_link) : ''));
        $data['mod_url'] =  url('/doctor-course-class/' . $this->class_or_exam_id . '/' . self::$_doctor_course_id
            . ($back_link ? '?back=' . urlencode($back_link) : ''));
        return $data;
    }

    public function examLink($back_link = '')
    {
        $doctor_exam = $this->doctor_exam;

        $exam_completed = ($doctor_exam->status ?? '') == 'Completed';
        $label = $exam_completed ? "Exam Result" : "Enter Exam";

        $path = $exam_completed ? '/course-exam-result/' : '/doctor-course-exam/';

        $disabled = $this->is_link_disabled();
        $url = $disabled ? 'javascript:void(0)' : url($path . self::$_doctor_course_id . '/' . ($this->exam->id ?? '')  . '/' . ($this->time_slot->schedule_id ?? 0) . ($back_link ? '?back=' . urlencode($back_link) : ''));

        return ['url' => $url, 'disabled' => $disabled, 'label' => $label, 'completed' => $exam_completed];
    }

    public function is_link_disabled()
    {

        $datatime =  $this->time_slot->datetime ?? null;

        if ($datatime) {
            return $datatime->gt(date('Y-m-d H:i:s'));
        }
        return false;
    }

    public function feedback_disabled()
    {
        if ($this->type == "Exam") {
            $f_or_s_class = $this->lectures[0] ?? null;

            if ($f_or_s_class) {
                if ($f_or_s_class->doctor_class_view == null) {
                    return true;
                }
            }

            return false;
        }

        if ($this->doctor_class_view == null)
            return true;

        return  false;
    }

    public function feedback_or_solve_class_disabled()
    {

        $f_or_s_class = $this->lectures[0] ?? new \App\ScheduleDetail();

        if ($f_or_s_class->video && !$f_or_s_class->video->lecture_address)
            return true;

        if ($f_or_s_class->id == null)
            return true;

        if ($this->doctor_class_view) {
            if ($this->type == "Class") {
                return $this->doctor_class_view->getAllRatings()->count() == 0;
            }
        } else {
            if ($this->exam) {
                //                dd( ( $this->exam->status ?? '' ) );
                return ($this->doctor_exam->status ?? '') != 'Completed';
            }
        }

        return true;
    }
}
