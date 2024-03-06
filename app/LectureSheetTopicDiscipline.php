<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureSheetTopicDiscipline extends Model
{
    use SoftDeletes;
    public $timestamps = null;

    //protected $table = 'lecture_sheet_links';
    protected $table = 'lecture_sheet_topic_discipline';

    public function discipline()
    {
        //return $this->belongsToMany('App\LectureSheet');
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function lecture_sheet_topic()
    {
        //return $this->belongsToMany('App\LectureSheet');
        return $this->belongsTo('App\LectureSheetTopic','lecture_sheet_topic_id','id');
    }    
    
}
