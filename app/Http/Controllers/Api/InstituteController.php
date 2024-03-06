<?php

namespace App\Http\Controllers\Api;

use App\CourseYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Institutes;

class InstituteController extends Controller
{
    public function index()
    {
        $year = request()->year;

        $institutes = Institutes::query()
            ->whereHas('courses', function ($query) use ($year) {
                $query->whereIn('id', $this->getCourseIdArray($year));
            })
            ->get([
                'id',
                'name'
            ]);

        if(request()->with_courses) {
            $institutes->load([
                'courses' => function($query) use ($year) {
                    $query->whereIn('id', $this->getCourseIdArray($year))
                    ->select([
                        'id',
                        'name',
                        'institute_id'
                    ]);
                }
            ]);
        }
            
        return $institutes;
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
}
