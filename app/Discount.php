<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model


{

    use SoftDeletes;

    /**
     * The attributes that should be hidden for arrays.
     *
      * @var array
     */

    protected $table = 'discounts';

    protected $guarded = [];
    
    public function batch(){
        return $this->belongsTo(Batches::class, 'batch_id');
    }
    
    public function discount_histories(){
        return $this->hasMany(DiscountHistory::class, 'id');
    }
    
    
}
