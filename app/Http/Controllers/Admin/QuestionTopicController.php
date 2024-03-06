<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Subjects;
use Illuminate\Http\Request;
use App\QuestionChapter;
use App\Books;
use App\QuestionSubject;
use App\QuestionTopic;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class QuestionTopicController extends Controller
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

        $data['topics'] = QuestionTopic::with('chapter','subject')->get();
        
        // $data['topics'] = Auth::user( )->question( )->topics->get( );

        $data['module_name'] = 'Question Topic';
        $data['title'] = 'Question Topic List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.question_topic.list',$data);
    }

    public function question_topic_list() {
        $question_topic_list = DB::table('ques_topics as d1' )
        ->leftjoin('ques_chapters as d2', 'd1.chapter_id', '=','d2.id' )
        ->leftjoin('ques_subjects as d3', 'd1.subject_id', '=','d3.id' );

        $question_topic_list->select(
            'd1.id as id',
            'd1.topic_name as topic_name',
            'd2.chapter_name as chapter_name',
            'd3.subject_name as subject_name',
        );
        return DataTables::of($question_topic_list)
            ->addColumn('action', function ($question_topic_list) {
                return view('admin.question_topic.question_topic_ajax_list',(['question_topic_list'=>$question_topic_list]));
            })

        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // $user=topics::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['subjects'] = QuestionSubject::pluck('subject_name', 'id');
        $data['module_name'] = 'Question Topic';
        $data['title'] = 'Question Topic Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.question_topic.create',$data);
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
            'chapter_id' => ['required'],
            'topic_name' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\QuestionTopicController@create')->withInput();
        }



        if (QuestionTopic::where(['subject_id'=>$request->subject_id,'chapter_id'=>$request->chapter_id,'topic_name'=>$request->topic_name])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Topic  already exists');
            return redirect()->action('Admin\QuestionTopicController@create')->withInput();
        }


            $topic = new QuestionTopic;
            $topic->subject_id=$request->subject_id;
            $topic->topic_name=$request->topic_name;
            $topic->chapter_id = $request->chapter_id;
            $topic->created_by =Auth::id();

            $topic->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\QuestionTopicController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=QuestionTopic::select('users.*')
                   ->find($id);
        return view('admin.question_topic.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=Chapters::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/


        $data['topic']=QuestionTopic::find($id);
        $data['subjects'] = QuestionSubject::pluck('subject_name', 'id');
        $data['chapters'] = QuestionChapter::where(['subject_id'=>$data['topic']->subject_id])->pluck('chapter_name', 'id');

        $data['module_name'] = 'Question Topic';
        $data['title'] = 'Question Topic Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.question_topic.edit',$data);
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
            'chapter_id' => ['required'],
            'topic_name' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data!!!");
            return redirect()->action('Admin\QuestionTopicController@edit',[$id])->withInput();
        }

        $topic = QuestionTopic::find($id);

        if($topic->topic_name != $request->topic_name || $topic->subject_id != $request->subject_id || $topic->chapter_id != $request->chapter_id)
        {
            if (QuestionTopic::where(['subject_id'=>$request->subject_id,'chapter_id'=>$request->chapter_id,'topic_name'=>$request->topic_name])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Topic  already exists');
                return redirect()->action('Admin\QuestionTopicController@edit',[$id])->withInput();
            }
        }

        $topic->subject_id=$request->subject_id;
        $topic->topic_name=$request->topic_name;
        $topic->chapter_id = $request->chapter_id;
        $topic->updated_by =Auth::id();

        $topic->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\QuestionTopicController@edit',[$id])->withInput();

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $question_topic = QuestionTopic::find($id);
        $question_topic->deleted_by=Auth::id();
        $question_topic->push();
        // if(!$user->hasRole('Admin')){
            //     return abort(404);
            // }
            
        $user=QuestionTopic::find(Auth::id());

        QuestionTopic::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\QuestionTopicController@index');
    }





}
