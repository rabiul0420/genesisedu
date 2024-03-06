<?php

namespace App\Http\Controllers;

use App\DoctorInstituteChoice;
use App\InstituteAllocation;
use Illuminate\Http\Request;

class DoctorInstituteChoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:doctor');
    }

    public function index()
    {
        //
    }

    public function create()
    {
        return view('doctor_institute_choice', [
            'instituteAllocations' => InstituteAllocation::orderBy('name')->pluck('name', 'id')
        ]);
    }

    public function store(Request $request)
    {
        DoctorInstituteChoice::create($this->checkValidatedData(request()));
        return redirect()
            ->route('doctor-course-exam', [$request->doctor_course_id, $request->exam_id]);
    }

    public function show(DoctorInstituteChoice $doctorInstituteChoice)
    {
        //
    }

    public function edit(DoctorInstituteChoice $doctorInstituteChoice)
    {
        //
    }

    public function update(Request $request, DoctorInstituteChoice $doctorInstituteChoice)
    {
        //
    }

    public function destroy(DoctorInstituteChoice $doctorInstituteChoice)
    {
        //
    }

    private function checkValidatedData($validatedData)
    {
        return $validatedData->validate([
            'doctor_course_id'         => 'required|numeric',
            'exam_id'           => 'required|numeric',
            'first_institute'   => '',
            'second_institute'  => '',
            'third_institute'   => '',
        ]);
    }
}
