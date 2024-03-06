<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\OnlineExamCommonCode;
use App\OnlineExamLink;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;


class OnlineExamLinkController extends Controller
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
        $data['online_exam_links'] = OnlineExamLink::get();
        $data['module_name'] = 'Online Exam Link';
        $data['title'] = 'Online Exam Link Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.online_exam_link.list',$data);
                
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

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::get()->pluck('name', 'id');

        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        $data['exam_comm_codes'] = OnlineExamCommonCode::get()->pluck('exam_comm_code', 'id');

        $data['module_name'] = 'Online Exam Link';
        $data['title'] = 'Online Exam Link Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.online_exam_link.create',$data);
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
            'batch_id' => ['required'],
            'exam_comm_code_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\OnlineExamLinkController@create')->withInput();
        }

        if (OnlineExamLink::where(['exam_comm_code_id'=>$request->exam_comm_code_id,'year'=>$request->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This common code already exists for the batch');
            return redirect()->action('Admin\OnlineExamLinkController@create')->withInput();
        }
        else{

            $online_exam_link = new OnlineExamLink();
            $online_exam_link->exam_comm_code_id = $request->exam_comm_code_id;
            $online_exam_link->year = $request->year;
            $online_exam_link->session_id = $request->session_id;
            $online_exam_link->institute_id=$request->institute_id;
            $online_exam_link->course_id=$request->course_id;
            $online_exam_link->faculty_id=$request->faculty_id;
            $online_exam_link->subject_id=$request->subject_id;
            $online_exam_link->batch_id=$request->batch_id;
            $online_exam_link->status=$request->status;
            $online_exam_link->created_by=Auth::id();
            $online_exam_link->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\OnlineExamLinkController@index');
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
        $user=Subjects::select('users.*')->find($id);
        return view('admin.subjects.show',['user'=>$user]);
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
        $online_exam_link = OnlineExamLink::find($id);
        $data['online_exam_link'] = OnlineExamLink::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        $data['exam_comm_codes'] = OnlineExamCommonCode::get()->pluck('exam_comm_code', 'id');

        $institute_type = Institutes::where('id',$online_exam_link->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-batches':'courses-subjects-batches';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::get()->where('institute_id',$online_exam_link->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$online_exam_link->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$online_exam_link->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$online_exam_link->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::get()->where('institute_id',$online_exam_link->institute_id)
            ->where('course_id',$online_exam_link->course_id)
            ->pluck('name', 'id');

        $data['module_name'] = 'Online Exam Link';
        $data['title'] = 'Online Exam Link Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.online_exam_link.edit', $data);
        
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
            'batch_id' => ['required'],
            'exam_comm_code_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $online_exam_link = OnlineExamLink::find($id);

        if($online_exam_link->exam_comm_code_id != $request->exam_comm_code_id || $online_exam_link->year != $request->year || $online_exam_link->session_id != $request->session_id || $online_exam_link->institute_id != $request->institute_id || $online_exam_link->course_id != $request->course_id || $online_exam_link->batch_id != $request->batch_id) {

            if (OnlineExamLink::where(['exam_comm_code_id'=>$request->exam_comm_code_id,'year'=>$request->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Exam Common Code already exists');
                return redirect()->action('Admin\OnlineExamLinkController@edit',[$id])->withInput();
            }

        }

        $online_exam_link->exam_comm_code_id = $request->exam_comm_code_id;
        $online_exam_link->year = $request->year;
        $online_exam_link->session_id = $request->session_id;
        $online_exam_link->institute_id=$request->institute_id;
        $online_exam_link->course_id=$request->course_id;
        $online_exam_link->faculty_id=$request->faculty_id;
        $online_exam_link->subject_id=$request->subject_id;
        $online_exam_link->batch_id=$request->batch_id;
        $online_exam_link->status=$request->status;
        $online_exam_link->updated_by=Auth::id();
        $online_exam_link->push();
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
        OnlineExamLink::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\OnlineExamLinkController@index');
    }
}