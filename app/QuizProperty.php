<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizProperty extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        "pass_mark",
    ];

    public function getPassMarkAttribute()
    {
        return ($this->full_mark * $this->pass_mark_percent) / 100;
    }

    public function quiz_property_items()
    {
        return $this->hasMany(QuizPropertyItem::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }
}
