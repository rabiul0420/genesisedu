<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $table = 'faculties';

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id','id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }

    public function subjects()
    {
        return $this->hasMany('App\Subjects','faculty_id','id');
    }

    public function scopeActive( $query ){
        return $query->where('status', 1);
    }

    public $timestamps = false;

    
}
