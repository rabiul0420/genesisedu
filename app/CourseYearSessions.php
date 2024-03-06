<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseYearSessions extends Model
{
    use SoftDeletes;
    protected $table = 'course_year_session';
    public $timestamps = false;
   
    public function session(){
        return $this->belongsTo('App\Sessions','session_id','id')->where('show_admission_form','yes');
    }
}
