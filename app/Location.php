<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $table = "location";
    public $timestamps = false;
    
    public function branch()
    {
        return $this->belongsTo('App\Branches','branch_id', 'id');
    }

    public function rooms()
    {
        return $this->hasMany('App\Room','location_id', 'id');
    }
}
  