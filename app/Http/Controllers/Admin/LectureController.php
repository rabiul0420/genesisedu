<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Lecture;
use App\Courses;
use Session;
use Auth;
use Validator;


class LectureController extends Controller
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
        $lectures = Lecture::get();
        $title = 'Lecture List';
        return view('admin.lecture.list',['lectures'=>$lectures,'title'=>$title]);
        
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
        

        $courses = Courses::get()->pluck('name', 'id');


        $title = 'Lecture Create';

        return view('admin.lecture.create',(['courses'=>$courses,'title'=>$title]));
        //echo "lecture create";
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
            'course_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter valid data!!!');
            return redirect()->action('Admin\LectureController@create')->withInput();
        } 

        if (Lecture::where('name',$request->name)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This lecture  already exists');
            return redirect()->action('Admin\LectureController@create')->withInput();
        }

        $lecture = new Lecture();
        $lecture->name=$request->name;
        $lecture->course_id=$request->course_id;
        $lecture->status=1;
        $lecture->created_by=Auth::id();
        $lecture->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\LectureController@index');
        
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lecture=Lecture::select('users.*')->find($id);
        return view('admin.lecture.show',['lecture'=>$lecture]);
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
        $lecture=Lecture::find($id);
        $course = Courses::get()->pluck('name', 'id');
        $title = 'Admin : lecture Edit';
        return view('admin.lecture.edit',['course'=>$course, 'lecture'=>$lecture, 'title'=>$title]);
        
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
            'course_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }
        
    //    $subject=Subjects::find($id);
    //    if($request->book_name != $subject->book_name){
    //        if (Subjects::where('book_name',$request->book_name)->exists()){
    //            Session::flash('class', 'alert-danger');
    //            session()->flash('message','This Subject already exists');
    //            return redirect()->back()->withInput();
    //        }
    //    }

        $lecture=Lecture::find($id);

        if($request->name != $lecture->name){
            if (Lecture::where('name',$request->name)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This lecture already exists');
                return redirect()->back()->withInput();
            }
        }
        
        $lecture->name=$request->name;
        $lecture->course_id=$request->course_id;
        $lecture->push();
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

       /* if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        Lecture::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureController@index');
    }
}