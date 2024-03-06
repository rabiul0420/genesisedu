<?php


namespace App\Http\Traits;


use App\Exam;
use App\QuestionTypes;

trait ManagesQuestions
{

    protected static $types = [ 1=>1, 2=>2, 3=>1 ];
    protected static $_question_type = null;

    public static function get_question_mark( $exam_id, $question_type, $index, &$negative_mark = 0 ){
        self::set_question_type( $exam_id );
        return self::_get_mark( $exam_id, $question_type, $index, $negative_mark );
    }

    protected static function set_question_type( $exam_id ) {
        if ( self::$_question_type === null ) {
            $type_id = Exam::where('id', $exam_id )->value('question_type_id');
            self::$_question_type = QuestionTypes::where('id',  $type_id )->first( );
        }
    }

    public function get_mark( $index, &$negative_mark = 0 ){
        if( isset( $this->exam_id ) &&  isset( $this->question_type ) ) {
            return self::_get_mark( $this->exam_id, $this->question_type, $index, $negative_mark );
        }
        return 0;
    }

    public static function getQuestionType( $type ){
        return self::$types[$type] ?? null;
    }

    public static function _get_mark( $exam_id, $question_type, $index, &$negative_mark = 0 ){
        if ( self::$_question_type === null || ( isset( self::$_question_type ) && self::$_question_type->exam_id != $exam_id ) ) {
            $type_id = Exam::where('id', $exam_id )->value('question_type_id');
            self::$_question_type = QuestionTypes::where('id', $type_id )->first();
        }

        switch ( $question_type ) {
            case 1:
                $negative_mark = self::_has_negative_mark( self::$_question_type->mcq_negative_mark_range, $index  ) ?
                    self::$_question_type->mcq_negative_mark:0;
                return self::$_question_type->mcq_mark/5;
            case 2:
                $negative_mark = self::_has_negative_mark( self::$_question_type->sba_negative_mark_range, $index ) ?
                    self::$_question_type->sba_negative_mark:0;
                return self::$_question_type->sba_mark;
            case 3:
                $negative_mark = self::_has_negative_mark( self::$_question_type->mcq2_negative_mark_range, $index ) ?
                    self::$_question_type->mcq2_negative_mark:0;
                return self::$_question_type->mcq2_mark/5;
        }
    }


    public  function getType(  ){
        if( isset($this->question_type) ) {
            return  self::$types[ $this->question_type ] ?? null;
        }
        return null;
    }

    protected function _question_has_negative_mark( $ranges, $question_index ){
        return self::_has_negative_mark( $ranges, $question_index );
    }

    protected static function _has_negative_mark( $ranges, $question_index ){
        $ranges = trim( strip_tags( $ranges ) );
        if( empty( $ranges ) )
            return true;

        $ranges = explode(',', $ranges );


        $numbers = [];

        foreach ( $ranges as $ind ) {

            if ( preg_match( '/^[0-9]+\-[0-9]+$/', $ind ) ) {

                $start = (int)substr($ind, 0, strpos($ind, '-'));
                $end = (int)substr($ind, strpos($ind, '-') + 1);

                if( $start <= $question_index && $question_index <= $end ) {
                    return true;
                }

            } else {
                if ( $question_index == $ind) {
                    return true;
                }
            }
        }

        return false;
    }
}
