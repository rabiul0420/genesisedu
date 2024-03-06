<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    protected $table = 'program';

    use SoftDeletes, ScheduleDefs;

    public function media_types()
    {
        $media_type_ids = ProgramContent::where('program_id',$this->id)->where(['content_type_id'=>$this->get_program_content_type_id_from_name('Program Media Type')])->pluck('content_id')->toArray();
        return ScheduleMediaType::whereIn('id',$media_type_ids)->pluck('name')->toArray();
    }

    public function topics()
    {
        $topic_ids = ProgramContent::where('program_id',$this->id)->where(['content_type_id'=>$this->get_program_content_type_id_from_name('Program Topic')])->pluck('content_id')->toArray();
        return Topic::whereIn('id',$topic_ids)->pluck('name')->toArray();
    }

    public function mentors()
    {
        $topic_mentor_ids = ProgramContent::where('program_id',$this->id)->where(['content_type_id'=>$this->get_program_content_type_id_from_name('Program Mentor')])->pluck('content_id')->toArray();
        $mentor_ids = TopicContent::whereIn('id',$topic_mentor_ids)->pluck('content_id')->toArray();
        return Teacher::whereIn('id',$mentor_ids)->get();
    }

    public function lecture_videos()
    {
        $topic_lecture_video_ids = ProgramContent::where('program_id',$this->id)->where(['content_type_id'=>$this->get_program_content_type_id_from_name('Program Lecture Video')])->pluck('content_id')->toArray();
        $lecture_video_ids = TopicContent::whereIn('id',$topic_lecture_video_ids)->pluck('content_id')->toArray();
        return LectureVideo::whereIn('id',$lecture_video_ids)->get();
    }

    public function exams()
    {
        $topic_exam_ids = ProgramContent::where('program_id',$this->id)->where(['content_type_id'=>$this->get_program_content_type_id_from_name('Program Exam')])->pluck('content_id')->toArray();
        $exam_ids = TopicContent::whereIn('id',$topic_exam_ids)->pluck('content_id')->toArray();
        return Exam::whereIn('id',$exam_ids)->get();
    }

    public function module_contents()
    {
        $exam_ids = ModuleContent::where(['content_type_id'=>'7'])->pluck('content_id')->toArray();
        return ModuleContent::whereIn('id',$exam_ids)->get();
    }

    public function lecture_sheets()
    {
        return $this->hasMany('App\ProgramContent','program_id','id')->where(['content_type_id'=>array_search('Lecture Sheet', $this->program_content_types())]);
    }

    public function program_type()
    {
        return $this->belongsTo('App\ScheduleProgramType','program_type_id','id');
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id','id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id','id');
    }

}
