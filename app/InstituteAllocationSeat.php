<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstituteAllocationSeat extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function instituteAllocation(){
        return $this->belongsTo(InstituteAllocation::class);
    }

    public function instituteDiscipline(){
        return $this->belongsTo(InstituteDiscipline::class);
    }

    public function course( ){
        return $this->belongsTo(Courses::class, 'allocation_course_id', 'id' );
    }
}
