<?php

namespace App;

use App\Http\Traits\ManageHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchesSchedules extends Model
{
    use SoftDeletes, ManageHistory;
    protected $table = 'batches_schedules';

    public static function has_back_url( ){
        return request()->session()->has('__schedule_back_link' );
    }

    public static function back_url( ){
        return request()->session()->get('__schedule_back_link' );
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id','id');
    }

    public function service_packages()
    {
        return $this->belongsTo('App\ServicePackages','service_package_id','id');
    }

    public function coming_by()
    {
        return $this->belongsTo('App\ComingBy','coming_by_id','id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id','id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id','id');
    }

    public function subject()
    {
        return $this->belongsTo( Subjects::class,'subject_id','id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id','id');
    }

    public function room()
    {
        return $this->belongsTo('App\RoomsTypes','room_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }


    public function time_slots()
    {
        return $this->hasMany('App\ScheduleTimeSlot','schedule_id','id');
    }

    public function slots()
    {
        return $this->hasMany('App\BatchesSchedulesTimeSlots','schedule_id','id');
    }

    public function meta()
    {
        return $this->hasMany('App\BatchesSchedulesMeta','schedule_id','id');
    }

}
