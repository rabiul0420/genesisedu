<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorsReviews extends Model
{
    protected $table = 'doctors_reviews';

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }
}
