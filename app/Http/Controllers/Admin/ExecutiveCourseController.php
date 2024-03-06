<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\Executive;
use App\ExecutiveCourse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExecutiveCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $executive_courses = ExecutiveCourse::with('executive','course')->get();
        return view('admin.executive_course.list',compact('executive_courses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $executives = Executive::where('status','1')->get();
        $courses = Courses::get();
        return view('admin.executive_course.create',compact('executives','courses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'executive_id' => ['required'],
            'course_id' => ['required'],
            'status' => ['required'],
        ]);
        
        if($request->course_id){
            foreach($request->course_id as $course){
                if($course == null){
                    continue;
                }else{
                    $executive_course = new ExecutiveCourse();
                    $executive_course->executive_id=$request->executive_id;
                    $executive_course->course_id=$course;
                    $executive_course->status=$request->status;
                    $executive_course->push();
                }
            }
         }
         
        return redirect()->action('Admin\ExecutiveCourseController@index')
            ->with('success','Executive created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $executive_course = ExecutiveCourse::with('course','executive')->find($id);
        $courses = Courses::pluck('name','id');
        
        return view('admin.executive_course.edit',compact('executive_course','courses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        request()->validate([
            'course_id' => ['required'],
            'status' => ['required'],
        ]);
        
        

    
            $executive_course =  ExecutiveCourse::find($id);
            $executive_course->executive_id=$request->executive_id;
            $executive_course->course_id=$request->course_id;
            $executive_course->status=$request->status;
            $executive_course->push();
         
        return redirect()->action('Admin\ExecutiveCourseController@index')
            ->with('success','Executive created successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ExecutiveCourse::destroy($id);
        return redirect()->action('Admin\ExecutiveCourseController@index')
            ->with('success','Executive Deleted successfully');
    }
}
