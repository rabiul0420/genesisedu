<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchesSchedulesTimeSlots extends Model
{
    use SoftDeletes;
    protected $table = 'schedule_time_slots';

    public function schedule()
    {
        return $this->belongsTo('App\BatchesSchedules','schedule_id','id');
    }

    public function details()
    {
        return $this->hasMany('App\ScheduleDetails' ,'slot_id', 'id');
    }
}
