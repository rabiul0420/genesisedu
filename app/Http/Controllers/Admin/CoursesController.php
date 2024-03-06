<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Courses;
use App\Institutes;
use App\Sessions;
use App\CourseSessions;
use App\Models\Moreinfo;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Validator;


class CoursesController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Auth::loginUsingId(1);
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /*  if(!$user->hasRole('Admin')){
              return abort(404);
          }*/
        $data['courses'] = Courses::get();
        $data['module_name'] = 'Course';
        $data['title'] = 'Genesis Admin : Courses List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.settings.course_list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user=Courses::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['institute'] = Institutes::get()->pluck('name', 'id');
        $data['module_name'] = 'Course';
        $data['title'] = 'Genesis Admin : Course Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.settings.course_create',$data);
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
            'name' => ['required'],
            'course_code' => ['required'],
            'institute_id' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\CoursesController@create')->withInput();
        }

        if (Courses::where('name',$request->name)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This name  already exists');
            //return back()->withInput();
            return redirect()->action('Admin\CoursesController@create')->withInput();
        }

        if (Courses::where('course_code',$request->course_code)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Course Code  already exists');
            //return back()->withInput();
            return redirect()->action('Admin\CoursesController@create')->withInput();
        }

        $course = new Courses();
        $course->name = $request->name;
        $course->priority = $request->priority;
        $course->course_code = $request->course_code;
        $course->institute_id = $request->institute_id;
        $course->bkash_marchent_number = $request->bkash_marchent_number;
        $course->status = $request->status;
        $course->course_detail = $request->course_detail;
        $course->created_by = Auth::id();        
        $course->save();

        Cache::forget(self::HOME_PAGE_COURSE);

        Redis::del('PageCourses');

        Session::flash('message', 'Record has been added successfully');

        //return back();

        return redirect()->action('Admin\CoursesController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course=Courses::select('settings.courses.*')
            ->find($id);
        return view('admin.settings.courses.show',['course'=>$course]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=Courses::find(Auth::id());
         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/

        $data['course'] = Courses::find($id);
        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['module_name'] = 'Course';
        $data['title'] = 'Course Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.settings.course_edit',$data);
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
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'course_code' => ['required'],
            'institute_id' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\CoursesController@edit',[$id])->withInput();
        }

        $course = Courses::find($id);
        
        if($course->name != $request->name){
            if (Courses::where('name',$request->name)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This name  already exists');
                //return back()->withInput();
                return redirect()->action('Admin\CoursesController@edit',[$id])->withInput();
            }
        }

        if($course->course_code != $request->course_code){
            if (Courses::where('course_code',$request->course_code)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Course Code  already exists');
                //return back()->withInput();
                return redirect()->action('Admin\CoursesController@edit',[$id])->withInput();
            }
        }

        $course->name = $request->name;
        $course->priority = $request->priority;
        $course->course_code = $request->course_code;
        $course->institute_id = $request->institute_id;
        $course->bkash_marchent_number = $request->bkash_marchent_number;
        $course->status = $request->status;
        $course->course_detail = $request->course_detail;
        $course->updated_by = Auth::id();

        $course->push();

        Cache::forget(self::HOME_PAGE_COURSE);
        Cache::forget(self::HOME_PAGE_COURSE_ . $course->id);

    
        $courses = collect(json_decode (Redis::get('PageCourses')));
        $course = $courses->where('id',$id)->first();
      
        $course->name = $request->name;
        $course->priority = $request->priority;
        $course->course_code = $request->course_code;
        $course->institute_id = $request->institute_id;
        $course->bkash_marchent_number = $request->bkash_marchent_number;
        $course->status = $request->status;
        $course->course_detail = $request->course_detail;
        $index = $courses->search($course);
        $courses[$index] = $course;
       
        Redis::set('PageCourses', json_encode($courses, TRUE));

        Session::flash('message', 'Record has been updated successfully');

        return back();

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$user=Courses::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $course = Courses::find($id);
        $course->deleted_by=Auth::id();
        $course->push();
        Courses::destroy($id); // 1 way

        $courses = collect(json_decode (Redis::get('PageCourses')));
        $course  = $courses->where('id',$id)->first();
        $index   = $courses->search($course);
        $courses ->forget($index);
      
        Redis::set('PageCourses', json_encode($courses, TRUE));
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\CoursesController@index');
    }





}
