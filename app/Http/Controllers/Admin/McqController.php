<?php

namespace App\Http\Controllers\Admin;
use App\Doctors;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Question;
use App\Question_ans;

use Session;
use Auth;
use Validator;
use Yajra\DataTables\DataTables;


class McqController extends Controller
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

        $title = 'MCQ Question';
        return view('admin.mcq.list',['title'=>$title]);
        //echo "OK";
    }


    public function mcq_list()
    {
        $mcq_list = Question::where('type', 1)->orderBy('id', 'desc')->select('*');

        return Datatables::of($mcq_list)
            ->addColumn('action', function ($mcq_list) {
                return view('admin.mcq.ajax_list',(['mcq_list'=>$mcq_list]));
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
       // $user=Questions::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        
        $title = 'Genesis Admin : MCQ Question Create';

        return view('admin.mcq.create', (['title'=>$title]));
           
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
            'question_title' => ['required'],
            'question_a' => ['required'],
            'answer_a' => ['required'],
            'question_b' => ['required'],
            'answer_b' => ['required'],
            'question_c' => ['required'],
            'answer_c' => ['required'],
            'question_d' => ['required'],
            'answer_d' => ['required'],
            'question_e' => ['required'],
            'answer_e' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\McqController@create')->withInput();
        }

        /*if (Questions::where('book_name',$request->book_name)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Chapter  already exists');
            return redirect()->action('Admin\QuestionsController@create')->withInput();
        }*/

            $question = new Question;
            $question->question_title = $request->question_title;
            $question->type = 1;
            $question->discussion = $request->discussion;
            // $question->reference = $request->reference;
            $question->status = 1;
            $question->created_by = Auth::id();
            $question->save();

            $question_id = $question->id;

            $answer = new Question_ans; $answer->question_id = $question_id; $answer->answer = $request->question_a; $answer->sl_no = 'A'; $answer->correct_ans = $request->answer_a; $answer->save();
            $answer = new Question_ans; $answer->question_id = $question_id; $answer->answer = $request->question_b; $answer->sl_no = 'B'; $answer->correct_ans = $request->answer_b; $answer->save();
            $answer = new Question_ans; $answer->question_id = $question_id; $answer->answer = $request->question_c; $answer->sl_no = 'C'; $answer->correct_ans = $request->answer_c; $answer->save();
            $answer = new Question_ans; $answer->question_id = $question_id; $answer->answer = $request->question_d; $answer->sl_no = 'D'; $answer->correct_ans = $request->answer_d; $answer->save();
            $answer = new Question_ans; $answer->question_id = $question_id; $answer->answer = $request->question_e; $answer->sl_no = 'E'; $answer->correct_ans = $request->answer_e; $answer->save();


            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\McqController@index');

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
        return view('admin.mcq.show',['user'=>$user]);
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


        $questions = Question::find($id);
      
        $title = 'MCQ Question Edit';

        return view('admin.mcq.edit',['questions'=>$questions, 'title'=>$title]);
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
            'question_title' => ['required'],
            'question_a' => ['required'],
            'answer_a' => ['required'],
            'question_b' => ['required'],
            'answer_b' => ['required'],
            'question_c' => ['required'],
            'answer_c' => ['required'],
            'question_d' => ['required'],
            'answer_d' => ['required'],
            'question_e' => ['required'],
            'answer_e' => ['required']
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
        $question = Question::find($id);
        $question->question_title=$request->question_title;
        $question->discussion=$request->discussion;
        // $question->reference=$request->reference;
        $question->push();

        $qa_id = $request->qa_id; $question_ans = Question_ans::find($qa_id); $question_ans->answer=$request->question_a; $question_ans->correct_ans=$request->answer_a; $question_ans->push();
        $qb_id = $request->qb_id; $question_ans = Question_ans::find($qb_id); $question_ans->answer=$request->question_b; $question_ans->correct_ans=$request->answer_b; $question_ans->push();
        $qc_id = $request->qc_id; $question_ans = Question_ans::find($qc_id); $question_ans->answer=$request->question_c; $question_ans->correct_ans=$request->answer_c; $question_ans->push();
        $qd_id = $request->qd_id; $question_ans = Question_ans::find($qd_id); $question_ans->answer=$request->question_d; $question_ans->correct_ans=$request->answer_d; $question_ans->push();
        $qe_id = $request->qe_id; $question_ans = Question_ans::find($qe_id); $question_ans->answer=$request->question_e; $question_ans->correct_ans=$request->answer_e; $question_ans->push();

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
        /*$user=Question::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        Question::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\McqController@index');
    }





}
