<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorPackage extends Model
{
    protected $table = 'bm_doctors_packages';

    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }
    
    public function package()
    {
        return $this->belongsTo('App\Package','package_id','id');
    }

}
