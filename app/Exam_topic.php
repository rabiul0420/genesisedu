<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam_topic extends Model
{
    protected $table = 'exam_topic';

    public function exam()
    {
        return $this->belongsTo('App\Exam', 'exam_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics', 'topic_id', 'id');
    }

}
