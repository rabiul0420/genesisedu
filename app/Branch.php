<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    public function batches()
    {
        return $this->hasMany('App\Batches','branch_id', 'id');
    }

    public function lecture_sheet_link()
    {
        return $this->hasMany('App\LectureSheetArticleBatch','branch_id', 'id');
    }

    public function doctor_courses()
    {
        return $this->hasMany('App\DoctorCourses','branch_id', 'id');
    }
    
}
