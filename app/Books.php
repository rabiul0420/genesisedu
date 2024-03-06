<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    protected $table = 'books';

    public function coupon_code()
    {
        return $this->belongsTo('App\Coupon_book','book_id','id');
    }
}
