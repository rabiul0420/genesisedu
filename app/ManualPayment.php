<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualPayment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function paymentable()
    {
        return $this->morphTo();
    }

    public function doctor()
    {
        return $this->belongsTo(Doctors::class);
    }
}
