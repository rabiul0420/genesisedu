<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamFaculty extends Model
{
    protected $table = 'exam_faculties';
    public $timestamps = false;
    use SoftDeletes;


    public function exam()
    {
        return $this->belongsTo('App\Exam','online_exam_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculties','faculty_id','id');
    }

}
