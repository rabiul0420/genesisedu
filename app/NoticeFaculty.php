<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticeFaculty extends Model
{
    use  SoftDeletes;
    public $timestamps = false;
    protected $table = 'notice_faculties';

    public function notice()
    {
        return $this->belongsTo('App\Notices','notice_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculties','faculty_id','id');
    }

}
