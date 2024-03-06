<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OmrScriptProperty extends Model
{
    use SoftDeletes;
    protected $table = 'omr_script_properties';

    public function omr_scripts()
    {
        return $this->hasMany('App\OmrScriptOmrScriptProperty','omr_script_property_id','id');
    }    

}
