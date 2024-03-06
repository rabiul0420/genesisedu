<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OmrScriptOmrScriptProperty extends Model
{
    use SoftDeletes;
    protected $table = 'omr_script_omr_script_properties';

    public function omr_script()
    {
        return $this->belongsTo('App\OmrScript','omr_script_id','id');
    }
    
    public function omr_script_property()
    {
        return $this->belongsTo('App\OmrScriptProperty','omr_script_property_id','id');
    }

}
