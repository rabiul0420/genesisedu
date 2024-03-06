<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teacher';

    public function programs()
    {
        $topic_content_ids = TopicContent::where(['content_type_id'=>'1','content_id'=>$this->id])->pluck('id')->toArray();
        $program_ids = ProgramContent::where(['content_type_id'=>'3'])->whereIn('content_id',$topic_content_ids)->pluck('program_id')->toArray();
        return Program::whereIn('id',$program_ids)->get();        
    }
}
