<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    protected $table = 'questions';

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Chapters','chapter_id','id');
    }
    
    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id','id');
    }

    public function answers()
    {
        return $this->hasMany('App\Answers','question_id','id');
    }
    
    public function correct_ans()
    {
        return $this->hasMany('App\Question_ans','correct_ans','only for mcq , only T,F');
    }
    public function question_answers()
    {
        return $this->hasMany('App\Question_ans','question_id','id');
    }

}
