<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamGroupExam extends Model
{
    use SoftDeletes;
    //

    public function group() {
        return $this->belongsTo('App\ExamGroup', 'id', 'group_id' );
    }

}
