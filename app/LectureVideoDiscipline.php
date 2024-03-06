<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureVideoDiscipline extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    //protected $table = 'lecture_video_links';
    protected $table = 'lecture_video_discipline';

    public function discipline()
    {
        //return $this->belongsToMany('App\LectureVideo');
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function lecture_video()
    {
        //return $this->belongsToMany('App\LectureVideo');
        return $this->belongsTo('App\LectureVideo','lecture_video_id','id');
    }    
    
}
