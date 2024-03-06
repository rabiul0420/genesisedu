<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Sms;
use App\SmsAssign;
use App\SmsDiscipline;
use App\SmsFaculty;
use App\SmsAssignBatchSmsAssign;
use App\Sessions;
use Illuminate\Http\Request;
use App\sms_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Topics;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class SmsAssignController extends Controller
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
        $data['sms_assigns'] = SmsAssign::get();
        $data['module_name'] = 'Sms Assign';
        $data['title'] = 'Sms Assign List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.sms_assign.list',$data);
                
        //echo $Institutes;
        //echo $title;
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

        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['smss'] = Sms::where(['type'=>'B'])->orWhere(['type'=>'C'])->pluck('title', 'id');

        //echo "<pre>";print_r($data);exit;

        $data['module_name'] = 'Sms Assign';
        $data['title'] = 'Sms Assign Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.sms_assign.create',$data);
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
            'sms_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],    
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\SmsAssignController@create')->withInput();
        }        

        if (SmsAssign::where(['sms_id'=>$request->sms_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Sms assignment already exists');
            return redirect()->action('Admin\SmsAssignController@create')->withInput();
        }
        else{

            $sms_assign = new SmsAssign();
            $sms_assign->sms_id = $request->sms_id;
            $sms_assign->institute_id = $request->institute_id;
            $sms_assign->course_id = $request->course_id;
            $sms_assign->status=$request->status;
            $sms_assign->created_by=Auth::id();
            $sms_assign->save();

            $institute = Institutes::where('id',$request->institute_id)->first();

            if($institute->type == 1)
            {
                if (SmsFaculty::where('sms_assign_id', $sms_assign->id)->first()) {
                    SmsFaculty::where('sms_assign_id', $sms_assign->id)->delete();
                }

                if($request->faculty_id)
                {
                    foreach ($request->faculty_id as $key => $value) {
                        if($value=='')continue;
                        SmsFaculty::insert(['sms_assign_id' => $sms_assign->id,'sms_id' => $sms_assign->sms_id, 'faculty_id' => $value]);
                    }
                }

                if (SmsDiscipline::where('sms_assign_id', $sms_assign->id)->first()) {
                    SmsDiscipline::where('sms_assign_id', $sms_assign->id)->delete();
                }
    
                if($request->subject_id)
                {
                    foreach ($request->subject_id as $key => $value) {
                        if($value=='')continue;
                        SmsDiscipline::insert(['sms_assign_id' => $sms_assign->id,'sms_id' => $sms_assign->sms_id, 'subject_id' => $value]);
                    }
                }

            }
            else
            {

                if (SmsDiscipline::where('sms_assign_id', $sms_assign->id)->first()) {
                    SmsDiscipline::where('sms_assign_id', $sms_assign->id)->delete();
                }

                if($request->subject_id)
                {
                    foreach ($request->subject_id as $key => $value) {
                        if($value=='')continue;
                        SmsDiscipline::insert(['sms_assign_id' => $sms_assign->id,'sms_id' => $sms_assign->sms_id, 'subject_id' => $value]);
                    }
                }

            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\SmsAssignController@index');
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
        $sms_assign=SmsAssign::select('sms_assigns.*')->find($id);
        return view('admin.sms_assign.show',['sms_assign'=>$sms_assign]);
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
        $sms_assign = SmsAssign::find($id);
        $data['sms_assign'] = SmsAssign::find($id);
        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['smss'] = Sms::where(['type'=>'B'])->orWhere(['type'=>'C'])->pluck('title', 'id');
        
        $institute = Institutes::where('id',$sms_assign->institute_id)->first();
        if($institute)$institute_type = $institute->type;
        else $institute_type = null;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties':'courses-subjects';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$sms_assign->institute_id)->pluck('name', 'id');
        
        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where(['institute_id'=>'6','course_id'=>'13'])->pluck('name', 'id');

            $sms_assign_faculties = SmsFaculty::where('sms_assign_id',$id)->get();
            $selected_faculties = array();
            foreach($sms_assign_faculties as $faculty)
            {
                $selected_faculties[] = $faculty->faculty_id;
            }

            $data['selected_faculties'] = collect($selected_faculties);

            $data['subjects'] = Subjects::where(['institute_id'=>'4','course_id'=>'19'])->pluck('name', 'id');

            $sms_assign_disciplines = SmsDiscipline::where('sms_assign_id',$id)->get();
            $selected_subjects = array();
            foreach($sms_assign_disciplines as $sms_assign_discipline)
            {
                $selected_subjects[] = $sms_assign_discipline->subject_id;
            }

            $data['selected_subjects'] = collect($selected_subjects);

        }else{
            $data['subjects'] = Subjects::where('course_id',$sms_assign->course_id)->pluck('name', 'id');

            $sms_assign_disciplines = SmsDiscipline::where('sms_assign_id',$id)->get();
            $selected_subjects = array();
            foreach($sms_assign_disciplines as $sms_assign_discipline)
            {
                $selected_subjects[] = $sms_assign_discipline->subject_id;
            }

            $data['selected_subjects'] = collect($selected_subjects);
        }

        $data['module_name'] = 'Sms Assign';
        $data['title'] = 'Sms Assign Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.sms_assign.edit', $data);
        
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
        //echo '<pre>';print_r($request->subject_id);exit;
        $validator = Validator::make($request->all(), [
            'sms_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
        
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\SmsAssignController@edit',[$id])->withInput();
        }

        $sms_assign = SmsAssign::find($id);

        if($sms_assign->sms_id != $request->sms_id || $sms_assign->institute_id != $request->institute_id || $sms_assign->course_id != $request->course_id) {

            if (SmsAssign::where(['sms_id'=>$request->sms_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Sms assignment already exists');
                return redirect()->action('Admin\SmsAssignController@edit',[$id])->withInput();
            }

        }

        $sms_assign->sms_id = $request->sms_id;
        $sms_assign->institute_id = $request->institute_id;
        $sms_assign->course_id = $request->course_id;
        $sms_assign->status=$request->status;
        $sms_assign->updated_by=Auth::id();
        $sms_assign->push();

        $institute = Institutes::where('id',$request->institute_id)->first();

        if($institute->type == 1)
        {
            if (SmsFaculty::where('sms_assign_id', $sms_assign->id)->first()) {
                SmsFaculty::where('sms_assign_id', $sms_assign->id)->delete();
            }

            if($request->faculty_id)
            {
                foreach ($request->faculty_id as $key => $value) {
                    if($value=='')continue;
                    SmsFaculty::insert(['sms_assign_id' => $sms_assign->id,'sms_id' => $sms_assign->sms_id, 'faculty_id' => $value]);
                }
            }

            
            if (SmsDiscipline::where('sms_assign_id', $sms_assign->id)->first()) {
                SmsDiscipline::where('sms_assign_id', $sms_assign->id)->delete();
            }

            if($request->subject_id)
            {
                foreach ($request->subject_id as $key => $value) {
                    if($value=='')continue;
                    SmsDiscipline::insert(['sms_assign_id' => $sms_assign->id,'sms_id' => $sms_assign->sms_id, 'subject_id' => $value]);
                }
            }            

        }
        else
        {

            if (SmsDiscipline::where('sms_assign_id', $sms_assign->id)->first()) {
                SmsDiscipline::where('sms_assign_id', $sms_assign->id)->delete();
            }

            if($request->subject_id)
            {
                foreach ($request->subject_id as $key => $value) {
                    if($value=='')continue;
                    SmsDiscipline::insert(['sms_assign_id' => $sms_assign->id,'sms_id' => $sms_assign->sms_id, 'subject_id' => $value]);
                }
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
        SmsAssign::destroy($id); // 1 way
        if (SmsFaculty::where('sms_assign_id', $id)->first()) {
            SmsFaculty::where('sms_assign_id', $id)->delete();
        }
        if (SmsDiscipline::where('sms_assign_id', $id)->first()) {
            SmsDiscipline::where('sms_assign_id', $id)->delete();
        }
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SmsAssignController@index');
    }
}  