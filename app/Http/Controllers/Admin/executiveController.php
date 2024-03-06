<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\Executive;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class executiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $executive = Executive::with('user')->get();
        return view('admin.executive.executiveList',['executive'=>$executive]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courses = Courses::pluck('name','id');
        $users = User::where('status','1')->pluck('name','id');
        return view('admin.executive.create',compact('courses','users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => ['required'],
            'mobile' => ['required'],
            'email' => ['required'],
            'status' => ['required'],
        ]);

        $executive = new Executive;
         
        $executive->name=$request->name;
        $executive->mobile=$request->mobile;
        $executive->email=$request->email;
        $executive->user_id = $request->user_id;
        $executive->status=$request->status;
        $executive->save();

        return redirect()->action('Admin\executiveController@index')
            ->with('success','Executive created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Executive  $executive
     * @return \Illuminate\Http\Response
     */
    public function show(Executive $executive)
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Executive  $executive
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['executive'] = Executive::with('user')->find($id);
        $users = User::pluck('name','id');
        return view('admin.executive.edit', compact('users'), $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Executive  $executive
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request,$id)
    {
        request()->validate([
            'name' => ['required'],
            'mobile' => ['required'],
            'email' => ['required'],
            'status' => ['required'],
        ]);
  
        $executive =  Executive::find($id);

        // if($request->course_id){
        //     foreach($request->course_id as $course){
        //         if($course == null){
        //             continue;
        //         }
        //         else{
        //          $course_id[] = $course;
        //         }
        //     }
        //  }

        $executive->name=$request->name;
        $executive->mobile=$request->mobile;
        $executive->email=$request->email;
        $executive->user_id = $request->user_id;
        $executive->status=$request->status;
        $executive->push();

        return redirect()->action('Admin\executiveController@index')
            ->with('success','Executive updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Executive  $executive
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Executive::destroy($id);
        return redirect()->action('Admin\executiveController@index')
            ->with('success','Executive Deleted successfully');
    }
}
