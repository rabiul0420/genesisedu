<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Executive extends Model
{
    use SoftDeletes;
    protected $table = 'executives_stuffs';

    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
