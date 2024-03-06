<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $appends = [
        'title',
        'path_url',
        'answer_script',
    ];

    public static $question_type_array = [
        1 => "MCQ",
        2 => "SBA",
    ];

    // QuestionType => Number Of Answers
    public static $stamp_of_question_array = [
        1 => 5,
        2 => 1,
    ];

    public static function getQuestionTypeText($type)
    {
        return self::$question_type_array[$type];
    }

    public static function getStampOfQuestion($type)
    {
        return self::$stamp_of_question_array[$type];
    }

    public function getTitleAttribute()
    {
        return $this->question_title ?? '';
    }

    public function getAnswerScriptAttribute()
    {
        $options = $this->question_answers()->get();

        $answer_script = "";

        foreach($options as $option) {
            $answer_script .= $option->correct_ans ?? ".";
            if($this->type == 2) {
                break;
            }
        }

        return $answer_script;
    }

    public function getPathTitleAttribute()
    {
        return "Question";
    }

    public function getPathUrlAttribute()
    {
        switch ($this->type) {
            case 1:
                $segment = "question";
                break;
            case 2:
                $segment = "sba";
                break;
            default:
                $segment = "question";
        }

        return url('/admin/' . $segment . '/' . $this->id);
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects', 'subject_id', 'id');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Chapters', 'chapter_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics', 'topic_id', 'id');
    }

    public function quest_subject()
    {
        return $this->belongsTo('App\QuestionSubject', 'subject_id', 'id');
    }

    public function quest_chapter()
    {
        return $this->belongsTo('App\QuestionChapter', 'chapter_id', 'id');
    }

    public function quest_topic()
    {
        return $this->belongsTo('App\QuestionTopic', 'topic_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany('App\Answers', 'question_id', 'id');
    }

    public function correct_ans()
    {
        return $this->hasMany('App\Question_ans', 'correct_ans', 'only for mcq , only T,F');
    }

    public function question_answers()
    {
        return $this->hasMany('App\Question_ans', 'question_id', 'id');
    }
    public function question_references()
    {
        return $this->hasMany('App\QuestionReference', 'question_id', 'id');
    }

    public function question_video_links()
    {
        return $this->hasMany(QuestionVideoLink::class);
    }

    public function log()
    {
        return $this->morphMany(LogHistory::class, 'loghistory');
    }

    public function reference_books()
    {
        return $this->hasMany(QuestionReferenceBook::class);
    }
    
    public function reference_book()
    {
        return $this->hasOne(QuestionReferenceBook::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'question_labels')
            ->whereNull('question_labels.deleted_at');
    }

    public function question_options()
    {
        return $this->hasMany(Question_ans::class, 'question_id', 'id');
    }
}
