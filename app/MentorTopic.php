<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorTopic extends Model
{
    use SoftDeletes;

    public $timestamps = NULL;

    protected $casts = [
        'access_upto' => 'date:Y-m-d',
    ];

    protected $guarded = [];

}
