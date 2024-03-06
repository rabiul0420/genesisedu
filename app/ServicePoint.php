<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicePoint extends Model
{
    protected $table = 'branches';

    public $timestamps = false;

    public function user()

    {
        return $this->belongsTo('App\User','created_by','id');
    }


}

