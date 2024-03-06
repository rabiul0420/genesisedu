<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
    protected $table = 'answers';

    public function question()
    {
        return $this->belongsTo('App\Questions','question_id');
    }

}
