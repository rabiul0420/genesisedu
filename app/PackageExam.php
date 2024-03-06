<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageExam extends Model
{
    //protected $table = 'lecture_video_links';
    protected $table = 'bm_package_exams';

    public function exam()
    {
        //return $this->belongsToMany('App\LectureVideo');
        return $this->belongsTo('App\Exam','exam_id','id');
    }

    public function package()
    {
        //return $this->belongsToMany('App\LectureVideo');
        return $this->belongsTo('App\Package','package_id','id');
    }    
    
}
