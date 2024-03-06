<?php

namespace App;

use App\Http\Controllers\ExamController;
use App\Http\Traits\ManagesQuestions;
use Illuminate\Database\Eloquent\Model;

class ExamEdit extends Model
{
    use ManagesQuestions;

    protected $table = 'exam as d1';
    protected static $mcqNgMarkRange = 'undefined';
    protected static $sbaNgMarkRange = 'undefined';


    function mcq_has_negative_mark( $question_index ){
        if( self::$mcqNgMarkRange === 'undefined' ) {
            self::$mcqNgMarkRange = ( $this->question_type->mcq_negative_mark_range ?? NULL );
        }

        return $this->_question_has_negative_mark( self::$mcqNgMarkRange, $question_index );
    }

    function sba_has_negative_mark( $question_index ){
        if( self::$sbaNgMarkRange === 'undefined' ) {
            self::$sbaNgMarkRange = ( $this->question_type->sba_negative_mark_range ?? NULL );
        }

        return $this->_question_has_negative_mark( self::$sbaNgMarkRange, $question_index );
    }

    protected   $correct_mark = 0, $negative_mark = 0, $wrong_answer_count = 0,
                $obtained_mark, $obtained_mark_percent;
    public  $doctor_course,
        $doctor_exam;

    public function getCorrectMark(){
        return $this->correct_mark;
    }

    public function getNegativeMark(){
        return $this->negative_mark;
    }

    public function getWrongAnswerCount(){
        return $this->wrong_answer_count;
    }

    public function getObtainedMark(){
        return $this->obtained_mark;
    }

    public function getObtainedMarkPercent(){
        return $this->obtained_mark_percent;
    }

    public function prepare_result( $doctor_course_id  ){

        $total_mark = QuestionTypes::where( 'id', $this->question_type_id )->value( 'full_mark' );
        $this->doctor_course = DoctorsCourses::where( 'id', $doctor_course_id )->first( );

        if( !$this->doctor_course ) return $this;

        $this->correct_mark = 0;

        $data = [];

        $this->negative_mark = 0;
        $this->wrong_answer_count = 0;
        $this->obtained_mark = 0;
        $given_answers = [];

        $this->doctor_exam = DoctorExam::where( [ 'exam_id' => $this->id, 'doctor_course_id' => $doctor_course_id ])->first();

        if( !$this->doctor_exam ) return $this;

        if( $this->doctor_exam instanceof DoctorExam) {
            $given_answers = ExamController::get_exam_answers( $this->doctor_exam, $file );
        }

        $ind = 0;
        $indexes = [ 1 => 0, 2 => 0, 3 => 0, 4=>0 ];

        foreach ($given_answers as $question_id => $doctor_answer)
        {
            $item =   [ 'obtained' => 0, 'negative' => 0 ];
            $answer = $doctor_answer['answer'] ?? '';
            $question = ExamController::question_data( $this->id, $question_id );
            $question_answers = $question[ 'question_option' ] ?? [ ];
            $qt = $doctor_answer[ 'question_type' ] ?? -1;;
            $question_type = Exam_question::getQuestionType( $doctor_answer[ 'question_type' ] ?? -1 );

            if( $qt == 1 || $qt == 3 )
            {

                $indexes[$qt]++;
                $mcq_mark = self::get_question_mark( $this->id, $qt, $indexes[$qt], $mcq_negative_mark );
                $item[ 'mcq_mark' ] =  $mcq_mark;
                $item[ 'qt' ] =  $qt;

                foreach( $question_answers as $index => $question_answer ) {
                    if( substr( $answer, $index,1 ) == $question_answer[ 'correct_ans' ] ){
                        $this->correct_mark += $mcq_mark;
                        $item['obtained'] += $mcq_mark;
                    } else if( substr( $answer, $index,1) != "." ) {
                        $item['negative'] += $mcq_negative_mark;
                        $this->negative_mark += $mcq_negative_mark;
                        $this->wrong_answer_count++;
                    }
                }
            } else if( $qt == 2 || $qt == 4 )  {
                $question_answer_sba = $question[ 'correct_ans_sba' ] ?? '';
                $indexes[$qt]++;

                $sba_mark = Exam::get_question_mark( $this->id, $qt, $indexes[$qt], $sba_negative_mark );

                if( $answer == $question_answer_sba ){
                    $this->correct_mark += $sba_mark;
                    $item['obtained'] = $sba_mark;
                } else if($answer != ".") {
                    $item['negative'] += $sba_negative_mark;
                    $this->negative_mark += $sba_negative_mark;
                    $this->wrong_answer_count++;
                }
            }

            $ind++;
            $data[ $ind ] = $item;
        }


        $this->obtained_mark = $this->correct_mark - $this->negative_mark;
        $this->obtained_mark_percent = (($this->obtained_mark*100) / $total_mark);

        //echo 'obtained = '.$this->obtained_mark.' , correct_mark = '.$this->correct_mark.' negative_mark = '.$this->negative_mark;

        return $this;
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

    public function faculty_subject()
    {
        return $this->belongsTo('App\Subjects','subject_id', 'id');
    }

    public function sessions()
    {
        return $this->belongsTo('App\Sessions','session_id', 'id');
    }

    public function question_type()
    {
        return $this->belongsTo('App\QuestionTypes','question_type_id', 'id');
    }

    public function exam_questions()
    {
        return $this->hasMany('App\Exam_question','exam_id', 'id');
    }

    public function results()
    {
        return $this->hasMany('App\Result','exam_id', 'id');
    }

    
    public function doctor_exams()
    {
        return $this->hasMany( DoctorExam::class, 'exam_id', 'id');
    }

    public function exam_topic()
    {
        return $this->hasOne(Exam_topic::class);
    }
    public function topic()
    {
        return $this->belongsTo('App\Topics','class_id', 'id');
    }
    public function class_topic()
    {
        return $this->belongsTo('App\Topics','doctor_class_rating', 'id');
    }
}
