<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendingVideo extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function batch()
    {
        return $this->belongsTo(Batches::class, 'batch_id');
    }

    public function video()
    {
        return $this->belongsTo(LectureVideo::class, 'video_id');
    }
}
