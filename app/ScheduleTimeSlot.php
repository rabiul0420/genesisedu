<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleTimeSlot extends Model
{

    use SoftDeletes;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [ 'datetime' => 'datetime' ];

    public function schedule(){
        return $this->belongsTo('App\BatchesSchedules' , 'schedule_id','id' );
    }

    public function schedule_details(){
        return $this->hasMany('App\ScheduleDetail' ,'slot_id', 'id');
    }


}
