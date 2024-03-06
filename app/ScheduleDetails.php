<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleDetails extends Model
{
    protected $table = 'schedule_details';

    public function slot()
    {
        return $this->belongsTo('App\ScheduleTimeSlot','slot_id','id');
    }

    public function video()
    {
        return $this->belongsTo('App\LectureVideo','class_or_exam_id','id');
    }

    public function class()
    {
        return $this->belongsTo('App\Topics','class_or_exam_id','id');
    }

    public function mentor()
    {
        return $this->belongsTo('App\Teacher','mentor_id','id');
    }

    public function exam()
    {
        return $this->belongsTo('App\Exam','class_or_exam_id','id');
    }

    public function solve_class()
    {
        return $this->belongsTo('App\LectureVideo','class_or_exam_id','id');
    }



}
