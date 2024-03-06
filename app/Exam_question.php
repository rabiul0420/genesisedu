<?php

namespace App;

use App\Http\Traits\ManagesQuestions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_question extends Model
{
    use ManagesQuestions;
    protected $table = 'exam_question';
    use SoftDeletes;
    

    public function exam()
    {
        return $this->belongsTo('App\Exam', 'exam_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

}
