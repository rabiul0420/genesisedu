<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountHistory extends Model


{
    public $timestamps = false;
    protected $table = 'discounts_history';
    public function discount(){
        return $this->belongsTo(Discount::class, 'discount_code_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}
    
