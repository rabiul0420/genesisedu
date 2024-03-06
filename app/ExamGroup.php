<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamGroup extends Model
{
    use SoftDeletes;

    //

    public function group( ){
        return $this->belongsTo( 'App\Group' ,'group_id', 'id' );
    }

    public function group_exams( ){
        return $this->hasMany('App\ExamGroupExam', 'exam_group_id', 'id' );
    }

    public function groups_of_doctors( ){
        return $this->hasMany( 'App\DoctorGroup' ,'group_id', 'group_id' );
    }

}
