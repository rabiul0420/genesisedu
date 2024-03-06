<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchesSchedulesSlotTypes extends Model
{
    protected $table = 'batches_schedules_slot_types';

    public function batches_schedules()
    {
        return $this->belongsTo('App\BatchesSchedules','schedule_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
