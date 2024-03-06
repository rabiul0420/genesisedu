<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lecture_video_batch_lecture_video extends Model
{
    protected $table = 'lecture_video_batch_lecture_video';

    public function lecture_video()
    {
        return $this->belongsTo('App\LectureVideo','lecture_video_id');
    }

}
