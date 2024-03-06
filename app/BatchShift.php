<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchShift extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    
    protected $guarded = [];
    
    protected $casts = [
        'histories'     => 'array',
        'shifted_at'    => 'datetime',
    ];

    public function from_doctor_course()
    {
        return $this->belongsTo(DoctorsCourses::class, 'from_doctor_course_id');
    }

    public function to_doctor_course()
    {
        return $this->belongsTo(DoctorsCourses::class, 'to_doctor_course_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'shifted_by');
    }
}
