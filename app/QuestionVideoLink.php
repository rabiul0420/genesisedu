<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionVideoLink extends Model
{
    use SoftDeletes;

    protected $table = 'question_video_links';
    public $timestamps = false;
    protected $guarded = [];



}
