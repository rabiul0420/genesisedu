<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Sessions extends Model
{

    protected $table = 'sessions';

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInActive($query)
    {
        return $query->where('status', 0);
    }

    public static function tableAs( $alias ) {
        $session = new Sessions;
        $session->table = 'sessions as '. $alias;
        return $session;
    }

    public function course_years()
    {
        return $this->belongsToMany(CourseYear::class, 'course_year_session', 'session_id', 'course_year_id')
            ->whereNull('course_year_session.deleted_at');
    }

}
