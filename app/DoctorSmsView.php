<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorSmsView extends Model
{
    use  SoftDeletes;
    public $timestamps = false;
    protected $table = 'doctor_sms_view';
}
