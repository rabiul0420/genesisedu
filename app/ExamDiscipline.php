<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamDiscipline extends Model
{
    //protected $table = 'exam_links';
    protected $table = 'exam_discipline';
    public $timestamps = false;
    use SoftDeletes;

    public function discipline()
    {
        //return $this->belongsToMany('App\Exam');
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function exam()
    {
        //return $this->belongsToMany('App\Exam');
        return $this->belongsTo('App\Exam','online_exam_id','id');
    }    
    
}
