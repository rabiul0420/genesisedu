<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    protected $table = 'questions';

    public function book()
    {
        return $this->belongsTo('App\Books','book_id','id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Chapters','chapter_id','id');
    }

    public function answers()
    {
        return $this->hasMany('App\Answers','question_id','id');
    }
}
