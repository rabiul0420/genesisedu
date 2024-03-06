<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseYear extends Model
{
    use SoftDeletes;
    protected $table = 'course_year';

    public function course(){
        return $this->belongsTo('App\Courses','course_id','id');
    }    

    public function course_year_session(){
        return $this->hasMany(CourseYearSession::class,'course_year_id','id');
    }
}
