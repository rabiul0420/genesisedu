<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticeBatchNotice extends Model
{
    use  SoftDeletes;
    public $timestamps = false;
    protected $table = 'notice_batch_notice';

    public function notice()
    {
        return $this->belongsTo('App\Notice','notice_id','id');
    }

    public function batch_notice()
    {
        return $this->belongsTo('App\NoticeBatch','notice_batch_id','id');
    }
    
}
