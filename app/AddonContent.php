<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddonContent extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function addon_service()
    {
        return $this->belongsTo(AddonService::class);
    }

    public function contentable()
    {
        return $this->morphTo('contentable');
    }
}
