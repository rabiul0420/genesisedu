<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Answers;
use App\Questions;
use Session;
use Auth;
use Validator;


class AnswersController extends Controller
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

        $answers = Answers::get();

        $title = 'Genesis Admin : Discipline List';

        return view('admin.answers.list',['answers'=>$answers,'title'=>$title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // $user=Answers::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $questions = Questions::get()->pluck('question_title', 'id');

        $title = 'Genesis Admin : Administrator Create';

        return view('admin.answers.create',(['questions'=>$questions,'title'=>$title]));
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
            'question_id' => ['required'],
            'answer_title' => ['required'],
            'tf' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\AnswersController@create')->withInput();
        }



    /*    if (Answers::where('subject_name',$request->subject_name)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Discipline  already exists');
            return redirect()->action('Admin\AnswersController@create')->withInput();
        }*/



            $answer = new Answers;
            $answer->question_id = $request->question_id;
            $answer->answer_title = $request->answer_title;
            $answer->tf=$request->tf;
            $answer->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\AnswersController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=Answers::select('users.*')
                   ->find($id);
        return view('admin.answers.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=Answers::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $answer=Answers::find($id);


        $title = 'GENESIS Admin : Administrator Edit';


        return view('admin.answers.edit',['answer'=>$answer,'title'=>$title]);
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
            'book_name' => ['required'],
            'price' => ['required'],
            'coupon_price' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid phone number");
            return back()->withInput();
        }

        $answer=Answers::find($id);

        if($request->book_name != $answer->book_name){
            if (Answers::where('book_name',$request->book_name)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Discipline already exists');
                return redirect()->back()->withInput();
            }
        }


        $answer->book_name=$request->book_name;
        $answer->coupon_price=$request->coupon_price;
        $answer->price = $request->price;
        $answer->status=$request->status;


        $answer->push();


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
        $user=Answers::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }

        Answers::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\AnswersController@index');
    }





}
