<?php

namespace App\Http\Controllers\Api;

use App\Courses;
use App\CourseYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Institutes;

class CourseController extends Controller
{
    public function index()
    {
        return Courses::query()
            ->whereIn('id', $this->getCourseIdArray(request()->year))
            ->get([
                'id',
                'name',
            ]);
    }

    public function getCourseIdArray($year = null)
    {
        return CourseYear::query()
            ->when($year, function ($query, $year) {
                $query->where('year', $year);
            })
            ->where('course_year.status', 1)
            ->pluck('course_id');
    }

    public function courseByYear($year = null)
    {
        $course_ids = $this->getCourseIdArray($year);
        // return
        $courses = Courses::query()
            ->whereIn('id', $course_ids)
            ->get([
                'id',
                'name',
                'institute_id',
            ]);

        $institutes = Institutes::query()
            ->with([
                'courses' => function($query) use ($course_ids) {
                    $query->whereIn('id', $course_ids)
                    ->select([
                        'id',
                        'name',
                        'institute_id'
                    ]);
                }
            ])
            ->whereHas('courses', function ($query) use ($course_ids) {
                $query->whereIn('id', $course_ids);
            })
            ->get();

        return response($institutes->toArray());
    }
}
