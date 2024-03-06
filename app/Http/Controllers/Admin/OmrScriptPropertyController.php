<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\OmrScriptPropertyAssign;
use App\OmrScriptPropertyLink;
use App\OmrScriptProperty;
use App\OmrScriptPropertyDiscipline;
use App\OmrScriptPropertyFaculty;
use App\OmrScriptPropertyBatchOmrScriptProperty;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Topics;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class OmrScriptPropertyController extends Controller
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
        $data['omr_script_properties'] = OmrScriptProperty::get();
        $data['module_name'] = 'OmrScriptProperty';
        $data['title'] = 'OmrScriptProperty List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.omr_script_property.list',$data);
                
        //echo $Institutes;
        //echo $title;
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

        $data['module_name'] = 'OmrScriptProperty';
        $data['title'] = 'OmrScriptProperty Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.omr_script_property.create',$data);
        //echo "Topic create";
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
            'name' => ['required'],
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\OmrScriptPropertyController@create')->withInput();
        }        

        if (OmrScriptProperty::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This OmrScriptProperty name already exists');
            return redirect()->action('Admin\OmrScriptPropertyController@create')->withInput();
        }
        else{

            $omr_script_property = new OmrScriptProperty();
            $omr_script_property->name = $request->name;
            $omr_script_property->status = $request->status;
            $omr_script_property->created_by=Auth::id();
            $omr_script_property->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\OmrScriptPropertyController@index');
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
        $omr_script_property=OmrScriptProperty::select('omr_script_propertys.*')->find($id);
        return view('admin.omr_script_property.show',['omr_script_property'=>$omr_script_property]);
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
        
        $data['omr_script_property'] = OmrScriptProperty::find($id);

        $data['module_name'] = 'OmrScriptProperty';
        $data['title'] = 'OmrScriptProperty Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.omr_script_property.edit', $data);
        
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
        //echo '<pre>';print_r($request->subject_id);exit;
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $omr_script_property = OmrScriptProperty::find($id);

        if($omr_script_property->name != $request->name) {

            if (OmrScriptProperty::where(['name'=>$request->name])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This OmrScriptProperty name already exists');
                return redirect()->action('Admin\OmrScriptPropertyController@edit',[$id])->withInput();
            }

        }

        
        $omr_script_property->name = $request->name;
        $omr_script_property->status = $request->status;
        $omr_script_property->updated_by=Auth::id();
        $omr_script_property->push();

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
        /*$user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        OmrScriptProperty::destroy($id); // 1 way
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\OmrScriptPropertyController@index');
    }

    
}  