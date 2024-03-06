<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class McqQuestionEditHistory extends Model
{
    protected $table = 'mcq_question_edit_history';

    
    public function user(){
        return $this->belongsTo(User::class, 'updated_by','id');
    }
}
