<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizPropertyItem extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'title',
    ];

    public function getTitleAttribute()
    {
        return $this->number_of_question . " " . Question::$question_type_array[$this->question_type];
    }
}
