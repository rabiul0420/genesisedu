<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorNotices extends Model
{
    use  SoftDeletes;
    protected $table = 'doctor_notice';
    public $timestamps = false;

    public function doctorname()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }

    
    
}
