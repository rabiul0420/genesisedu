<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogHistory extends Model
{
    protected $guarded = [];
    protected $table = 'log_histories';

    public function getPlainDetailsAttribute()
    {
        return json_decode($this->details, 1);
    }

    public function loghistory(){
        return $this->morphTo();
    }

    public function mcq_ques(){
        return $this->morphTo(__FUNCTION__, 'loghistory_type', 'loghistory_id')
        ->whereHas('log',function($query){
            $query->where('loghistory_type','App\Question');
        });
    }

    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
