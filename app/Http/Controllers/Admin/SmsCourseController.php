<?php
namespace App\Http\Controllers\Admin;
use App\SmsCourse;
use App\SmsCourseSms;
use App\Http\Controllers\Controller;
use App\Sms;
use App\Sessions;
use Illuminate\Http\Request;
use App\Sms_question;
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


class SmsCourseController extends Controller
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
        $data['sms_courses'] = SmsCourse::get();
        $data['module_name'] = 'Sms Course';
        $data['title'] = 'Sms Course List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.sms_course.list',$data);

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

        $data['smss'] = Sms::where(['type'=>'C'])->pluck('title', 'id');



        $data['module_name'] = 'Sms Course';
        $data['title'] = 'Sms Course Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.sms_course.create',$data);
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
            'sms_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\SmsCourseController@create')->withInput();
        }

        if (SmsCourse::where(['year'=>$request->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\SmsCourseController@create')->withInput();
        }
        else{

            $sms_course = new SmsCourse();

            $sms_course->year = $request->year;
            $sms_course->session_id = $request->session_id;
            $sms_course->institute_id=$request->institute_id;
            $sms_course->course_id=$request->course_id;
            $sms_course->status=$request->status;
            $sms_course->created_by=Auth::id();
            $sms_course->save();


            $sms_ids = $request->sms_id;

            if (is_array($sms_ids)) {
                foreach ($sms_ids as $key => $value) {

                    if($value == '')continue;

                    unset($sms_course_sms);
                    $sms_course_sms = new SmsCourseSms();
                    $sms_course_sms->sms_course_id = $sms_course->id;
                    $sms_course_sms->sms_id = $value;
                    //$sms_course_sms->status = $request->status;
                    //$sms_course_sms->created_by = Auth::id();
                    $sms_course_sms->save();

                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\SmsCourseController@index');
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
        $sms_course=SmsCourse::select('sms_courses.*')->find($id);
        return view('admin.sms_course.show',['sms_course'=>$sms_course]);
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
        $sms_course = SmsCourse::find($id);
        $data['sms_course'] = SmsCourse::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');

        

        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$sms_course->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$sms_course->institute_id)->pluck('name', 'id');

        $selected_smss = array();
        foreach($sms_course->smss as $sms)
        {
            $selected_smss[] = $sms->sms_id;
        }


        $data['selected_smss'] = collect($selected_smss);


        $data['smss'] = Sms::where(['type'=>'C'])->pluck('title', 'id');


        $data['module_name'] = 'Sms Course';
        $data['title'] = 'Sms Course Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.sms_course.edit', $data);

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
            'sms_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $sms_course = SmsCourse::find($id);

        if($sms_course->year != $request->year || $sms_course->session_id != $request->session_id || $sms_course->institute_id != $request->institute_id || $sms_course->course_id != $request->course_id) {

            if (SmsCourse::where(['year'=>$request->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\SmsCourseController@edit',[$id])->withInput();
            }

        }

        $sms_course->year = $request->year;
        $sms_course->session_id = $request->session_id;
        $sms_course->institute_id=$request->institute_id;
        $sms_course->course_id=$request->course_id;
        $sms_course->status=$request->status;
        $sms_course->updated_by=Auth::id();
        $sms_course->push();


        $sms_ids = $request->sms_id;

        if(SmsCourseSms::where('sms_course_id',$sms_course->id)->first())
        {
            SmsCourseSms::where('sms_course_id',$sms_course->id)->delete();
        }

        if (is_array($sms_ids)) {

            foreach ($sms_ids as $key => $value) {

                if($value == '')continue;

                unset($sms_course_sms);
                $sms_course_sms = new SmsCourseSms();
                $sms_course_sms->sms_course_id = $sms_course->id;
                $sms_course_sms->sms_id = $value;
                //$sms_course_sms->status = $request->status;
                //$sms_course_sms->created_by = Auth::id();
                $sms_course_sms->save();

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
        SmsCourse::destroy($id); // 1 way
        SmsCourseSms::where('sms_course_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SmsCourseController@index');
    }
}