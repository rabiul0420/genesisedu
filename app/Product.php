<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public function coupon_code()
    {
        return $this->belongsTo('App\SoftCopyPermissions','book_id','id');
    }

    public function subjects()
    {
        return $this->hasMany('App\Subjects','book_id','id');
    }

    public function discount()
    {
        return $this->belongsTo('App\Discount','discount_id','id');
    }

    public function categories(){
        return $this->belongsToMany('App\Category')->whereNull('deleted_at');
    }
}
