<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Mockery\Matcher\Ducktype;

class DoctorClassView extends Model
{


    const CLASS_CRITERIA_LIST = ['Introduction', 'Knowledge depth of Mentor', 'Expression Capability', 'Interaction', 'Overall'];
    const SOLVE_CLASS_CRITERIA_LIST = ['Introduction', 'Knowledge depth of Mentor', 'Expression Capability', 'Interaction', 'Overall'];
    const VIDEO_QUALITY_CRITERIA_LIST = ['Projector Support', 'Sound System', 'Video Quality'];

    const PROGRESSES = ['', 'Average', 'Good', 'Very Good', 'Excellent']; //1=Average, 2=Good, 3=Very Good , 4=Excellent
    const VIDEO_PROGRESSES = ['', 'Smooth', 'Little bit disturb', 'disturb']; //1=Average, 2=Good, 3=Very Good , 4=Excellent

    static $mentor_id;

    public static function getProgresses()
    {
        return self::PROGRESSES;
    }

    public static function getVideoProgresses()
    {
        return self::VIDEO_PROGRESSES;
    }



    public static function getAllCriteria()
    {
        $classCriteriaList = self::getClassCriteriaList();
        $solveClassCriteriaList = self::getSolveClassCriteriaList();
        $videoQualityCriteriaList = self::getVideoQualityCriteriaList();

        return array_merge($classCriteriaList, $solveClassCriteriaList, $videoQualityCriteriaList);
    }

    public static function getAllProgresses()
    {
        $progressList = self::getProgresses();
        $videoProgressList = self::getVideoProgresses();

        return array_merge($progressList, $videoProgressList);
    }

    public static function getClassCriteriaList()
    {
        return self::CLASS_CRITERIA_LIST;
    }

    public static function getSolveClassCriteriaList()
    {
        return self::SOLVE_CLASS_CRITERIA_LIST;
    }

    public static function getVideoQualityCriteriaList()
    {
        return self::VIDEO_QUALITY_CRITERIA_LIST;
    }

    public function schedule_details()
    {

        return $this->hasMany('App\ScheduleDetail', 'class_or_exam_id', 'lecture_video_id');
    }

    public function schedule_detail()
    {
        return $this->hasOne('App\ScheduleDetail', 'class_or_exam_id', 'lecture_video_id')
            ->where('mentor_id', self::$mentor_id);
    }

    public function saveRatings($variant,  array $data)
    {
        if ($this->doctor_course_id) {
            return $this->save();
        }
        return false;
    }

    public static $allRattings = null;

    public function setRatings($variant,  array $data)
    {

        if (self::$allRattings === null) {
            self::$allRattings = json_decode($this->ratings, true) ?? [];
        }

        $ratings = self::$allRattings[$variant] ?? [];

        $prev = $ratings;

        $ccl = self::getClassCriteriaList();
        $sccl = self::getSolveClassCriteriaList();
        $vqcl = self::getVideoQualityCriteriaList();

        $crList = array_merge($ccl, $sccl, $vqcl);

        foreach ($data as $key => $value) {
            if (in_array($key, $crList)) {
                $ratings[$key] = $value;
            }
        }

        //dd( $data, self::$allRattings, $prev, $ratings );

        self::$allRattings[$variant] = $ratings;

        $this->ratings = json_encode(self::$allRattings);
        return $this;
    }

    public function getRatings($variant): Collection
    {
        $all_ratings = json_decode($this->ratings, true) ?? [];
        return Collection::make($all_ratings[$variant] ?? []);
    }

    public function getRatingValue($variant, $key)
    {
        return $this->getRatings($variant)->get($key);
    }

    public function getAllRatings()
    {
        $video = $this->getRatings('video-quality');
        $primary = $this->getRatings('primary');
        return $video->merge($primary);
    }

    public function getPrimaryRatings()
    {
        $primary = $this->getRatings('primary');
        return $primary;
    }

    public function getVideoRatings()
    {
        $video = $this->getRatings('video-quality');
        return $video;
    }

    public function doctor_course()
    {
        return $this->belongsTo(DoctorsCourses::class);
    }
    public function lecture_video()
    {
        return $this->belongsTo(LectureVideo::class);
    }
}
