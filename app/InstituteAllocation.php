<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstituteAllocation extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function instituteAllocationSeat(){
        return $this->hasOne(InstituteAllocationSeat::class);
    }

    public function courses(){
        return $this->belongsToMany(Courses::class, 'institute_allocation_courses', 'allocation_id', 'course_id');
    }
}
