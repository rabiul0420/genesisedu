<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureVideoBatch extends Model
{
    //protected $table = 'lecture_video_links';

    use SoftDeletes;

    protected $table = 'lecture_video_batch';
    
    public function topics()
    {
        //return $this->belongsToMany('App\LectureVideo');
        return $this->hasMany('App\LectureVideoTopics','lecture_video_batch_id','id');
    }

    public function lecture_videos()
    {
        //return $this->belongsToMany('App\LectureVideo');
        return $this->hasMany('App\LectureVideoBatchLectureVideo','lecture_video_batch_id','id');
    }
    
    public function batch_lecture_videos()
    {
        //return $this->belongsToMany('App\LectureVideo');
        return $this->hasMany('App\LectureVideoBatchPost','lecture_video_batch_id','id');
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id', 'id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id', 'id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id', 'id');
    }

    public function faculty_subject()
    {
        return $this->belongsTo('App\Subject','subject_id', 'id');
    }
}
