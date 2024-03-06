<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\OmrScriptAssign;
use App\OmrScriptLink;
use App\OmrScript;
use App\OmrScriptDiscipline;
use App\OmrScriptFaculty;
use App\OmrScriptBatchOmrScript;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\OmrScriptOmrScriptProperty;
use App\OmrScriptProperty;
use App\Topics;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class OmrScriptController extends Controller
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
        $data['omr_scripts'] = OmrScript::get();
        
        $data['module_name'] = 'OmrScript';
        $data['title'] = 'OmrScript List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.omr_script.list',$data);
                
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

        $data['omr_script_properties'] = OmrScriptProperty::pluck('name','id');
        $data['module_name'] = 'OmrScript';
        $data['title'] = 'OmrScript Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.omr_script.create',$data);
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
            return redirect()->action('Admin\OmrScriptController@create')->withInput();
        }        

        if (OmrScript::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This OmrScript name already exists');
            return redirect()->action('Admin\OmrScriptController@create')->withInput();
        }
        else{

            $omr_script = new OmrScript();
            $omr_script->name = $request->name;
            $omr_script->status = $request->status;
            $omr_script->created_by=Auth::id();
            $omr_script->save();

            $omr_script_property_ids = $request->omr_script_property_id;

            if (is_array($omr_script_property_ids))
            {
                foreach ($omr_script_property_ids as $key => $value)
                {

                    if($value == '')continue;
                    //echo "<pre>";print_r($value);exit;
                    unset($omr_script_omr_script_property);
                    $omr_script_omr_script_property = new OmrScriptOmrScriptProperty();
                    $omr_script_omr_script_property->omr_script_id = $omr_script->id;
                    $omr_script_omr_script_property->omr_script_property_id = $value;
                    $omr_script_omr_script_property->start_position = $request->start_position[$key];
                    $omr_script_omr_script_property->end_position = $request->end_position[$key];
                    $omr_script_omr_script_property->created_at = Auth::id();
                    $omr_script_omr_script_property->save();

                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\OmrScriptController@index');
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
        $omr_script=OmrScript::select('couriers.*')->find($id);
        return view('admin.omr_script.show',['courier'=>$omr_script]);
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
        
        $data['omr_script'] = OmrScript::find($id);
        $data['omr_script_properties'] = OmrScriptProperty::pluck('name','id');

        $data['module_name'] = 'OmrScript';
        $data['title'] = 'OmrScript Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.omr_script.edit', $data);
        
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

        $omr_script = OmrScript::find($id);

        if($omr_script->name != $request->name) {

            if (OmrScript::where(['name'=>$request->name])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This OmrScript name already exists');
                return redirect()->action('Admin\OmrScriptController@edit',[$id])->withInput();
            }

        }
        
        $omr_script->name = $request->name;
        $omr_script->status = $request->status;
        $omr_script->updated_by=Auth::id();
        $omr_script->push();

        $omr_script_property_ids = $request->omr_script_property_id;

        if (is_array($omr_script_property_ids))
        {
            OmrScriptOmrScriptProperty::where(['omr_script_id'=>$omr_script->id])->delete();
            
            foreach ($omr_script_property_ids as $key => $value)
            {

                if($value == '')continue;
                //echo "<pre>";print_r($value);exit;
                unset($omr_script_omr_script_property);
                $omr_script_omr_script_property = new OmrScriptOmrScriptProperty();
                $omr_script_omr_script_property->omr_script_id = $omr_script->id;
                $omr_script_omr_script_property->omr_script_property_id = $value;
                $omr_script_omr_script_property->start_position = $request->start_position[$key];
                $omr_script_omr_script_property->end_position = $request->end_position[$key];
                $omr_script_omr_script_property->created_at = Auth::id();
                $omr_script_omr_script_property->save();

            }
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
        /*$user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        OmrScript::destroy($id); // 1 way
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\OmrScriptController@index');
    }

    
}  