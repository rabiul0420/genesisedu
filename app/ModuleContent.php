<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleContent extends Model
{
    protected $table = 'module_content';

    use SoftDeletes, ScheduleDefs;

    public function module()
    {
        return $this->belongsTo('App\Module','module_id','id');
    }

    public function program()
    {
        return $this->belongsTo('App\Program','content_id','id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Batches','content_id','id');
    }

}
