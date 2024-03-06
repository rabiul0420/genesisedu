<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LectureVideo extends Model
{
    use SoftDeletes;
    
    //protected $table = 'lecture_videos';
    protected $table = 'lecture_video';
    
    public $timestamps = false;
    
    protected $guarded = [];

    protected $appends = [
        'link'
    ];

    public function getLinkAttribute()
    {
        return $this->lecture_address;
    }

    public function disciplines()
    {
        return $this->hasMany('App\LectureVideoDiscipline','lecture_video_id','id');
    }
    
    public function batches()
    {
        return $this->hasMany('App\LectureVideoTopics','topic_id','topic_id');
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id', 'id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id', 'id');
    }

    public function class()
    {
        return $this->belongsTo('App\Topics','class_id', 'id');
    }

    public function classes_topic()
    {
        return $this->belongsTo('App\Topics','doctor_class_rating', 'id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id', 'id');
    }

    public function faculty_subject()
    {
        return $this->belongsTo('App\Subject','subject_id', 'id');
    }

    public function lecture_video_items(){
        return $this->hasMany(LectureVideoItem::class );
    }

    public function doctor_asks()
    {
        return $this->hasMany(DoctorAsk::class, 'lecture_video_id');
    }
    public function teacher()  
    {
        return $this->belongsTo('App\Teacher', 'teacher_id','id');
    }

    public function addon_content()
    {
        return $this->morphOne(AddonContent::class, 'contentable');
    }

    public function lecture_video_prices()
    {
        return $this->hasMany('App\LectureVideoPrice','lecture_video_id','id' )->orderBy('active_from','desc');
    }

    public function active_price()
    {
        return $this->hasOne(LectureVideoPrice::class, 'lecture_video_id', 'id')
            ->whereDate('lecture_video_price.active_from', '<', Carbon::now()->format('Y-m-d'))
            ->orderBy('lecture_video_price.active_from', 'desc');
    }

    public function subscription()
    {
        return $this->morphOne(Subscription::class, 'subscribable', 'subscribable_type', 'subscribable_id');
    }

}
