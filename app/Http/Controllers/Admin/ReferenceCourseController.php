<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\QuestionSubject;
use App\Books;
use App\Institutes;
use App\ReferenceCourse;
use App\ReferenceInstitute;
use Session;
use Auth;
use Validator;


class ReferenceCourseController extends Controller
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
        $data['reference_course'] = ReferenceCourse::get();
        $data['module_name'] = 'Reference Course';
        $data['title'] = 'Reference Course';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.reference_course.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        $data['reference_course'] = ReferenceCourse::get();
        $data['institutes'] = ReferenceInstitute::pluck('name','id');

        $data['module_name'] = 'Reference Course';
        $data['title'] = 'Reference Course Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.reference_course.create',$data);
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
            'course_name' => ['required'],
            'institute_id' => ['required'],
            'reference_code' => ['required'],
        ]);
        if ($validator->fails()){
            // dd($validator);
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please input valid data');
            return redirect()->action('Admin\ReferenceCourseController@create')->withInput();
        }


        if (ReferenceCourse::where(['name'=>$request->course_name])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Institute  already exists');
            return redirect()->action('Admin\ReferenceCourseController@create')->withInput();
        }

        else{

            $reference_course = new ReferenceCourse();
            $reference_course->timestamps = false;
            $reference_course->name = $request->course_name;
            $reference_course->institute_id = $request->institute_id;
            $reference_course->type = $request->institute_id == 6 ? 1 : 0;
            $reference_course->reference_code = $request->reference_code;
            $reference_course->created_by =Auth::id();
            
            $reference_course->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\ReferenceCourseController@index');
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
        $user=ReferenceCourse::select('users.*')
                   ->find($id);
        return view('admin.reference_course.show',['user'=>$user]);
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

        $data['reference_courses']=ReferenceCourse::find($id);
        $data['module_name'] = 'Reference Course';
        $data['title'] = 'Reference Course Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.reference_course.edit',$data);
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
            'course_name' => ['required'],
            'reference_code' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data");
            return redirect()->action('Admin\ReferenceCourseController@edit',[$id])->withInput();
        }

        $institute=ReferenceCourse::find($id);

        if($institute->name != $request->name)
        {
            if (ReferenceCourse::where(['name'=>$request->name])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Subject  already exists');
                return redirect()->action('Admin\ReferenceCourseController@edit',[$id])->withInput();
            }

        }

        $institute->name=$request->course_name;
        $institute->reference_code=$request->reference_code;
        $institute->updated_by =Auth::id();

        $institute->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\ReferenceCourseController@edit',[$id])->withInput();

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
        $question_institute = ReferenceCourse::find($id);
        $question_institute->deleted_by=Auth::id();
        $question_institute->push();
            
        $user=ReferenceCourse::find(Auth::id());

        ReferenceCourse::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ReferenceCourseController@index');
    }





}
