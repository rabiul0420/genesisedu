<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapters extends Model
{
    protected $table = 'ques_chapters';

    public function book()
    {
        return $this->belongsTo('App\Books','book_id','id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id','id');
    }

    public function topics()
    {
        return $this->hasMany('App\Topics','chapter_id','id');
    }
}
