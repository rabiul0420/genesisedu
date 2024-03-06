<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\QuestionSubject;
use App\Books;
use App\ReferenceCourse;
use App\ReferenceInstitute;
use App\ReferenceSession;
use Session;
use Auth;
use Validator;


class ReferenceSessionController extends Controller
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
        $data['reference_sessions'] = ReferenceSession::get();

        $data['module_name'] = 'Reference Session';
        $data['title'] = 'Reference Session';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.reference_session.list',$data);
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
        $data['reference_sessions'] = ReferenceSession::pluck('name','id');
        $data['module_name'] = 'Reference Session';
        $data['title'] = 'Reference Session Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.reference_session.create',$data);
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
            'course_id' => ['required'],
            'session_name' => ['required'],
            'reference_code' => ['required'],
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please input valid data');
            return redirect()->action('Admin\ReferenceSessionController@create')->withInput();
        }
        else{
            $ref_session = new ReferenceSession();
            $ref_session->timestamps = false;
            $ref_session->course_id = $request->course_id;
            $ref_session->name = $request->session_name;
            $ref_session->reference_code = $request->reference_code;
            $ref_session->created_by =Auth::id();
            $ref_session->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\ReferenceSessionController@index');
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
        $user=ReferenceSession::select('users.*')
                   ->find($id);
        return view('admin.reference_session.show',['user'=>$user]);
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

        $data['reference_sessions']=ReferenceSession::find($id);
        $data['module_name'] = 'Question Institute';
        $data['title'] = 'Question Institute Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.reference_session.edit',$data);
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
            'session_name' => ['required'],
            'reference_code' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data");
            return redirect()->action('Admin\ReferenceSessionController@edit',[$id])->withInput();
        }

        $institute=ReferenceSession::find($id);

        if($institute->name != $request->name)
        {
            if (ReferenceSession::where(['name'=>$request->name])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Subject  already exists');
                return redirect()->action('Admin\ReferenceSessionController@edit',[$id])->withInput();
            }

        }

        $institute->name=$request->session_name;
        $institute->reference_code=$request->reference_code;
        $institute->updated_by =Auth::id();

        $institute->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\ReferenceSessionController@edit',[$id])->withInput();

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
        $question_institute = ReferenceSession::find($id);
        $question_institute->deleted_by=Auth::id();
        $question_institute->push();
            
        $user=ReferenceSession::find(Auth::id());

        ReferenceSession::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ReferenceSessionController@index');
    }





}
