<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Questionlink;
use Validator;
use Session;
use Auth;


class QuestionlinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['questionlink'] =Questionlink::get();
        return view('admin.questionlink.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $data['module_name'] = 'questionlink ';
        $data['title'] = 'Genesis Admin : Questionlink Create';
        return view('admin.questionlink.create',$data);
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
            'title' => ['required'],
            'question_link'=> ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Enter Fields Correctly');
            return redirect()->action('Admin\QuestionlinkController@create')->withInput();
        }

        $questionlink = new Questionlink;
        $questionlink->title = $request->title;
        $questionlink->question_link = $request->question_link;  
        $questionlink->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\QuestionlinkController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['questionlink'] = Questionlink::find($id);
        $data['title'] = 'Genesis Admin : Faq Edit';
        return view('admin.questionlink.edit',$data); 
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
            'title' => ['required'],
            'question_link'=> ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Enter Fields Correctly');
            return redirect()->action('Admin\QuestionlinkController@create')->withInput();
        }

        $questionlink =  Questionlink::find($id);
        $questionlink->title = $request->title;
        $questionlink->question_link = $request->question_link;  
        $questionlink->push();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\QuestionlinkController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question_link = Questionlink::find($id);
        $question_link->deleted_by = Auth::id();
        $question_link->push();
        Questionlink::destroy($id);
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\QuestionlinkController@index');
    }
}
