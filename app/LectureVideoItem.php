<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LectureVideoItem extends Model
{
    //
    public function lecture_video(){
        return $this->belongsTo( LectureVideo::class );
    }
}
