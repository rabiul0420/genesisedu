<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionOrder extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'is_paid',
    ];

    public function getPaymentPageLinkAttribute()
    {
        return route('subscribers.orders.show', [$this->doctor_id, $this->id]);
    }

    public function getIsPaidAttribute()
    {
        return (boolean) ($this->payment_status);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctors::class, 'doctor_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionOrderPayment::class);
    }

    public function manual_payments()
    {
        return $this->morphMany(ManualPayment::class, 'paymentable');
    }
}
