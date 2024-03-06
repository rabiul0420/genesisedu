<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\InformationAlumni;
use App\Courses;
use App\Branch;
use App\Sessions;
use App\Institutes;
use App\ServicePackages;
use App\ComingBy;
use App\Batches;
use App\Doctors;
use Session;

class InformationalumniController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['informationalumni'] =Informationalumni::get();
        return view('Admin.informationalumni.list',$data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $data['informationalumni']=informationalumni::get();
        // $data['courses'] = Courses::where('status',1)
        //  ->pluck('name' , 'id');
        // return view('Admin.informationalumni.create',$data);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['title'] = 'SIF Admin : Doctor Courses Create';
        $data['doctors'] = Doctors::select(DB::raw("CONCAT(name,' - ',bmdc_no) AS full_name"),'id')->orderBy('id', 'DESC')->pluck('full_name', 'id');
       
        $data['courses'] = Courses::get()->pluck('name', 'id');
       
        return view('Admin.Informationalumni.create',$data);
        
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
            'doctor_id' => ['required'],     
            'course_id' => ['required'],
            'result'=> ['required'],
            'email'=> ['required'],
            'phone'=> ['required'],           
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\InformationalumniController@create')->withInput();
        }

        
        else{
            $informationalumni = new Informationalumni();
            $informationalumni->doctor_id = $request->doctor_id;
            $informationalumni->course_id = $request->course_id;
            $informationalumni->result= $request->result;
            $informationalumni->email = $request->email;           
            $informationalumni->phone = $request->phone;
            $informationalumni->save();

           
                    
        }

        Session::flash('message', 'Record has been added successfully');
         return redirect()->action('Admin\InformationalumniController@index');

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
        // $informationalumni = Informationalumni::find($id);
        
        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['title'] = 'SIF Admin : Doctor Courses Create';
        $data['doctors'] = Doctors::select(DB::raw("CONCAT(name,' - ',bmdc_no) AS full_name"),'id')->orderBy('id', 'DESC')->pluck('full_name', 'id');
        $data['courses'] = Courses::get()->pluck('name', 'id');
        $data['informationalumni'] = Informationalumni::findOrfail($id);
      

        return view('Admin.Informationalumni.edit',$data);
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
            'doctor_id' => ['required'],     
            'course_id' => ['required'],
            'result'=> ['required'],
            'email'=> ['required'],
            'phone'=> ['required'],           
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\InformationalumniController@edit')->withInput();
        }

        
        else{
            $informationalumni =Informationalumni::find($id);
            $informationalumni->doctor_id = $request->doctor_id;
            $informationalumni->course_id = $request->course_id;
            $informationalumni->result= $request->result;
            $informationalumni->email = $request->email;           
            $informationalumni->phone = $request->phone;
            $informationalumni->save();

        
                    
        }

        Session::flash('message', 'Record has been added successfully');
         return redirect()->action('Admin\InformationalumniController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Informationalumni::destroy($id);
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\InformationalumniController@index');
    }
}
