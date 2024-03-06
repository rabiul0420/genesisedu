<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticeCourseNotice extends Model
{

    use SoftDeletes;

    protected $table = 'notice_course_notice';

    public function notice()
    {
        return $this->belongsTo('App\Notice','notice_id','id');
    }

    public function course_notice()
    {
        return $this->belongsTo('App\NoticeCourse','notice_course_id','id');
    }

    public $timestamps = false;

    
}
