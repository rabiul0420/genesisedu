<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photos extends Model
{
    protected $table = 'photos';

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
