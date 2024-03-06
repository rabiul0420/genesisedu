<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\TravelQuestion;
use App\TravelQAnswer;
use Illuminate\Support\Facades\Gate;
use Session;
use Auth;
use Validator;


class TravelQuestionController extends Controller
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


        if (! Gate::allows('Travel Question')) {
            return abort(404);
        }

        $travel_question = TravelQuestion::get();

        $title = 'GENESIS Admin : Travel Question List';

        return view('admin.travel_question.list',['travel_question'=>$travel_question,'title'=>$title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*if (! Gate::allows('Travel Question Add')) {
            return abort(404);
        }*/


        $title = 'Genesis Admin : Travel Question Create';

        return view('admin.travel_question.create',['title'=>$title]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        if (TravelQuestion::where('title',$request->title)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Travel question already exists');
            return redirect()->action('Admin\TravelQuestionController@create')->withInput();
        }

        else{
            $validator = Validator::make($request->all(), [
                'sort_order' => [
                    'required','string', 'min:1', 'regex:/^[0-9]*$/i'
                ]
            ]);

            if ($validator->fails()){
                Session::flash('message', "Please enter number ");
                return back()->withInput();
            }

            if($request->type==2){
                if($request->q_title>=$request->q_value){
                    Session::flash('message', "Start(Day) must be less than Ending(Day)");
                    return back()->withInput();
                }
            }


            $travel_question = new TravelQuestion;
            $travel_question->title = $request->title;
            $travel_question->dependency_id = $request->dependency_id;
            $travel_question->category = $request->category;
            $travel_question->type = $request->type;
            $travel_question->sort_order = $request->sort_order;
            $travel_question->indentifier = $request->indentifier;
            $travel_question->status = $request->status;

            $travel_question->save();

            if($request->type==2){
                $travel_question_answer = new TravelQAnswer;
                $travel_question_answer->question_id = $travel_question->id;
                $travel_question_answer->q_title = $request->q_title;
                $travel_question_answer->q_value = $request->q_value;
                $travel_question_answer->save();
            }


            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\TravelQuestionController@index');
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
        if (! Gate::allows('Org. Details')) {
            return abort(404);
        }

        $organization = Organization::find($id);
        return view('admin.organizations.show',['organization'=>$organization]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('Travel Question Edit')) {
            return abort(404);
        }



        $travel_question = TravelQuestion::find($id);


        $TravelQuestion = (new TravelQuestion())->getTable();
        $TravelQAnswer = (new TravelQAnswer())->getTable();

        $appAdmin = 'appAdmin';


        /*$travel_answer = DB::table($appAdmin.'.'.$TravelQAnswer.' as d1')
            ->leftjoin($appAdmin.'.'.$TravelQuestion.' as d2', 'd2.id', '=','d1.question_id')
            ->where('d1.question_id', '!=',$id)
            ->where('d2.category',$travel_question->category)
            ->get();*/


        $travel_answer = TravelQAnswer::select('id','q_title','question_id')
            ->where('question_id', '!=',$id)
            ->get();

        $title = 'GENESIS Admin : Travel Question Edit';

        return view('admin.travel_question.edit',['travel_question'=>$travel_question,'travel_answer'=>$travel_answer,'title'=>$title]);
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
            'sort_order' => [
                'required','string', 'min:1', 'regex:/^[0-9]*$/i'
            ]
        ]);

        if ($validator->fails()){
            Session::flash('message', "Please enter number");
            return back()->withInput();
        }

        if($request->type==2){
            if($request->q_title>=$request->q_value){
                Session::flash('message', "Start(Day) must be less than Ending(Day)");
                return back()->withInput();
            }
        }

        $travel_question = TravelQuestion::find($id);
        $travel_question->title = $request->title;
        $travel_question->dependency_id = $request->dependency_id;
        $travel_question->category = $request->category;
        $travel_question->type = $request->type;
        $travel_question->sort_order = $request->sort_order;
        $travel_question->indentifier = $request->indentifier;
        $travel_question->status = $request->status;

        $travel_question->save();

        if($request->type==2){
            if($request->a_id){
                $travel_question_answer = TravelQAnswer::find($request->a_id);
            }else{
                $travel_question_answer = new TravelQAnswer;
            }

            $travel_question_answer->question_id = $travel_question->id;
            $travel_question_answer->q_title = $request->q_title;
            $travel_question_answer->q_value = $request->q_value;
            $travel_question_answer->save();
        }

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
        if (! Gate::allows('Travel Question Delete')) {
            return abort(404);
        }

        $travel_question = TravelQuestion::find(Auth::id());


        TravelQuestion::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\TravelQuestionController@index');
    }





}
