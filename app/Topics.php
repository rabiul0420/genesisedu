<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topics extends Model
{
    use SoftDeletes;
    
    protected $table = 'topics';

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }
    public function institute()
    {
        return $this->belongsTo(Institutes::class,'institute_id','id');
    }

     public function session()
    {
        return $this->belongsTo(Sessions::class,'session_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

    public function lectures( ){
        return $this->hasMany('App\LectureVideo', 'class_id','id');
    }

    public function exams( ){
        return $this->hasMany('App\Exam', 'class_id','id');
    }

    public function faculties( ){
        return $this->hasMany(TopicFaculty::class, 'topic_id', 'id' )
            ->join('faculties','faculties.id','topic_faculties.faculty_id');
    }

    public function subjects( ){
        return $this->hasMany(TopicSubject::class, 'topic_id', 'id' )
            ->join('subjects','subjects.id','topic_subjects.subject_id')
            ->where('topic_subjects.combined_bcps', 0 );
    }

    public function bcps_subjects( ){
        return $this->hasMany(TopicSubject::class, 'topic_id', 'id' )
            ->join('subjects','subjects.id','topic_subjects.subject_id')
            ->where('topic_subjects.combined_bcps', 1 );
    }

    public function topics_ids(){
        return $this->hasMany(TopicTeachers::class,'topic_id','id')->where('deleted_by',NULL);
    }

    public function schedule_detaills()
    {
        return $this->hasMany(ScheduleDetails::class, 'class_or_exam_id');
    }

}
