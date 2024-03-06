<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\QuestionSubject;
use App\Books;
use App\Courses;
use App\ReferenceCourse;
use App\ReferenceFaculty;
use App\ReferenceInstitute;
use Session;
use Auth;
use Validator;


class ReferenceFacultyController extends Controller
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




//        $data['subjects'] = QuestionSubject::get();
        $data['reference_faculties'] = ReferenceFaculty::get();

        $data['module_name'] = 'Reference Faculty';
        $data['title'] = 'Reference Faculty';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.reference_faculty.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // $user=QuestionSubject::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/


        $data['courses'] = ReferenceCourse::pluck('name','id');
        $data['reference_faculty'] = ReferenceFaculty::pluck('name','id');
        $data['module_name'] = 'Reference Faculty';
        $data['title'] = 'Reference Faculty Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.reference_faculty.create',$data);
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
            'faculty_name' => ['required'],
            'course_id' => ['required'],
            'reference_code' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please input valid data');
            return redirect()->action('Admin\ReferenceFacultyController@create')->withInput();
        }



        if (ReferenceFaculty::where(['name'=>$request->name])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Institute  already exists');
            return redirect()->action('Admin\ReferenceFacultyController@create')->withInput();
        }

        else{

            $faculty = new ReferenceFaculty();
            $faculty->timestamps = false;
            $faculty->name = $request->faculty_name;
            $faculty->course_id = $request->course_id;
            $faculty->reference_code = $request->reference_code;
            $faculty->created_by =Auth::id();
            $faculty->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\ReferenceFacultyController@index');
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
        $user=ReferenceFaculty::select('users.*')
                   ->find($id);
        return view('admin.reference_faculty.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=QuestionSubject::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['reference_faculties']=ReferenceFaculty::find($id);
        $data['module_name'] = 'Reference Faculty';
        $data['title'] = 'Reference Faculty Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.reference_faculty.edit',$data);
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
            'faculty_name' => ['required'],
            'reference_code' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data");
            return redirect()->action('Admin\ReferenceFacultyController@edit',[$id])->withInput();
        }

        $faculty=ReferenceFaculty::find($id);
        $faculty->name=$request->faculty_name;
        $faculty->reference_code=$request->reference_code;
        $faculty->updated_by =Auth::id();

        $faculty->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\ReferenceFacultyController@edit',[$id])->withInput();

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        // if(!$user->hasRole('Admin')){
            //     return abort(404);
            // }
        $question_institute = ReferenceFaculty::find($id);
        $question_institute->deleted_by=Auth::id();
        $question_institute->push();
            
        $user=ReferenceFaculty::find(Auth::id());

        ReferenceFaculty::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ReferenceFacultyController@index');
    }





}
