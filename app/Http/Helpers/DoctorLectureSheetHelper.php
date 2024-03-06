<?php

namespace App\Http\Helpers;

use App\Courier;
use App\DoctorsCourses;
use App\Faculty;
use App\LectureSheetDeliveryStatus;
use App\LectureSheetTopic;
use App\LectureSheetTopicBatch;
use App\Providers\AppServiceProvider;
use App\Subjects;

class DoctorLectureSheetHelper
{


    static function lectureSheetTopics( DoctorsCourses $doctor_course, $lecture_sheet_topic_batch_id ){

        $lecture_sheet_topics = LectureSheetTopic::query()
            ->select('lecture_sheet_topic.*')
            ->join( 'lecture_sheet_topic_batch_lecture_sheet_topic as lstblst', 'lstblst.lecture_sheet_topic_id', 'lecture_sheet_topic.id' )
            ->join( 'lecture_sheet_topic_batch as lstb', 'lstblst.lecture_sheet_topic_batch_id', 'lstb.id' )
            ->where( 'lstb.id', $lecture_sheet_topic_batch_id )
            ->whereNull( 'lstblst.deleted_at' )
            ->whereNull( 'lstb.deleted_at' ) ;

        $facultyExistance = null;
        $disciplineExistance = null;

        $getFacultyIds = function () use ( $doctor_course ) {
            $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value( 'name' );
            return Faculty::where('name',$faculty_name)->select('id');
        };

        $getSubjectIds = function ( $subject_id = 'subject_id' ) use ( $doctor_course ) {
            $subject_name = Subjects::where('id', $doctor_course->{$subject_id} )->value('name');
            return Subjects::where('name', $subject_name)->select('id')->whereNull('subjects.deleted_at');
        };

        $facultyJoin = function( &$query, $returnFacultyIdsWhere = false ) use ( $doctor_course ) {
            $query->leftJoin( 'lecture_sheet_topic_faculty as lstf', 'lstf.lecture_sheet_topic_assign_id', 'lsta.id' )
                ->whereNull('lstf.deleted_at');
        };

        $disciplineJoin = function( &$query, $where = 'where' ) use ( $doctor_course ) {
            $query->leftJoin( 'lecture_sheet_topic_discipline as lstd', 'lstd.lecture_sheet_topic_assign_id', 'lsta.id' )
                ->whereNull('lstd.deleted_at');
        };

        if ( $doctor_course->batch->fee_type == "Discipline_Or_Faculty" || $doctor_course->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {
            $lecture_sheet_topics->join( 'lecture_sheet_topic_assign as lsta', 'lsta.lecture_sheet_topic_id', 'lecture_sheet_topic.id' )
                ->whereNull('lsta.deleted_at');

            if ( $doctor_course->institute->type == 1 ) {
                $facultyExistance = true;
            } else {
                $disciplineExistance = true;
            }
        }

        if( $doctor_course->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {


            $facultyJoin( $lecture_sheet_topics );
            $disciplineJoin( $lecture_sheet_topics, 'orWhere' );

            $lecture_sheet_topics->where( function( $query ) use ( $getSubjectIds, $getFacultyIds ){
                $query->whereIn( 'lstf.faculty_id', $getFacultyIds() );
                $query->orWhereIn( 'lstd.subject_id', $getSubjectIds( 'bcps_subject_id' ) );
            });

        } else {
            if( $facultyExistance ) {
                $facultyJoin( $lecture_sheet_topics );
                $lecture_sheet_topics->whereIn( 'lstf.faculty_id', $getFacultyIds() );
            } else if( $disciplineExistance ) {

                $disciplineJoin( $lecture_sheet_topics );
                $lecture_sheet_topics->whereIn( 'lstd.subject_id', $getSubjectIds() );
            }
        }

        return $lecture_sheet_topics->groupBy('lecture_sheet_topic.id');
    }

}