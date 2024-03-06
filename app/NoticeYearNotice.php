<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticeYearNotice extends Model
{

    use  SoftDeletes;

    protected $table = 'notice_year_notice';

    public function notice()
    {
        return $this->belongsTo('App\Notice','notice_id','id');
    }

    public function year_notice()
    {
        return $this->belongsTo('App\NoticeYear','notice_year_id','id');
    }

    public $timestamps = false;

    
}
