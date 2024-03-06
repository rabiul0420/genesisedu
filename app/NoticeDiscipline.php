<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticeDiscipline extends Model
{
    //protected $table = 'notice_links';
    use  SoftDeletes;
    public $timestamps = false;
    protected $table = 'notice_disciplines';

    public function discipline()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function notice()
    {
        return $this->belongsTo('App\Notices','notice_id','id');
    }    
    
}
