<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchSchedules extends Model
{
    protected $table = 'batch_schedules';

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

    
}
