<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchesSchedulesMeta extends Model
{
    //
    public $timestamps = false;
    protected $table = 'batches_schedules_meta';

    public function schedule()
    {
        return $this->belongsTo('App\BatchesSchedules','schedule_id','id' );
    }


}
