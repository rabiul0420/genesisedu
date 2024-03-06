<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    protected $table = 'notice';
    use SoftDeletes;

    public function adminname()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

    public function sessionname()
    {
        return $this->belongsTo('App\Sessions','session_id','id');
    }

    public function institutename()
    {
        return $this->belongsTo('App\Institutes','institute_id','id');
    }

    public function coursename()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }

    public function batchname()
    {
        return $this->belongsTo('App\Batches','batch_id','id');
    }
    
    
}
