<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicSubject extends Model
{
    //
    use SoftDeletes;
    public $timestamps = false;

    function scopeCombinedBcps( $query ){
        $query->where('combined_bcps', 1 );
    }

    function scopeMain( $query ){
        $query->where('combined_bcps', 0 );
    }
}
