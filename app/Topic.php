<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    protected $table = 'topic';

    use SoftDeletes, ScheduleDefs;

    public function lecture_sheets()
    {
        return $this->hasMany('App\TopicContent','content_id','id')->where(['content_type_id'=>array_search('Lecture Sheet', $this->module_content_types())]);
    }

    public function mentors()
    {
        $mentor_ids = TopicContent::where(['topic_id'=>$this->id,'content_type_id'=>$this->get_topic_content_type_id_from_name('Mentor')])->pluck('content_id')->toArray();
        return Teacher::whereIn('id',$mentor_ids)->get();
    }

    public function lecture_videos()
    {
        $lecture_video_ids = TopicContent::where(['topic_id'=>$this->id,'content_type_id'=>$this->get_topic_content_type_id_from_name('Lecture Video')])->pluck('content_id')->toArray();
        return LectureVideo::whereIn('id',$lecture_video_ids)->get();
    }

    public function exams()
    {
        $exam_ids = TopicContent::where(['topic_id'=>$this->id,'content_type_id'=>$this->get_topic_content_type_id_from_name('Exam')])->pluck('content_id')->toArray();
        return Exam::whereIn('id',$exam_ids)->get();
    }
}
