<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceCourse extends Model
{
    use SoftDeletes;
    protected $table = 'reference_courses';
    public $timestamps = false;


    public function institute()
    {
        return $this->belongsTo('App\ReferenceInstitute','institute_id','id');
    }

}
