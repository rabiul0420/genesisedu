<?php

namespace App\Http\Controllers\Admin;

use App\CourseYear;
use App\Courses;
use App\CourseYearSessions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Sessions;
use App\YearCourseSession;
use Illuminate\Support\Facades\DB;
use Session;



class CourseYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courseyear = CourseYear::with('course','course_year_session','course_year_session.session')->get();
        return view('admin.courseyear.list',['course_years'=>$courseyear]);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() 
    {
        $data['course'] = Courses::pluck('name', 'id');
        $data['sessions'] =  DB::table('sessions')->pluck('name', 'sessions.id');
        return view('admin.courseyear.create',$data); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => ['required'],
            'course_id' => ['required'],
            'status' =>['required'],
        ]);

        if (CourseYear::where(['course_id'=>$request->course_id,'year'=>$request->year])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This course already exists in this year');
            return redirect()->action('Admin\CourseYearController@create')->withInput();
        }

        $course = new CourseYear();
        $course->year = $request->year;
        $course->course_id = $request->course_id;
        $course->status = $request->status;
        $course->save();

        $session_ids = $request->session_id;

        if (is_array($session_ids)) {
            foreach ($session_ids as $key => $value) {
                    
                    if($value == '')continue;
                
                    unset($course_sessions);
                    $course_sessions = new CourseYearSessions();
                    $course_sessions->course_year_id = $course->id;
                    $course_sessions->session_id = $value;
                    $course_sessions->save();
                    
            }
        }

        return redirect()->action('Admin\CourseYearController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CourseYear  $courseYear
     * @return \Illuminate\Http\Response
     */
    public function show(CourseYear $courseYear)
    {
        $course=CourseYear::select('course_year.*')->find($id);
        return view('admin.courseyear.list');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CourseYear  $courseYear
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['courseyear'] = CourseYear::find($id);
        $data['courses'] = Courses::pluck('name', 'id');
        $data['sessions'] =  DB::table('sessions')->pluck('name', 'sessions.id');

        $data['selected_sessions' ] = DB::table('course_year_session')
        ->join('course_year', 'course_year.id', '=', 'course_year_session.course_year_id')
        ->where('course_year_id',$id)
        ->where('course_year.deleted_at',NULL)
        ->where('course_year_session.deleted_at',NULL)
        ->pluck( 'session_id');
        return view('admin.courseyear.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CourseYear  $courseYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $courseyear =  CourseYear::find($id);
        $courseyear->course_id  = $request->course_id ;
        $courseyear->year = $request->year;
        $courseyear->status = $request->status;
       
        $courseyear->push();

        $session_ids = $request->session_id;

        
        CourseYearSessions::where('course_year_id', $id)->delete(); 

        if (is_array($session_ids)) {
            foreach ($session_ids as $key => $value) {
                    
                    if($value == '')continue;
                
                    unset($course_sessions);
                    $course_sessions = new CourseYearSessions();
                    $course_sessions->course_year_id = $id;
                    $course_sessions->session_id = $value;
                    $course_sessions->save();
                    
            }
        }



        return redirect()->action('Admin\CourseYearController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CourseYear  $courseYear
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CourseYear::destroy($id); 
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\CourseYearController@index');
    }
}
