<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionReference extends Model
{
    use SoftDeletes;

    protected $table = 'questions_references';

    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo('App\Question','question_id', 'id');
    }

    public function question_reference_exam()
    {
        return $this->belongsTo('App\QuestionReferenceExam','reference_id', 'id');
    }


}
