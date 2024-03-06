<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchAddonService extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function addon_service()
    {
        return $this->belongsTo(AddonService::class, 'addon_service_id');
    }

}
