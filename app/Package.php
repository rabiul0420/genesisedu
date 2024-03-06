<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    //protected $table = 'lecture_videos';
    protected $table = 'bm_packages';
    
    public function exams()
    {
        return $this->hasMany('App\PackageExam','package_id','id');
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
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

}
