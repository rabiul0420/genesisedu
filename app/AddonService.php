<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddonService extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function addon_contents()
    {
        return $this->hasMany(AddonContent::class)->orderBy('priority');
    }
}
