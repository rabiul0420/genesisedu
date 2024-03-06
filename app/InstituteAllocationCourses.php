<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstituteAllocationCourses extends Model
{
    //
    use SoftDeletes;
    protected $table = 'institute_allocation_courses';
    public $timestamps = false;
    protected $guarded = [];
}
