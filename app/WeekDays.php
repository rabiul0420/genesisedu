<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeekDays extends Model
{
    protected $table = 'week_days';

    public function batches_schedules()
    {
        return $this->belongsTo('App\BatchesSchedules','schedule_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
