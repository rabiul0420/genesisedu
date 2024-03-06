<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'question_labels')
            ->whereNull('question_labels.deleted_at');
    }
}
