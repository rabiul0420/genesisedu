<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlineExamFaculty extends Model
{
    protected $table = 'online_exam_faculties';

    public function online_exam()
    {
        return $this->belongsTo('App\OnlineExam','online_exam_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculties','faculty_id','id');
    }

}
