<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceSubject extends Model
{
    use SoftDeletes;
    protected $table = 'reference_subjects';
    public $timestamps = false;

    public function course()
    {
        return $this->belongsTo('App\ReferenceCourse','course_id','id');
    }
    
}
