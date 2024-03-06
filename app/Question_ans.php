<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question_ans extends Model
{
    protected $table = 'question_ans';

    public $timestamps = false;

    protected $appends = [
        'serial',
        'title',
    ];

    public function getSerialAttribute()
    {
        return $this->sl_no ?? '';
    }

    public function getTitleAttribute()
    {
        return $this->answer ?? '';
    }
    
}
