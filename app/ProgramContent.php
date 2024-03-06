<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramContent extends Model
{
    protected $table = 'program_content';

    use SoftDeletes, ScheduleDefs;

    public function program()
    {
        return $this->belongsTo('App\Program','program_id','id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topic','content_id','id');
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
