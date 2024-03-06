<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceFaculty extends Model
{
    use SoftDeletes;
    protected $table = 'reference_faculties';
    public $timestamps = false;


    public function course()
    {
        return $this->belongsTo('App\ReferenceCourse','course_id','id');
    }
    
}
