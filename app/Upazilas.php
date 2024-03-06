<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upazilas extends Model
{
    public $timestamps = false;
    
    protected $table = 'upazilas';

    public function district()
    {
        return $this->belongsTo('App\Districts','district_id', 'id');
    }
}
