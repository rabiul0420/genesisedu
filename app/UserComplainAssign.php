<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserComplainAssign extends Model
{
    // protected $table = 'user_complain_type';
    protected $table = 'complain_user_permissions';
    
    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }

    public function course_complain_type(){
        return $this->belongsTo('App\CourseComplainType','course_complain_type_id','id');
    }


}
