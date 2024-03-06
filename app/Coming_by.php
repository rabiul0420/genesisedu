<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coming_by extends Model
{
    protected $table = 'coming_by';

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
