<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionChapter extends Model
{
    use SoftDeletes;

    protected $table = 'ques_chapters';

    public function subject()
    {
        return $this->belongsTo('App\QuestionSubject','subject_id','id');
    }

    public function topics()
    {
        return $this->hasMany('App\Topics','chapter_id','id');
    }
}
