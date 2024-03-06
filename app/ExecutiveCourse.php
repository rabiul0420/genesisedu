<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExecutiveCourse extends Model
{
    use SoftDeletes;
    public function executive(){
        return $this->belongsTo('App\Executive','executive_id','id');
    }

    public function course(){
        return $this->belongsTo('App\Courses','course_id','id');
    }
}

