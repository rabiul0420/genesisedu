<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsDiscipline extends Model
{
    //protected $table = 'sms_links';
    use  SoftDeletes;
    public $timestamps = false;
    protected $table = 'sms_disciplines';

    public function discipline()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function sms()
    {
        return $this->belongsTo('App\Smss','sms_id','id');
    }    
    
}
