<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $table = "schedule";
    public $timestamps = false;
    
    public function module()
    {
        return $this->belongsTo('App\Module','module_id', 'id');
    }
}
  