<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlineExamDiscipline extends Model
{
    //protected $table = 'online_exam_links';
    protected $table = 'online_exam_discipline';

    public function discipline()
    {
        //return $this->belongsToMany('App\OnlineExam');
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function online_exam()
    {
        //return $this->belongsToMany('App\OnlineExam');
        return $this->belongsTo('App\OnlineExam','online_exam_id','id');
    }    
    
}
