<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchPaymentOptions extends Model
{
    //
    public $timestamps = null;
    protected $table = 'batch_payment_options';
    use SoftDeletes;

}
