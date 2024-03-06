<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CourseYearSession extends Model
{
    use SoftDeletes;
    protected $table = 'course_year_session';

    public function session(){
        return $this->belongsTo('App\Sessions','session_id','id');
    }

}
