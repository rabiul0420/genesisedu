<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class LectureSheet extends Model
{

    use SoftDeletes;
    //protected $table = 'lecture_sheets';
    protected $table = 'lecture_sheet';

    public function lecture_sheet_links()
    {
        return $this->belongsToMany('App\LectureSheetArticleBatch',['year','session_id','institute_id','course_id','topic_id'], ['year','session_id','institute_id','course_id','topic_id']);
    }

    public function lecture_sheet_topic()
    {
        return $this->belongsTo('App\LectureSheetTopic','lecture_sheet_topic_id', 'id');
    }


    public function lecture_sheet_topics(){
        return $this->belongsToMany(LectureSheetTopic::class,'lecture_sheet_topic_lecture_sheet','lecture_sheet_id','lecture_sheet_topic_id' )
            ->whereNull('lecture_sheet_topic_lecture_sheet.deleted_at');
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

    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id', 'id');
    }

    public function faculty_subject()
    {
        return $this->belongsTo('App\Subject','subject_id', 'id');
    }


    public static $deliveredCourseId = null;

    public function doctor_delivered( ){

        $relation = $this->hasOne( DoctorCourseLectureSheet::class, 'lecture_sheet_id' )
                        ->join('doctors_courses as dc', 'dc.id', 'doctor_course_lecture_sheet.doctor_course_id' );

        if( self::$deliveredCourseId ) {
            return $relation->where('dc.id', self::$deliveredCourseId );
        }

        return $relation->where('dc.doctor_id', Auth::id() );

    }

    public function doctor_delivered_list( ){
        return $this->hasMany( DoctorCourseLectureSheet::class, 'lecture_sheet_id' );
    }
}
