<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Subjects;
use App\Books;
use Session;
use Auth;
use Validator;


class Faculty_subjectController  extends Controller
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

        $subjects = Subjects::get();

        $title = 'Genesis Admin : Discipline List';

        return view('admin.subjects.list',['subjects'=>$subjects,'title'=>$title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user=Subjects::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $books = Books::get()->pluck('book_name', 'id');

        $title = 'Genesis Admin : Administrator Create';

        return view('admin.subjects.create',(['books'=>$books,'title'=>$title]));
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
            'book_id' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\SubjectsController@create')->withInput();
        }



        if (Subjects::where('subject_name',$request->subject_name)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Discipline  already exists');
            return redirect()->action('Admin\SubjectsController@create')->withInput();
        }

        else{

            $subject = new Subjects;
            $subject->subject_name = $request->subject_name;
            $subject->book_id=$request->book_id;
            $subject->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\SubjectsController@index');
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
        $user=Subjects::select('users.*')
            ->find($id);
        return view('admin.subjects.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=Subjects::find(Auth::id());

         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/

        $subject=Subjects::find($id);


        $title = 'GENESIS Admin : Administrator Edit';


        return view('admin.subjects.edit',['subject'=>$subject,'title'=>$title]);
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

        $subject=Subjects::find($id);

        if($request->book_name != $subject->book_name){
            if (Subjects::where('book_name',$request->book_name)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Discipline already exists');
                return redirect()->back()->withInput();
            }
        }


        $subject->book_name=$request->book_name;
        $subject->coupon_price=$request->coupon_price;
        $subject->price = $request->price;
        $subject->status=$request->status;


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
        $user=Subjects::find($id);

        if(!$user->hasRole('Admin')){
            return abort(404);
        }

        Subjects::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SubjectsController@index');
    }





}
