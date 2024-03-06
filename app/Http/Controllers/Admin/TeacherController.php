<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Teacher;

use Session;
use Auth;
use Validator;


class TeacherController extends Controller
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
        $teachers = Teacher::get();
        $title = 'Teacher List';
        return view('admin.teacher.list',['teachers'=>$teachers,'title'=>$title]);

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
        
        $gender = array(''=>'Select Gender', 'Male'=>'Male', 'Female'=>'Female');
        $title = 'Teacher Create';

        return view('admin.teacher.create',(['gender'=>$gender, 'title'=>$title]));
        
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
            'bmdc_no' => ['required'],
            'phone' => ['required'],
            'gender' => ['required'],
            'designation' => ['required'],
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter valid data!!!');
            return redirect()->action('Admin\TeacherController@create')->withInput();
        }
      
        if (Teacher::where('bmdc_no',$request->bmdc_no)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This BMDC No. already exists');
            return redirect()->action('Admin\TeacherController@create')->withInput();
        }
        else{

            $teacher = new Teacher;
            $teacher->name=$request->name;
            $teacher->bmdc_no=$request->bmdc_no;
            $teacher->designation=$request->designation;
            $teacher->phone=$request->phone;
            $teacher->email=$request->email;
            $teacher->gender=$request->gender;
            $teacher->nid=$request->nid;
            $teacher->passport=$request->passport;
            $teacher->address=$request->address;
            $teacher->status=1;
            $teacher->save();

            Session::flash('message', 'Record has been added successfully');
            return redirect()->action('Admin\TeacherController@index');
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
        $data['teacher'] = Teacher::find($id);
        $data['title'] = 'Teacher Edit';
        $data['gender'] = array(''=>'Select Gender', 'Male'=>'Male', 'Female'=>'Female');
        return view('admin.teacher.edit', $data);
        
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
            'bmdc_no' => ['required'],
            'phone' => ['required'],
            'gender' => ['required'],
            'designation' => ['required'],
        ]);



        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter valid data!!!');
            return redirect()->action('Admin\TeacherController@edit', [$id])->withInput();
        }
        
    //    $subject=Subjects::find($id);
    //    if($request->book_name != $subject->book_name){
    //        if (Subjects::where('book_name',$request->book_name)->exists()){
    //            Session::flash('class', 'alert-danger');
    //            session()->flash('message','This Discipline already exists');
    //            return redirect()->back()->withInput();
    //        }
    //    }

        /*
        $topic=Topic::find($id);
        if($request->topic_name != $topic->name){
            if (Topic::where('name',$request->topic_name)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Topic already exists');
                return redirect()->back()->withInput();
            }
        }
        */

        $teacher = Teacher::find($id);

        if($teacher->bmdc_no != $request->bmdc_no) {

            if (Teacher::where('bmdc_no', $request->bmdc_no)->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This bmdc no already exists');
                return redirect()->action('Admin\TeacherController@edit', [$id])->withInput();
            }

        }

        $teacher->name = $request->name;
        $teacher->designation=$request->designation;
        $teacher->bmdc_no = $request->bmdc_no;
        $teacher->phone = $request->phone;
        $teacher->email = $request->email;
        $teacher->gender = $request->gender;
        $teacher->nid = $request->nid;
        $teacher->passport = $request->passport;
        $teacher->address = $request->address;
        $teacher->push();

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
        Teacher::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\TeacherController@index');
    }
}
