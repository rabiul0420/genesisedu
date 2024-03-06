<?php

namespace App;

use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Courses extends Model
{
    use SoftDeletes;
    protected $table = 'courses';

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInActive($query)
    {
        return $query->where('status', 0);
    }

    public function course_years( ){
        return $this->hasMany(CourseYear::class, 'course_id', 'id' );
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id','id');
    }

    public function registered( )
    {
        return $this->hasOne('App\DoctorsCourses','course_id','id' )->where('doctor_id', Auth::id() );
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
   

    public function sessions()
    {
        return $this->belongsToMany(Sessions::class, 'course_session', 'course_id', 'session_id');
    }

    public function course_sessions( )
    {
        return $this->hasMany(CourseSessions::class, 'course_id', 'id' )
            ->join('sessions', 'sessions.id', 'course_session.session_id' );
    }

    public function isCombined( ){
        return $this->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID;
    }

    public function combined_faculties( ){

        return Faculty::with('subjects')->where([
            'show_in_combined'=> 1,
            'institute_id'=> AppServiceProvider::$BSMMU_INSTITUTE_ID,
            'course_id'=> AppServiceProvider::$MPH_DIPLOMA_COURSE_ID]);

    }

    public function combined_disciplines( ){

        return Subjects::where([
            'show_in_combined'=> 1,
            'institute_id'=> AppServiceProvider::$BCPS_INSTITUTE_ID,
            'course_id'=> AppServiceProvider::$FCPSP1_COURSE_ID]);

    }


    public function site_setup()
    {
        return $this->hasOne(SiteSetup::class, 'course_id', 'id');
    }

}
