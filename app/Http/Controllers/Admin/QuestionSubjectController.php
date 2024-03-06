<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\QuestionSubject;
use App\Books;
use Session;
use Auth;
use Validator;


class QuestionSubjectController extends Controller
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
        $data['subjects'] = Auth::user( )->question( )->subjects->get( );

        $data['module_name'] = 'Question Subject';
        $data['title'] = 'Question Subject';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.question_subject.list',$data);
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

        $data['module_name'] = 'Question Subject';
        $data['title'] = 'Question Subject Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.question_subject.create',$data);
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
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please input valid data');
            return redirect()->action('Admin\QuestionSubjectController@create')->withInput();
        }



        if (QuestionSubject::where(['subject_name'=>$request->subject_name])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Subject  already exists');
            return redirect()->action('Admin\QuestionSubjectController@create')->withInput();
        }

        else{

            $subject = new QuestionSubject;
            $subject->subject_name = $request->subject_name;
            $subject->created_by =Auth::id();
            $subject->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\QuestionSubjectController@index');
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
        $user=QuestionSubject::select('users.*')
                   ->find($id);
        return view('admin.question_subject.show',['user'=>$user]);
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

        $data['subject']=QuestionSubject::find($id);
        $data['module_name'] = 'Question Subject';
        $data['title'] = 'Question Subject Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.question_subject.edit',$data);
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
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data");
            return redirect()->action('Admin\QuestionSubjectController@edit',[$id])->withInput();
        }

        $subject=QuestionSubject::find($id);

        if($subject->subject_name != $request->subject_name)
        {
            if (QuestionSubject::where(['subject_name'=>$request->subject_name])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Subject  already exists');
                return redirect()->action('Admin\QuestionSubjectController@edit',[$id])->withInput();
            }

        }

        $subject->subject_name=$request->subject_name;
        $subject->updated_by =Auth::id();

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
        
        // if(!$user->hasRole('Admin')){
            //     return abort(404);
            // }
        $question_subject = QuestionSubject::find($id);
        $question_subject->deleted_by=Auth::id();
        $question_subject->push();
            
        $user=QuestionSubject::find(Auth::id());

        QuestionSubject::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\QuestionSubjectController@index');
    }





}
