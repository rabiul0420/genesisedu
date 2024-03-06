<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlineExamCommonCode extends Model
{
    protected $table = 'online_exam_common_codes';

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

}
