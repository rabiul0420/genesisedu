<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Result extends Model
{

    protected $table = 'results';
    use SoftDeletes;
    public $timestamps = false;

    protected $guarded = [];

    public function doctor_courses()
    {
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id', 'id');
    }

    public function doctor_course()
    {
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id','id');
    }

    public function exam()
    {
        return $this->belongsTo('App\Exam','exam_id','id');
    }

    public static function postion_suffix( $index ) {
        $suffixes = [ 'st', 'nd', 'rd' ];
        return $suffixes[$index] ?? 'th';
    }

    protected static $batchExamList = [];

    static $doctorPositions = [ 'batch' => [], 'subject' => [], 'overall' => [], 'candidate' => [] ];

    protected function getExamList(  ){
        if( !isset( self::$batchExamList[ $this->exam_id ] ) ) {
            self::$batchExamList[ $this->exam_id ] = self::select( '*' )->where( 'exam_id', $this->exam_id )->orderBy( 'obtained_mark', 'desc' )->get( );
        }
        return self::$batchExamList[ $this->exam_id ];
    }

    protected function getPosition( Collection $results, $reference ){

        $data =& self::$doctorPositions[ $reference ];
        $key = $this->exam_id.'-'.$this->doctor_course_id;

        if( $results->count() && !isset( $data[ $key ] ) ) {

            $obtained_mark = -50000;
            $possition = 0;
            $index = 0;

            foreach ( $results as $exam_result ){

                if ( $obtained_mark != $exam_result->obtained_mark ) {

                    $possition = $pos = ( $index + 1 ) . self::postion_suffix( $index );
                    $index++;
                } else {
                    $pos = $possition;
                }

                $obtained_mark = $exam_result->obtained_mark;
                $data[ $exam_result->exam_id.'-'.$exam_result->doctor_course_id ]= $pos;
            }
            //dd( $data , $key);
        }
        return  $data[ $key ] ?? null;
    }

    public function getBatchPosition(  ){
        //Batch wise Position
        $results = $this->getExamList( )->where( 'batch_id', $this->batch_id );
        return $this->getPosition( $results, 'batch' );
    }

    public function overallPosition(  ){
        //Overall Position
        $results = $this->getExamList();
        return $this->getPosition( $results, 'overall' );
    }

    public function getDisciplinePosition(  ){
        //Batch wise Position
        $results = $this->getExamList( )->where( 'subject_id', $this->subject_id );
        return $this->getPosition( $results, 'subject' );
    }

    static $candidateSubjects = [];
    static $candidateResults = [];
    static $subjectResults = [];
    static $resultDoctorCourses = [];

    function getCandidateResults( ){

        if( isset( self::$resultDoctorCourses[ $this->doctor_course_id ] ) === false ) {
            self::$resultDoctorCourses[ $this->doctor_course_id ] = DoctorsCourses::where( 'id', $this->doctor_course_id )->first( );
        }

        $doctor_course = self::$resultDoctorCourses[ $this->doctor_course_id ];
        $subject_id = $doctor_course->subject_id;
        $candidate_type = $doctor_course->candidate_type;


        if( isset(self::$candidateSubjects[$subject_id]) === false ) {
            self::$candidateSubjects[$subject_id] = Subjects::where( 'id', $subject_id )->value( 'name' );
        }

        $key = self::$candidateSubjects[$subject_id].'-'.$this->exam_id;

        if( isset( self::$candidateResults[ $key ] ) === false ) {
            $results = Result::with('doctor_course')
                ->join('subjects', 'subjects.id', '=', 'results.subject_id')
                ->where(['subjects.name' => self::$candidateSubjects[$subject_id],  'exam_id' => $this->exam_id])
                ->orderBy('obtained_mark', 'desc')->get();

            self::$candidateResults[ $key ] = $results->where('doctor_course.candidate_type', $candidate_type );
        }

        //dd( $key, self::$candidateResults[ $key ], $candidate_type );

        return self::$candidateResults[ $key ];
    }

    function getCandidatePosition( )
    {

        $candidate_results = $this->getCandidateResults( );

        return $this->getPosition( $candidate_results, 'candidate' );
    }



}
