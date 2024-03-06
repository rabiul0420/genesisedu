<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopicTeachers extends Model
{
    protected $table = 'topic_teachers';

    public function teacher(){
        return $this->belongsTo('App\Teacher','teacher_id','id');
    }

}