<?php

namespace App;

use App\Http\Traits\ManagesQuestions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionTypes extends Model
{
    use SoftDeletes;
    use ManagesQuestions;

    protected $table = 'question_types';

    static $negativeMarkIndexes = [ ];

    function mcq_has_negative_mark( $question_index ){
        return $this->_question_has_negative_mark( $this->mcq_negative_mark_range, $question_index );
    }

    function sba_has_negative_mark( $question_index ){
        return $this->_question_has_negative_mark( $this->sba_negative_mark_range, $question_index );
    }




}
