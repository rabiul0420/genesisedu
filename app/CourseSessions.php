<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CourseSessions extends Model
{
    protected $table = 'course_session';
    use SoftDeletes;

    public $timestamps = false;

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

    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id','id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function online_exam_links()
    {
        return $this->hasMany('App\OnlineExamLink','subject_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
