<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batches extends Model
{
    protected $table = 'batches';

    function scopeOnline($query){
        $query->where('branch_id', 4);
    }

    function scopeOffline($query){
        $query->where('branch_id', '!=', 4);
    }

    function scopeActive( $query ){
        $query->where( 'status', 1 );
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo(Sessions::class, 'session_id', 'id');
    }

    public function faculty()
    {
        return $this->belongsTo( Faculty::class,'faculty_id', 'id');
    }

    public function faculties()
    {
        return $this->belongsToMany(Faculty::class, 'batches_faculties', 'batch_id', 'faculty_id')
            ->whereNull('batches_faculties.deleted_at');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subjects::class, 'batches_subjects', 'batch_id', 'subject_id')
            ->whereNull('batches_subjects.deleted_at');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id', 'id');
    }

    public function faculty_fees()
    {
        return $this->hasMany( BatchFacultyFee::class,'batch_id', 'id' );
    }

    public function subject_fees()
    {
        return $this->hasMany( BatchDisciplineFee::class,'batch_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id', 'id');
    }
    public function CourierChargePackage()
    {
        return $this->belongsTo('App\CourierChargePackage','package_id', 'id');
    }

    public function master_schedule( ){

        return $this->hasOne( \App\BatchesSchedules::class, 'batch_id', 'id' )
            ->whereHas('time_slots', function ( $time_slot ){
                $time_slot->whereNull( 'deleted_at' );
            });
    }

    public function batch_schedule(){
        return $this->hasMany(BatchesSchedules::class, 'batch_id','id');
    }
    public function get_status(){
        return $this->status==1? "Active":"InActive";
    }

    public function doctors_courses()
    {
        return $this->hasMany('App\DoctorsCourses','batch_id', 'id');
    }

    public function doctor_courses()
    {
        return $this->hasMany(DoctorsCourses::class, 'batch_id');
    }

    public function payment_options()
    {
        return $this->hasMany('App\BatchPaymentOptions','batch_id', 'id');
    }

    public function courier_package_charge()
    {
        return $this->belongsTo('App\CourierChargePackage','package_id', 'id');
    }

    public function batch_addon_services()
    {
        return $this->hasMany(BatchAddonService::class, 'batch_id');
    }

    public function addon_services()
    {
        return $this->belongsToMany(AddonService::class, 'batch_addon_services', 'batch_id', 'addon_service_id');
    }

    public function pending_videos()
    {
        return $this->hasMany(PendingVideo::class, 'batch_id')->orderBy('priority');
    }

}
