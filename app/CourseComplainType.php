<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseComplainType extends Model
{
    // protected $table = 'course_complain_type';
    protected $table = 'complain_type_course';
    
    public function course_complain(){
        return $this->hasMany('App\Complain','course_complain_related_id','id');
    }

    public function course(){
        return $this->belongsTo('App\Courses','course_id','id');
    }
    public function complain_type(){
        return $this->belongsTo('App\ComplainRelated','complain_type_id','id');
    }
}
