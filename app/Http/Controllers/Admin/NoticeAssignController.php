<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Notice;
use App\NoticeAssign;
use App\NoticeDiscipline;
use App\NoticeFaculty;
use App\NoticeAssignBatchNoticeAssign;
use App\Sessions;
use Illuminate\Http\Request;
use App\notice_question;
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


class NoticeAssignController extends Controller
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
        $data['notice_assigns'] = NoticeAssign::get();
        $data['module_name'] = 'Notice Assign';
        $data['title'] = 'Notice Assign List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.notice_assign.list',$data);
                
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

        $data['notices'] = Notice::where(['type'=>'B'])->orWhere(['type'=>'C'])->pluck('title', 'id');

        $data['module_name'] = 'Notice Assign';
        $data['title'] = 'Notice Assign Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.notice_assign.create',$data);
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
            'notice_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],    
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\NoticeAssignController@create')->withInput();
        }        

        if (NoticeAssign::where(['notice_id'=>$request->notice_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Notice assignment already exists');
            return redirect()->action('Admin\NoticeAssignController@create')->withInput();
        }
        else{

            $notice_assign = new NoticeAssign();
            $notice_assign->notice_id = $request->notice_id;
            $notice_assign->institute_id = $request->institute_id;
            $notice_assign->course_id = $request->course_id;
            $notice_assign->status=$request->status;
            $notice_assign->created_by=Auth::id();
            $notice_assign->save();

            $institute = Institutes::where('id',$request->institute_id)->first();

            if($institute->type == 1)
            {
                if (NoticeFaculty::where('notice_assign_id', $notice_assign->id)->first()) {
                    NoticeFaculty::where('notice_assign_id', $notice_assign->id)->delete();
                }

                if($request->faculty_id)
                {
                    foreach ($request->faculty_id as $key => $value) {
                        if($value=='')continue;
                        NoticeFaculty::insert(['notice_assign_id' => $notice_assign->id,'notice_id' => $notice_assign->notice_id, 'faculty_id' => $value]);
                    }
                }

            }
            else
            {

                if (NoticeDiscipline::where('notice_assign_id', $notice_assign->id)->first()) {
                    NoticeDiscipline::where('notice_assign_id', $notice_assign->id)->delete();
                }

                if($request->subject_id)
                {
                    foreach ($request->subject_id as $key => $value) {
                        if($value=='')continue;
                        NoticeDiscipline::insert(['notice_assign_id' => $notice_assign->id,'notice_id' => $notice_assign->notice_id, 'subject_id' => $value]);
                    }
                }

            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\NoticeAssignController@index');
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
        $notice_assign=NoticeAssign::select('notice_assigns.*')->find($id);
        return view('admin.notice_assign.show',['notice_assign'=>$notice_assign]);
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
        $notice_assign = NoticeAssign::find($id);
        $data['notice_assign'] = NoticeAssign::find($id);
        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['notices'] = Notice::where(['type'=>'B'])->orWhere(['type'=>'C'])->pluck('title', 'id');
        
        $institute = Institutes::where('id',$notice_assign->institute_id)->first();
        if($institute)$institute_type = $institute->type;
        else $institute_type = null;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties':'courses-subjects';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$notice_assign->institute_id)->pluck('name', 'id');
        
        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$notice_assign->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$notice_assign->faculty_id)->pluck('name', 'id');

            $notice_assign_faculties = NoticeFaculty::where('notice_assign_id',$id)->get();
            $selected_faculties = array();
            foreach($notice_assign_faculties as $faculty)
            {
                $selected_faculties[] = $faculty->faculty_id;
            }

            $data['selected_faculties'] = collect($selected_faculties);

        }else{
            $data['subjects'] = Subjects::where('course_id',$notice_assign->course_id)->pluck('name', 'id');

            $notice_assign_disciplines = NoticeDiscipline::where('notice_assign_id',$id)->get();
            $selected_subjects = array();
            foreach($notice_assign_disciplines as $notice_assign_discipline)
            {
                $selected_subjects[] = $notice_assign_discipline->subject_id;
            }

            $data['selected_subjects'] = collect($selected_subjects);
        }

        $data['module_name'] = 'Notice Assign';
        $data['title'] = 'Notice Assign Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.notice_assign.edit', $data);
        
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
            'notice_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
        
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\NoticeAssignController@edit',[$id])->withInput();
        }

        $notice_assign = NoticeAssign::find($id);

        if($notice_assign->notice_id != $request->notice_id || $notice_assign->institute_id != $request->institute_id || $notice_assign->course_id != $request->course_id) {

            if (NoticeAssign::where(['notice_id'=>$request->notice_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Notice assignment already exists');
                return redirect()->action('Admin\NoticeAssignController@edit',[$id])->withInput();
            }

        }

        $notice_assign->notice_id = $request->notice_id;
        $notice_assign->institute_id = $request->institute_id;
        $notice_assign->course_id = $request->course_id;
        $notice_assign->status=$request->status;
        $notice_assign->updated_by=Auth::id();
        $notice_assign->push();

        $institute = Institutes::where('id',$request->institute_id)->first();

        if($institute->type == 1)
        {
            if (NoticeFaculty::where('notice_assign_id', $notice_assign->id)->first()) {
                NoticeFaculty::where('notice_assign_id', $notice_assign->id)->delete();
            }

            if($request->faculty_id)
            {
                foreach ($request->faculty_id as $key => $value) {
                    if($value=='')continue;
                    NoticeFaculty::insert(['notice_assign_id' => $notice_assign->id,'notice_id' => $notice_assign->notice_id, 'faculty_id' => $value]);
                }
            }

        }
        else
        {

            if (NoticeDiscipline::where('notice_assign_id', $notice_assign->id)->first()) {
                NoticeDiscipline::where('notice_assign_id', $notice_assign->id)->delete();
            }

            if($request->subject_id)
            {
                foreach ($request->subject_id as $key => $value) {
                    if($value=='')continue;
                    NoticeDiscipline::insert(['notice_assign_id' => $notice_assign->id,'notice_id' => $notice_assign->notice_id, 'subject_id' => $value]);
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
        NoticeAssign::destroy($id); // 1 way
        if (NoticeFaculty::where('notice_assign_id', $id)->first()) {
            NoticeFaculty::where('notice_assign_id', $id)->delete();
        }
        if (NoticeDiscipline::where('notice_assign_id', $id)->first()) {
            NoticeDiscipline::where('notice_assign_id', $id)->delete();
        }
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\NoticeAssignController@index');
    }
}  