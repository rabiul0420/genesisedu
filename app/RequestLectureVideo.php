<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestLectureVideo extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        "end" => "datetime"
    ];

    private static $status_array = [
        0 => "Request Lectue Video",
        1 => "Current watching",
        2 => "Complete",
    ];

    private static $status_message_array = [
        0 => "",
        1 => "Video Available upto",
        2 => "Complete"
    ];

    public function getStatusMessageAttribute()
    {
        return self::$status_message_array[$this->status] ?? '';
    }

    public function doctor_course()
    {
        return $this->belongsTo(DoctorsCourses::class, 'doctor_course_id');
    }

    public function pending_video()
    {
        return $this->belongsTo(PendingVideo::class);
    }
}
