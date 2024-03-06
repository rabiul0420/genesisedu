<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionTopic extends Model
{
    use SoftDeletes;
    protected $table = 'ques_topics';

    public function subject()
    {
        return $this->belongsTo('App\QuestionSubject','subject_id','id');
    }

    public function chapter()
    {
        return $this->belongsTo('App\QuestionChapter','chapter_id','id');
    }
    public function questions()
    {
        return $this->hasMany('App\Question','topic_id','id');
    }
}
