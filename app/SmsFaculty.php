<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsFaculty extends Model
{
    use  SoftDeletes;
    public $timestamps = false;
    protected $table = 'sms_faculties';

    public function sms()
    {
        return $this->belongsTo('App\Smss','sms_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculties','faculty_id','id');
    }

}
