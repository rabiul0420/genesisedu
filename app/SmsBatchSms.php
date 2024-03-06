<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsBatchSms extends Model
{
    use  SoftDeletes;
    public $timestamps = false;
    protected $table = 'sms_batch_sms';

    public function sms()
    {
        return $this->belongsTo('App\Sms','sms_id','id');
    }

    public function sms_batch()
    {
        return $this->belongsTo('App\SmsBatch','sms_batch_id','id');
    }
    
}
