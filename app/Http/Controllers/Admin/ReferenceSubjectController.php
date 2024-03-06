<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\QuestionSubject;
use App\Books;
use App\Courses;
use App\ReferenceCourse;
use App\ReferenceInstitute;
use App\ReferenceSubject;
use Session;
use Auth;
use Validator;


class ReferenceSubjectController extends Controller
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
        $data['reference_subject'] = ReferenceSubject::get();

        $data['module_name'] = 'Reference Subject';
        $data['title'] = 'Reference Subject';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.reference_subject.list',$data);
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

        $data['reference_courses'] = ReferenceCourse::pluck('name','id');
        $data['reference_subjects'] = ReferenceSubject::pluck('name','id');

        $data['module_name'] = 'Reference Subject';
        $data['title'] = 'Reference Subject Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.reference_subject.create',$data);
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
            'subject_name' => ['required'],
            'course_id' => ['required'],
            'reference_code' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please input valid data');
            return redirect()->action('Admin\ReferenceSubjectController@create')->withInput();
        }
        // if (ReferenceSubject::where(['name'=>$request->name])->first()){
        //     Session::flash('class', 'alert-danger');
        //     session()->flash('message','This Institute  already exists');
        //     return redirect()->action('Admin\ReferenceSubjectController@create')->withInput();
        // }

        else{

            $ref_subject = new ReferenceSubject();
            $ref_subject->timestamps = false;
            $ref_subject->name = $request->subject_name;
            $ref_subject->course_id = $request->course_id;
            $ref_subject->reference_code = $request->reference_code;
            $ref_subject->created_by =Auth::id();
            $ref_subject->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\ReferenceSubjectController@index');
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
        $user=ReferenceSubject::select('users.*')
                   ->find($id);
        return view('admin.reference_subject.show',['user'=>$user]);
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

        $data['reference_subjects']=ReferenceSubject::find($id);
        $data['module_name'] = 'Question Institute';
        $data['title'] = 'Question Institute Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.reference_subject.edit',$data);
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
            'subject_name' => ['required'],
            'reference_code' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data");
            return redirect()->action('Admin\ReferenceSubjectController@edit',[$id])->withInput();
        }

        $institute=ReferenceSubject::find($id);

        if($institute->name != $request->name)
        {
            if (ReferenceSubject::where(['name'=>$request->name])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Subject  already exists');
                return redirect()->action('Admin\ReferenceSubjectController@edit',[$id])->withInput();
            }

        }

        $institute->name=$request->subject_name;
        $institute->reference_code=$request->reference_code;
        $institute->updated_by =Auth::id();

        $institute->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\ReferenceSubjectController@edit',[$id])->withInput();

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
        $question_institute = ReferenceSubject::find($id);
        $question_institute->deleted_by=Auth::id();
        $question_institute->push();
            
        $user=ReferenceSubject::find(Auth::id());

        ReferenceSubject::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ReferenceSubjectController@index');
    }





}
