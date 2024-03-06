<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertisements extends Model
{
    protected $table = 'advertisements';

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
