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


class OnlineExamCommonCodeController extends Controller
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
        $data['online_exam_common_codes'] = OnlineExamCommonCode::get();
        $data['module_name'] = 'Online Exam Code';
        $data['title'] = 'Online Exam Code List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.online_exam_common_code.list',$data);
                
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

        $data['module_name'] = 'Online Exam Code';
        $data['title'] = 'Online Exam Code Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.online_exam_common_code.create',$data);
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
            'exam_comm_code' => ['required'],
            'status' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\OnlineExamCommonCodeController@create')->withInput();
        }

        if (OnlineExamCommonCode::where('exam_comm_code',$request->exam_comm_code)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This common code already exists');
            return redirect()->action('Admin\OnlineExamCommonCodeController@create')->withInput();
        }
        else{

            $online_exam_common_code = new OnlineExamCommonCode();
            $online_exam_common_code->exam_comm_code = $request->exam_comm_code;
            $online_exam_common_code->status=$request->status;
            $online_exam_common_code->created_by=Auth::id();
            $online_exam_common_code->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\OnlineExamCommonCodeController@index');
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
        $online_exam_common_code = OnlineExamCommonCode::select('online_exam_common_codes.*')->find($id);
        return view('admin.online_exam_common_code.show',['online_exam_common_code'=>$online_exam_common_code]);
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
        $data['online_exam_common_code'] = OnlineExamCommonCode::find($id);
        $data['module_name'] = 'Online Exam Code';
        $data['title'] = 'Online Exam Code Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.online_exam_common_code.edit', $data);
        
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
            'exam_comm_code' => ['required'],
            'status' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $online_exam_common_code = OnlineExamCommonCode::find($id);

        if($online_exam_common_code->exam_comm_code != $request->exam_comm_code) {

            if (OnlineExamCommonCode::where('exam_comm_code', $request->exam_comm_code)->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This Exam Common Code already exists');
                return redirect()->action('Admin\OnlineExamCommonCodeController@edit', [$id])->withInput();
            }

        }
        
        $online_exam_common_code->exam_comm_code = $request->exam_comm_code;
        $online_exam_common_code->status=$request->status;
        $online_exam_common_code->updated_by=Auth::id();
        $online_exam_common_code->push();
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
        OnlineExamCommonCode::destroy($id); // 1 way
        OnlineExamLink::where('exam_comm_code_id',$id)->delete();
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\OnlineExamCommonCodeController@index');
    }
}