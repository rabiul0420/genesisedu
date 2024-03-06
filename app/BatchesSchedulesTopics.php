<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchesSchedulesTopics extends Model
{
    protected $table = 'batches_schedules_topics';

    public function schedule()
    {
        return $this->belongsTo('App\BatchesSchedulesLecturesExams','schedule_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
