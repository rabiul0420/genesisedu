<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendingLecture extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function request_lecture_videos()
    {
        return $this->hasMany(RequestLectureVideo::class);
    }
}
