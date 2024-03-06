<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model


{

    protected $table = 'lectures';

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }


}
