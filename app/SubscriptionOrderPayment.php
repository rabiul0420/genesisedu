<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionOrderPayment extends Model
{
    use SoftDeletes;

    protected $table = 'doctor_subscription_payment';

    protected $guarded = [];

    public function subscription_order()
    {
        return $this->belongsTo(SubscriptionOrder::class);
    }
}
