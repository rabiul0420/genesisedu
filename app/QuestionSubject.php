<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionSubject extends Model
{
    use SoftDeletes;
    protected $table = 'ques_subjects';

    public function chapters()
    {
        return $this->hasMany('App\QuestionChapter','subject_id','id');
    }
    public function topics()
    {
        return $this->hasMany('App\QuestionTopic','subject_id','id');
    }
}
