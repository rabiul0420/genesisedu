<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizQuestion extends Model
{
    use SoftDeletes;

    protected $appends = [
        // 'pin',
    ];
    
    protected $guarded = [];

    // public function getPinAttribute()
    // {
    //     return $this->serial ? 1 : 0;
    // }

    public function getQuestionOptionsAttribute($key)
    {
        return json_decode($key, 1);
    }

    public function scopePin($query)
    {
        return $query
            ->whereNotNull('serial')
            ->orderBy('serial');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
