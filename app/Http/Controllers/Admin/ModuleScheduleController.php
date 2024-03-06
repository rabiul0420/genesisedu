<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\ModuleScheduleAssign;
use App\ModuleScheduleLink;
use App\ModuleSchedule;
use App\ModuleScheduleDiscipline;
use App\ModuleScheduleFaculty;
use App\ModuleScheduleBatchModuleSchedule;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\ModuleScheduleContent;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Branch;
use App\Branches;
use App\CourseYear;
use App\CourseYearSession;
use App\LectureSheet;
use App\LectureVideo;
use App\Location;
use App\Locations;
use App\Module;
use App\ModuleScheduleSlot;
use App\ScheduleMediaType;
use App\ScheduleModuleScheduleType;
use App\ScheduleProgramType;
use App\Teacher;
use App\Topic;
use App\TopicContent;
use Session;
use Auth;
use Illuminate\Support\Collection;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;


class ModuleScheduleController extends Controller
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
        
        $data['module_name'] = 'ModuleSchedule';
        $data['title'] = 'ModuleSchedule List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module_schedule.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function module_schedule_ajax_list(Request $request)
    {        
        $module_schedule_list = DB::table('module_schedule as d1')->where('d1.status','1')->whereNull('d1.deleted_at')->join('branches as d2','d2.id','d1.branch_id')->where('d2.status','1')->whereNull('d2.deleted_at')->join('location as d3','d3.id','d1.location_id')->where('d3.status','1')->whereNull('d3.deleted_at');
        $module_schedule_list = $module_schedule_list->select('d1.*','d2.name as branch_name','d3.name as location_name','d2.status','d2.deleted_at','d3.status','d3.deleted_at');
                
        return Datatables::of($module_schedule_list)
            ->editColumn('status', function ($module_schedule_list) {
                return $module_schedule_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($module_schedule_list) {
                $data['module_schedule_list'] = $module_schedule_list;               
                return view('admin.module_schedule.module_schedule_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function branch_change_in_module_schedule(Request $request)
    {
        $branch = Branches::where(['id'=>$request->branch_id,'status'=>'1'])->first();

        $locations = Location::where(['branch_id'=>$branch->id,'status'=>'1'])->pluck('name','id');
        
        return  json_encode(array('location'=>view('admin.module_schedule.ajax.module_schedule_branch_location',['locations'=>$locations])->render()), JSON_FORCE_OBJECT);

    }

    public function location_change_in_module_schedule(Request $request)
    {
        return  json_encode(array('floor'=>view('admin.module_schedule.ajax.module_schedule_location_floor',['request'=>$request])->render(),'capacity'=>view('admin.module_schedule.ajax.module_schedule_location_floor_capacity',['request'=>$request])->render()), JSON_FORCE_OBJECT);
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
        $data['module_name'] = 'ModuleSchedule';
        $data['title'] = 'ModuleSchedule Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.module_schedule.create',$data);
        //echo "ModuleSchedule create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
            'module_id' => ['required'],
            'contact_details' => ['required'],
            'status' => ['required'],
        ]);
    }

    public function check_request($request)
    {
        $module_schedule = ModuleSchedule::where(['name'=>$request->name,'module_id'=>$request->module_id])->first();
        if(isset($module_schedule))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = $this->validate_request($request);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\ModuleScheduleController@create')->withInput();
        }        

        if ($this->check_request($request)){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This record already exists');
            return redirect()->action('Admin\ModuleScheduleController@create')->withInput();
        }
        else
        {

            $module_schedule = new ModuleSchedule();
            $module_schedule->module_id = $request->module_id;
            $module_schedule->name = $request->name;
            $module_schedule->schedule_info = $request->schedule_info;
            $module_schedule->contact_details = $request->contact_details;
            $module_schedule->address = $request->address;
            $module_schedule->terms_and_conditions = $request->terms_and_conditions;        
            $module_schedule->status = $request->status;
            $module_schedule->created_by = Auth::id();
            $module_schedule->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\ModuleScheduleController@index');
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
        $data['module_schedule']=ModuleSchedule::find($id);
        return view('admin.module_schedule.show',$data);
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
        $data['module_schedule'] = ModuleSchedule::find($id);
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');

        $data['module_name'] = 'ModuleSchedule';
        $data['title'] = 'ModuleSchedule Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.module_schedule.edit', $data);
        
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
        $validator = $this->validate_request($request);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $module_schedule = ModuleSchedule::find($id);

        if( $module_schedule->name != $request->name || $module_schedule->module_id != $request->module_id )
        {

            if ($this->check_request($request)){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This record already exists');
                return redirect()->action('Admin\ModuleScheduleController@edit',[$id])->withInput();
            }

        }
        
        $module_schedule->module_id = $request->module_id;
        $module_schedule->name = $request->name;
        $module_schedule->schedule_info = $request->schedule_info;
        $module_schedule->contact_details = $request->contact_details;
        $module_schedule->address = $request->address;
        $module_schedule->terms_and_conditions = $request->terms_and_conditions;        
        $module_schedule->status = $request->status;
        $module_schedule->created_by = Auth::id();
        $module_schedule->push();

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
        ModuleSchedule::destroy($id); // 1 way

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ModuleScheduleController@index');
    }

    /**
     * module schedule list
     *
     * @param  int  $module_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_list($module_id)
    {       
        $data['module'] = Module::where('id',$module_id)->first(); 
        $data['module_schedule_lists'] = ModuleSchedule::where('module_id',$module_id)->get();
        
        $data['module_name'] = 'ModuleSchedule';
        $data['title'] = 'ModuleSchedule add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Save';

        return view('admin.module_schedule.module_schedule_list', $data);
    }

    /**
     * module schedule add
     *
     * @param  int  $module_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_add($module_id)
    {        
        $data['module'] = Module::where('id',$module_id)->first();
        
        $data['module_name'] = 'ModuleSchedule';
        $data['title'] = 'ModuleSchedule Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Save';

        return view('admin.module_schedule.module_schedule_add', $data);
    }

    /**
     * Module Schedule Save
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_save(Request $request)
    {

        $validator = $this->validate_request($request);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect(url('admin/module-schedule-add/'.$request->module_id));
        }        

        if ($this->check_request($request)){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This record already exists');
            return redirect(url('admin/module-schedule-add/'.$request->module_id));
        }
        else
        {

            $module_schedule = new ModuleSchedule();
            $module_schedule->module_id = $request->module_id;
            $module_schedule->name = $request->name;
            $module_schedule->schedule_info = $request->schedule_info;
            $module_schedule->contact_details = $request->contact_details;
            $module_schedule->address = $request->address;
            $module_schedule->terms_and_conditions = $request->terms_and_conditions;        
            $module_schedule->status = $request->status;
            $module_schedule->created_by = Auth::id();
            $module_schedule->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect(url('admin/module-schedule-list/'.$request->module_id));
        }
    }

    /**
     * module schedule edit
     *
     * @param  int  $module_schedule_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_edit($module_schedule_id)
    {        
        $data['module_schedule'] = ModuleSchedule::where('id',$module_schedule_id)->first();
        

        $data['module_name'] = 'ModuleSchedule';
        $data['title'] = 'ModuleSchedule Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';

        return view('admin.module_schedule.module_schedule_edit', $data);
    }

    /**
     * Module Schedule update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_update(Request $request)
    {

        $validator = $this->validate_request($request);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect(url('admin/module-schedule-edit/'.$request->module_schedule_id));
        }

        $module_schedule = ModuleSchedule::find($request->module_schedule_id);

        if( $module_schedule->name != $request->name || $module_schedule->module_id != $request->module_id )
        {

            if ($this->check_request($request)){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This record already exists');
                return redirect(url('admin/module-schedule-edit/'.$request->module_schedule_id));
            }

        }

        $module_schedule->module_id = $request->module_id;
        $module_schedule->name = $request->name;
        $module_schedule->schedule_info = $request->schedule_info;
        $module_schedule->contact_details = $request->contact_details;
        $module_schedule->address = $request->address;
        $module_schedule->terms_and_conditions = $request->terms_and_conditions;        
        $module_schedule->status = $request->status;
        $module_schedule->updated_by = Auth::id();
        $module_schedule->push();

        Session::flash('message', 'Record has been added successfully');

        return redirect(url('admin/module-schedule-edit/'.$request->module_schedule_id));
        
    }

    /**
     * module schedule delete
     *
     * @param  int  $module_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_delete($module_schedule_id)
    {
        $module_schedule = ModuleSchedule::where(['id'=>$module_schedule_id])->first();
        $module = Module::where(['id'=>$module_schedule->module->id])->first();
        ModuleSchedule::where(['id'=>$module_schedule_id])->update(['deleted_by'=>Auth::id()]);        
        ModuleSchedule::where(['id'=>$module_schedule_id])->delete();

        ModuleScheduleSlot::where(['module_schedule_id'=>$module_schedule_id])->update(['deleted_by'=>Auth::id()]);        
        ModuleScheduleSlot::where(['module_schedule_id'=>$module_schedule_id])->delete();

        Session::flash('message', 'Record has been removed successfully!!!');
        return redirect(url('admin/module-schedule-list/'.$module->id));
    }

    /**
     * module schedule print
     *
     * @param  int  $module_schedule_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_print($module_schedule_id)
    {
        $data['module_schedule'] = ModuleSchedule::where(['id'=>$module_schedule_id])->first();
        
        $data['module_name'] = 'ModuleSchedule';
        $data['title'] = 'ModuleSchedule Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module_schedule.print', $data);
    }
}  