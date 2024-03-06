<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorSpecialBatch extends Model
{
    protected $table = 'doctor_special_batches';
    public $timestamps = false;


    public function batch()
    {
        return $this->hasOne('App\Batches','id', 'batch_id');
    }

    public function course()
    {
        return $this->hasOne('App\Courses','id', 'course_id');
    }

    public function selected_batches( ){
        return $this->hasMany('App\DoctorGroupSelectedBatchId', 'group_id', 'id');
    }

}
