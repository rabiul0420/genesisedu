<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureSheetTopicFaculty extends Model
{

    use SoftDeletes;
    public $timestamps = null;

    protected $table = 'lecture_sheet_topic_faculty';

    public function lecture_sheet_topic()
    {
        return $this->belongsTo('App\LectureSheetTopic','lecture_sheet_topic_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id','id');
    }

}
