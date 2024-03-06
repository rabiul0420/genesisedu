<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchesSchedulesLecturesExams extends Model
{

    use SoftDeletes;

    protected $table = 'batches_schedules_lecture_exam';

    public function batches_schedules()
    {
        return $this->belongsTo('App\BatchesSchedules','schedule_id','id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id','id');
    }

    public function slot()
    {
        return $this->belongsTo('App\BatchesSchedulesSlotTypes','slot_id','slot_type');
    }

    public function teacher()
    {
        return $this->belongsTo('App\Teachers','teacher_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
    
    
}
