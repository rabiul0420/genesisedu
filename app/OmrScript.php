<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OmrScript extends Model
{
    use SoftDeletes;
    protected $table = 'omr_scripts';

    public function properties()
    {
        return $this->hasMany(OmrScriptOmrScriptProperty::class,'omr_script_id','id');
    }
    
    public function answer()
    {
        return $this->hasMany('App\OmrScriptOmrScriptProperty','omr_script_id','id')->where('omr_script_property_id','1');
    }

}
