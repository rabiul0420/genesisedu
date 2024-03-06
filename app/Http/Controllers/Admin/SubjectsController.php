<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Subjects;
use Illuminate\Http\Request;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Faculty_subject;
use App\Question;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class SubjectsController extends Controller
{
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

        $subjects = Subjects::get();
        $title = 'Discipline List';
        return view('admin.settings.subject_list',['subjects'=>$subjects,'title'=>$title]);
    }

    public function discipline_list() {
        $discipline_list = DB::table('subjects as d1' )
        ->leftjoin('institutes as d2', 'd1.institute_id', '=','d2.id' )
        ->leftjoin('courses as d3', 'd1.course_id', '=','d3.id')
        ->leftjoin('faculties as d4', 'd1.faculty_id', '=','d4.id');

        $discipline_list->select(
            'd1.id as id',
            'd1.name as discipline_name',
            'd1.subject_omr_code as omr_code',
            'd2.name as institute_name',
            'd3.name as course_name',
            'd4.name as faculty_name',
            'd1.show_in_combined as show_combined',
        );
        return DataTables::of($discipline_list)
            ->addColumn('action', function ($discipline_list) {
                return view('admin.settings.subject_ajax_list',(['discipline_list'=>$discipline_list]));
            })

            ->addColumn('show_combined',function($discipline_list){
                return '<span style="color:' .( $discipline_list->show_combined == 1 ? 'green;':'red;' ).' font-weight: bold; font-size: 16px;  ">'
                        . ($discipline_list->show_combined == 1 ? '&check;':'&times;') .
                    '</span>';
            })
        
            ->rawColumns(['action','show_combined'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $institute = Institutes::get()->pluck('name', 'id');
        $title = 'Genesis Admin : Discipline Create';

        return view('admin.settings.subject_create',(['institute'=>$institute,'title'=>$title]));
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
            'subject_omr_code' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\SubjectsController@create')->withInput();
        }

        $subject = new Subjects();
        $subject->name = $request->name;
        $subject->subject_omr_code = $request->subject_omr_code;
        $subject->institute_id = $request->institute_id;
        $subject->course_id = $request->course_id;
        $subject->faculty_id = $request->faculty_id;
        $subject->show_in_combined = $request->show_in_combined ?? 0;

        $subject->status = $request->status;
        $subject->created_by = Auth::id();

        $subject->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\SubjectsController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=Questions::select('users.*')
            ->find($id);
        return view('admin.questions.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=Questions::find(Auth::id());
         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/

        $subject = Subjects::find($id);

        $data['institute'] = Institutes::pluck('name', 'id');

        $data['course'] = Courses::get()->where('institute_id', $subject->institute_id)->pluck('name', 'id');
        $data['faculty'] = Faculty::get()->where('course_id', $subject->course_id)->pluck('name', 'id');
        $data['title'] = 'Discipline Edit';

        return view('admin.settings.subject_edit', ['subject'=>$subject], $data);
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
            'subject_omr_code' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        /*   if($request->book_name != $question->book_name){
               if (Questions::where('book_name',$request->book_name)->exists()){
                   Session::flash('class', 'alert-danger');
                   session()->flash('message','This Chapter already exists');
                   return redirect()->back()->withInput();
               }
           }*/

        $subject = Subjects::find($id);
        $subject->name=$request->name;
        $subject->subject_omr_code = $request->subject_omr_code;
        $subject->institute_id = $request->institute_id;
        $subject->course_id=$request->course_id;
        $subject->faculty_id=$request->faculty_id;
        $subject->status = $request->status;
        $subject->show_in_combined = $request->show_in_combined ?? 0;
        $subject->updated_by = Auth::id();
        $subject->push();
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
        /*$user=Questions::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        Subjects::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SubjectsController@index');
    }

}
