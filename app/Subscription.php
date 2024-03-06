<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    public function getSubscriptionableTypeAttribute($value)
    {
        return str_replace('Models\\', '', $value);
    }

    public function subscriptionable()
    {
        return $this->morphTo();
    }
}
