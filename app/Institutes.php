<?php

namespace App;

use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Institutes extends Model
{
    use SoftDeletes;

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInActive($query)
    {
        return $query->where('status', 0);
    }

    protected $table = 'institutes';

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

    public $timestamps = false;

    public function faculty_label(){
        return ($this->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ? 'Residency ':'') . 'Faculty';
    }

    public function discipline_label(){
        return ($this->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ? 'FCPS Part-1 ':'') . 'Discipline';
    }

    public function courses( ){
        return $this->hasMany( 'App\Courses',  'institute_id', 'id' );
    }

    public function active_courses(){
        return $this->hasMany(Courses::class,  'institute_id', 'id' )
            ->where('courses.status', 1);
    }


}
