<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleType extends Model
{
    use SoftDeletes;

    protected $table = "schedule_type";
    public $timestamps = false;
    
}
  