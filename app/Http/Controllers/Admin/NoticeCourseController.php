<?php
namespace App\Http\Controllers\Admin;
use App\NoticeCourse;
use App\NoticeCourseNotice;
use App\Http\Controllers\Controller;
use App\Notice;
use App\Sessions;
use Illuminate\Http\Request;
use App\Notice_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Coursees;
use App\Branch;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;


class NoticeCourseController extends Controller
{
    //
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        $data['notice_courses'] = NoticeCourse::get();
        $data['module_name'] = 'Notice Course';
        $data['title'] = 'Notice Course List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.notice_course.list',$data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user=Subjects::find(Auth::id());
        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');

        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['branches'] = Branch::pluck('name','id');

        $data['notices'] = Notice::pluck('title', 'id');



        $data['module_name'] = 'Notice Course';
        $data['title'] = 'Notice Course Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.notice_course.create',$data);
        //echo "Topic create";
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
            'session_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'notice_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\NoticeCourseController@create')->withInput();
        }

        if (NoticeCourse::where(['year'=>$request->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\NoticeCourseController@create')->withInput();
        }
        else{

            $notice_course = new NoticeCourse();

            $notice_course->year = $request->year;
            $notice_course->session_id = $request->session_id;
            $notice_course->institute_id=$request->institute_id;
            $notice_course->course_id=$request->course_id;
            $notice_course->status=$request->status;
            $notice_course->created_by=Auth::id();
            $notice_course->save();


            $notice_ids = $request->notice_id;

            if (is_array($notice_ids)) {
                foreach ($notice_ids as $key => $value) {

                    if($value == '')continue;

                    unset($notice_course_notice);
                    $notice_course_notice = new NoticeCourseNotice();
                    $notice_course_notice->notice_course_id = $notice_course->id;
                    $notice_course_notice->notice_id = $value;
                    //$notice_course_notice->status = $request->status;
                    //$notice_course_notice->created_by = Auth::id();
                    $notice_course_notice->save();

                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\NoticeCourseController@index');
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notice_course=NoticeCourse::select('notice_courses.*')->find($id);
        return view('admin.notice_course.show',['notice_course'=>$notice_course]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=Subjects::find(Auth::id());
         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/
        $notice_course = NoticeCourse::find($id);
        $data['notice_course'] = NoticeCourse::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['notices'] = Notice::pluck('title', 'id');

        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$notice_course->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$notice_course->institute_id)->pluck('name', 'id');

        $selected_notices = array();
        foreach($notice_course->notices as $notice)
        {
            $selected_notices[] = $notice->notice_id;
        }


        $data['selected_notices'] = collect($selected_notices);


        $data['notices'] = Notice::pluck('title', 'id');


        $data['module_name'] = 'Notice Course';
        $data['title'] = 'Notice Course Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.notice_course.edit', $data);

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
            'year' => ['required'],
            'session_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'notice_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $notice_course = NoticeCourse::find($id);

        if($notice_course->year != $request->year || $notice_course->session_id != $request->session_id || $notice_course->institute_id != $request->institute_id || $notice_course->course_id != $request->course_id) {

            if (NoticeCourse::where(['year'=>$request->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\NoticeCourseController@edit',[$id])->withInput();
            }

        }

        $notice_course->year = $request->year;
        $notice_course->session_id = $request->session_id;
        $notice_course->institute_id=$request->institute_id;
        $notice_course->course_id=$request->course_id;
        $notice_course->status=$request->status;
        $notice_course->updated_by=Auth::id();
        $notice_course->push();


        $notice_ids = $request->notice_id;

        if(NoticeCourseNotice::where('notice_course_id',$notice_course->id)->first())
        {
            NoticeCourseNotice::where('notice_course_id',$notice_course->id)->delete();
        }

        if (is_array($notice_ids)) {

            foreach ($notice_ids as $key => $value) {

                if($value == '')continue;

                unset($notice_course_notice);
                $notice_course_notice = new NoticeCourseNotice();
                $notice_course_notice->notice_course_id = $notice_course->id;
                $notice_course_notice->notice_id = $value;
                //$notice_course_notice->status = $request->status;
                //$notice_course_notice->created_by = Auth::id();
                $notice_course_notice->save();

            }
        }
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
        /*$user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        NoticeCourse::destroy($id); // 1 way
        NoticeCourseNotice::where('notice_course_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\NoticeCourseController@index');
    }
}