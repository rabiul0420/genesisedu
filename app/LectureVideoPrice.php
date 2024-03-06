<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureVideoPrice extends Model
{
    use SoftDeletes;
    
    protected $table = 'lecture_video_price';

    // protected $dates = [
    //     'active_from',
    // ];

    public function lecture_video()
    {
        return $this->belongsTo('App\LectureVideo','lecture_video_id','id');
    }

}
