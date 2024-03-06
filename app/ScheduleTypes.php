<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleTypes extends Model
{
    protected $table = 'schedule_types';

    public function batches_schedules()
    {
        return $this->belongsTo('App\BatchesSchedules','schedule_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
