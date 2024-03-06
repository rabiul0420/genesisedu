<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureVideoFaculty extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    
    protected $table = 'lecture_video_faculties';

    public function lecture_video()
    {
        return $this->belongsTo('App\LectureVideo','lecture_video_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculties','faculty_id','id');
    }

}
