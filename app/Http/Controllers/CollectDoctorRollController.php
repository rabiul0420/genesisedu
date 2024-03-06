<?php

namespace App\Http\Controllers;

use App\DoctorsCourses;
use Illuminate\Http\Request;

class CollectDoctorRollController extends Controller
{
    public function store(Request $request)
    {
        $doctor_course = DoctorsCourses::findOrFail($request->doctor_course_id);
        $doctor_course->roll = $request->roll;
        $doctor_course->save();

        return back();
    }
}
