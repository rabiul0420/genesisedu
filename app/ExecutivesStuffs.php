<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExecutivesStuffs extends Model
{
    protected $table = 'executives_stuffs';

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
