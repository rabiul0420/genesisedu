<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionReferenceBook extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function reference_book()
    {
        return $this->belongsTo(ReferenceBook::class);
    }
    
}
