<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SbaQuestionEditHistory extends Model
{
    protected $table = 'sba_question_edit_history';


    public function user(){
        return $this->belongsTo(User::class, 'updated_by','id');
    }
}