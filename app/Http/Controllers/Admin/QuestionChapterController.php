<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\QuestionSubject;
use Illuminate\Http\Request;
use App\QuestionChapter;
use Session;
use Auth;
use Validator;


class QuestionChapterController extends Controller
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

//        $data['chapters'] = QuestionChapter::get();
        $data['chapters'] =  Auth::user( )->question( )->chapters->get( );;

        $data['module_name'] = 'Question Subject';
        $data['title'] = 'Question Subject List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);


        return view('admin.question_chapter.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // $user=QuestionChapter::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['subjects'] = QuestionSubject::pluck('subject_name', 'id');
        $data['module_name'] = 'Question Chapter';
        $data['title'] = 'Question Chapter Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.question_chapter.create',$data);
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
            'subject_id' => ['required'],
            'chapter_name' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\QuestionChapterController@create')->withInput();
        }

        if (QuestionChapter::where(['subject_id'=>$request->subject_id,'chapter_name'=>$request->chapter_name])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Chapter  already exists');
            return redirect()->action('Admin\QuestionChapterController@create')->withInput();
        }

        $chapter = new QuestionChapter;
        $chapter->subject_id=$request->subject_id;
        $chapter->chapter_name=$request->chapter_name;
        $chapter->created_by =Auth::id();

        $chapter->save();

        Session::flash('message', 'Record has been added successfully');

        //return back();

        return redirect()->action('Admin\QuestionChapterController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=QuestionChapter::select('users.*')
                   ->find($id);
        return view('admin.question_chapter.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=QuestionChapter::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/


        $data['chapter'] = QuestionChapter::find($id);

        $data['subjects'] = QuestionSubject::pluck('subject_name', 'id');
        $data['module_name'] = 'Question Chapter';
        $data['title'] = 'Question Chapter Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';

        return view('admin.question_chapter.edit',$data);
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
            'subject_id' => ['required'],
            'chapter_name' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid input");
            return back()->withInput();
        }

        $chapter=QuestionChapter::find($id);

        if( $chapter->chapter_name != $request->chapter_name || $chapter->subject_id != $request->subject_id )
        {
            if (QuestionChapter::where(['subject_id'=>$request->subject_id,'chapter_name'=>$request->chapter_name])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Chapter  already exists');
                return redirect()->action('Admin\QuestionChapterController@edit',[$id])->withInput();
            }

        }


        $chapter->subject_id=$request->subject_id;
        $chapter->chapter_name=$request->chapter_name;
        $chapter->updated_by =Auth::id();

        $chapter->push();


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
            $question_chapter = QuestionChapter::find($id);
            $question_chapter->deleted_by=Auth::id();
            $question_chapter->push();  
            
        $user=QuestionChapter::find(Auth::id());

        QuestionChapter::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\QuestionChapterController@index');
    }





}
