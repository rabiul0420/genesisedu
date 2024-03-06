<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformationAlumni extends Model
{
    protected $table = 'informationalumni';

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }
}
