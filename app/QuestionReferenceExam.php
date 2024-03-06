<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionReferenceExam extends Model
{
    use SoftDeletes;

    protected $table = 'questions_references_exams';

    public function reference_questions()
    {
        return $this->hasMany('App\QuestionReference','reference_id', 'id');
    }

    public function institute()
    {
        return $this->belongsTo('App\ReferenceInstitute','institute_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\ReferenceCourse','course_id', 'id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\ReferenceFaculty','faculty_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo('App\ReferenceSubject','subject_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo('App\ReferenceSession','session_id', 'id');
    }

    public function exam_type()
    {
        return $this->belongsTo('App\Exam_type','exam_type_id', 'id');
    }


}
