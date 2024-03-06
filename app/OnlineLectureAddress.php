<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlineLectureAddress extends Model
{
    protected $table = 'online_lecture_addresses';

    public function lecture_links()
    {
        return $this->hasMany('App\OnlineLectureLink','lecture_address_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

}
