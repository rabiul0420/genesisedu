<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LectureSheetTopicLectureSheet extends Model
{

    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'lecture_sheet_topic_lecture_sheet';

    public function lecture_sheet_topics()
    {
        return $this->hasMany('App\LectureSheet','lecture_sheet_topic_id','id');
    }

    public function lecture_sheet()
    {
        return $this->belongsTo('App\LectureSheet','lecture_sheet_id','id');
    }

    //public $timestamps = false;
    
}
