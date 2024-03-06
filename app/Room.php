<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    protected $table = 'room';
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

    public function location()
    {
        return $this->belongsTo('App\Location','location_id','id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branches','branch_id','id');
    }

    public function slots()
    {
        return $this->hasMany('App\RoomSlot','room_id','id');
    }

}
