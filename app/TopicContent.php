<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicContent extends Model
{
    protected $table = 'topic_content';

    use SoftDeletes, ScheduleDefs;

    public function topic()
    {
        return $this->belongsTo('App\Topic','topic_id','id');
    }

    public function mentor()
    {
        return $this->belongsTo('App\Teacher','content_id','id');
    }

    public function lecture_video()
    {
        return $this->belongsTo('App\LectureVideo','content_id','id');
    }

    public function exam()
    {
        return $this->belongsTo('App\Exam','content_id','id');
    }

    public function lecture_sheet()
    {
        return $this->belongsTo('App\LectureSheet','content_id','id');
    }

}
