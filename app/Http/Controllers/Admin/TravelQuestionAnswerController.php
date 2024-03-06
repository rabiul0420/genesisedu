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


class TravelQuestionAnswerController extends Controller
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

        $travel_question_answer = TravelQAnswer::get();

        return view('admin.travel_question_answer.list',['travel_question_answer'=>$travel_question_answer]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('Travel Question Add')) {
            return abort(404);
        }

        $travel_question = TravelQuestion::where('status',1)->pluck('title','id');

        $title = 'GENESIS Admin : Travel Question Answer Create';

        return view('admin.travel_question_answer.create',['travel_question'=>$travel_question,'title'=>$title]);
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
            'sort_order' => [
                'required','string', 'min:1', 'regex:/^[0-9]*$/i'
            ]
        ]);

        if ($validator->fails()){
            Session::flash('message', "Please enter number");
            return back()->withInput();
        }

        if (TravelQAnswer::where('q_title',$request->q_title)->where('question_id',$request->question_id)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This answer already exists');
            return redirect()->action('Admin\TravelQuestionAnswerController@create')->withInput();
        }


        $travel_question_answer = new TravelQAnswer;

        $travel_question_answer->question_id = $request->question_id;
        $travel_question_answer->q_title = $request->q_title;
        $travel_question_answer->q_value = $request->q_value;
        $travel_question_answer->sort_order = $request->sort_order;

        $travel_question_answer->save();

        Session::flash('message', 'Record has been added successfully');
        return redirect('admin/travel-question/'.$request->question_id.'/edit');
        //return back();
       // return redirect()->action('Admin\TravelQuestionAnswerController@index');

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

        $travel_question_answer = TravelQAnswer::find($id);
        $travel_question = TravelQuestion::where('status',1)->pluck('title','id');

        $title = 'GENESIS Admin : Travel Question Answer Edit';

        return view('admin.travel_question_answer.edit',['travel_question_answer'=>$travel_question_answer,'travel_question'=>$travel_question,'title'=>$title]);
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

        $travel_question_answer = TravelQAnswer::find($id);

        if($request->q_title != $travel_question_answer->q_title){
            if (TravelQAnswer::where('q_title',$request->q_title)->where('question_id',$request->question_id)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This answer already exists');
                return redirect()->back()->withInput();
            }
        }

        $travel_question_answer->question_id = $request->question_id;
        $travel_question_answer->q_title = $request->q_title;
        $travel_question_answer->q_value = $request->q_value;
        $travel_question_answer->sort_order = $request->sort_order;

        $travel_question_answer->save();

        Session::flash('message', 'Answer has been updated successfully');

        return redirect('admin/travel-question/'.$request->question_id.'/edit');

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('Travel Question Answer Delete')) {
            return abort(404);
        }

        $question_id = TravelQAnswer::where('id',$id)->value('question_id');

        TravelQAnswer::destroy($id); // 1 way
        Session::flash('message', 'Answer has been deleted successfully');
        return redirect('admin/travel-question/'.$question_id.'/edit');
    }





}
