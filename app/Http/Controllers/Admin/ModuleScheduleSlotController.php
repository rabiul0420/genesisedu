<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\ModuleScheduleSlotAssign;
use App\ModuleScheduleSlotLink;
use App\ModuleSchedule;
use App\ModuleScheduleSlot;
use App\ModuleScheduleSlotDiscipline;
use App\ModuleScheduleSlotFaculty;
use App\ModuleScheduleSlotBatchModuleScheduleSlot;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\ModuleScheduleSlotContent;
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
use App\Program;
use App\ProgramContent;
use App\Room;
use App\RoomSlot;
use App\ScheduleMediaType;
use App\ScheduleModuleScheduleSlotType;
use App\ScheduleProgramType;
use App\Teacher;
use App\Teachers;
use App\Topic;
use App\TopicContent;
use App\User;
use Session;
use Auth;
use DateTimeZone;
use Illuminate\Support\Collection;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;


class ModuleScheduleSlotController extends Controller
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
        
        $data['module_name'] = 'ModuleScheduleSlot';
        $data['title'] = 'ModuleScheduleSlot List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module_schedule_slot.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function module_schedule_slot_ajax_lists(Request $request)
    {        
        $module_schedule_slot_list = DB::table('module_schedule as d1')->where('d1.status','1')->whereNull('d1.deleted_at')->join('branches as d2','d2.id','d1.branch_id')->where('d2.status','1')->whereNull('d2.deleted_at')->join('location as d3','d3.id','d1.location_id')->where('d3.status','1')->whereNull('d3.deleted_at');
        $module_schedule_slot_list = $module_schedule_slot_list->select('d1.*','d2.name as branch_name','d3.name as location_name','d2.status','d2.deleted_at','d3.status','d3.deleted_at');
                
        return Datatables::of($module_schedule_slot_list)
            ->editColumn('status', function ($module_schedule_slot_list) {
                return $module_schedule_slot_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($module_schedule_slot_list) {
                $data['module_schedule_slot_list'] = $module_schedule_slot_list;               
                return view('admin.module_schedule_slot.module_schedule_slot_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function branch_change_in_module_schedule(Request $request)
    {
        $branch = Branches::where(['id'=>$request->branch_id,'status'=>'1'])->first();

        $locations = Location::where(['branch_id'=>$branch->id,'status'=>'1'])->pluck('name','id');
        
        return  json_encode(array('location'=>view('admin.module_schedule_slot.ajax.module_schedule_slot_branch_location',['locations'=>$locations])->render()), JSON_FORCE_OBJECT);

    }

    public function location_change_in_module_schedule(Request $request)
    {
        return  json_encode(array('floor'=>view('admin.module_schedule_slot.ajax.module_schedule_slot_location_floor',['request'=>$request])->render(),'capacity'=>view('admin.module_schedule_slot.ajax.module_schedule_slot_location_floor_capacity',['request'=>$request])->render()), JSON_FORCE_OBJECT);
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
        $data['module_name'] = 'ModuleScheduleSlot';
        $data['title'] = 'ModuleScheduleSlot Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.module_schedule_slot.create',$data);
        //echo "ModuleScheduleSlot create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
            'module_schedule_id' => ['required'],
            'contact_details' => ['required'],
            'status' => ['required'],
        ]);
    }

    public function check_request($request)
    {
        $module_schedule = ModuleScheduleSlot::where(['name'=>$request->name,'module_schedule_id'=>$request->module_schedule_id])->first();
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
            return redirect()->action('Admin\ModuleScheduleSlotController@create')->withInput();
        }        

        if ($this->check_request($request)){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This record already exists');
            return redirect()->action('Admin\ModuleScheduleSlotController@create')->withInput();
        }
        else
        {

            $module_schedule = new ModuleScheduleSlot();
            $module_schedule->module_schedule_id = $request->module_schedule_id;
            $module_schedule->name = $request->name;
            $module_schedule->schedule_info = $request->schedule_info;
            $module_schedule->contact_details = $request->contact_details;
            $module_schedule->address = $request->address;
            $module_schedule->terms_and_conditions = $request->terms_and_conditions;        
            $module_schedule->status = $request->status;
            $module_schedule->created_by = Auth::id();
            $module_schedule->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\ModuleScheduleSlotController@index');
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
        $data['module_schedule']=ModuleScheduleSlot::find($id);
        return view('admin.module_schedule_slot.show',$data);
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
        $data['module_schedule'] = ModuleScheduleSlot::find($id);
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');

        $data['module_name'] = 'ModuleScheduleSlot';
        $data['title'] = 'ModuleScheduleSlot Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.module_schedule_slot.edit', $data);
        
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

        $module_schedule = ModuleScheduleSlot::find($id);

        if( $module_schedule->name != $request->name || $module_schedule->module_schedule_id != $request->module_schedule_id )
        {

            if ($this->check_request($request)){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This record already exists');
                return redirect()->action('Admin\ModuleScheduleSlotController@edit',[$id])->withInput();
            }

        }
        
        $module_schedule->module_schedule_id = $request->module_schedule_id;
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
        ModuleScheduleSlot::destroy($id); // 1 way

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ModuleScheduleSlotController@index');
    }

    function cmp($a, $b)
    {
        return strcmp($a["fruit"], $b["fruit"]);
    }

    /**
     * module schedule list
     *
     * @param  int  $module_schedule_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_slot_list($module_schedule_id)
    {       
        $data['module_schedule'] = ModuleSchedule::where('id',$module_schedule_id)->first(); 
        $data['module_schedule_slot_lists'] = ModuleScheduleSlot::where('module_schedule_id',$module_schedule_id)->get();
        
        $data['module_name'] = 'ModuleScheduleSlot';
        $data['title'] = 'ModuleScheduleSlot add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Save';
        
        return view('admin.module_schedule_slot.module_schedule_slot_list', $data);
    }

    public function module_schedule_slot_ajax_list(Request $request)
    {
        $module_schedule_id = $request->module_schedule_id; 
        $module_schedule_slot_list = DB::table('module_schedule_slot as d1')->join('module_schedule_slot as d2','d2.content_id','d1.id')->where('d2.content_type_id','1')->where('d2.module_schedule_id',$module_schedule_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $module_schedule_slot_list = $module_schedule_slot_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name','d2.id as module_schedule_slot_id','d2.deleted_at','d3.deleted_at','d4.deleted_at','d5.deleted_at');
        
        if($request->institute_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d1.session_id',$request->session_id);
        }

        return Datatables::of($module_schedule_slot_list)
            ->addColumn('action', function ($module_schedule_slot_list) {

                $data['module_schedule_slot_list'] = $module_schedule_slot_list;
                
                return view('admin.module_schedule_slot.module_schedule_slot_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    /**
     * module schedule add
     *
     * @param  int  $module_schedule_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_slot_add($module_schedule_id)
    {        
        $data['module_schedule'] = ModuleSchedule::where('id',$module_schedule_id)->first();
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');
        $data['floors'] = Room::select('floor')->groupBy('floor')->pluck('floor','floor');
        $data['rooms'] = Room::where(['status'=>'1'])->pluck('name','id');
        
        $data['module_name'] = 'ModuleScheduleSlot';
        $data['title'] = 'ModuleScheduleSlot Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Save';

        return view('admin.module_schedule_slot.module_schedule_slot_add', $data);
    }

    public function module_schedule_slot_add_list(Request $request)
    {
        $module_schedule_slot_list = DB::table('room_slot as d1')->leftJoin('room as d2','d2.id','d1.room_id')->where('d2.status','1')->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $module_schedule_slot_list = $module_schedule_slot_list->select('d1.*','d2.name','d2.branch_id','d2.location_id','d2.floor','d2.deleted_at');
        $module_schedule_slot_list = $module_schedule_slot_list->addSelect(DB::raw('"'.$request->module_schedule_id.'"'." as `module_schedule_id`"));
        
        if($request->branch_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d2.branch_id',$request->branch_id);
        }

        if($request->location_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d2.location_id',$request->location_id);
        }

        if($request->floor)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d2.floor',$request->floor);
        }

        if($request->room_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d1.room_id',$request->room_id);
        }

        if($request->search_date)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d1.start_time','like',"%{$request->search_date}%");
        }

        return Datatables::of($module_schedule_slot_list)
            ->addColumn('custom_date', function ($module_schedule_slot_list) { 
                $room_slot = RoomSlot::where('id',$module_schedule_slot_list->id)->first();
                if(isset($room_slot))
                {
                    return $room_slot->custom_date();
                }
                else
                {
                    return false;
                }
            })
            ->addColumn('hrstart_time', function ($module_schedule_slot_list) { 
                $room_slot = RoomSlot::where('id',$module_schedule_slot_list->id)->first();
                if(isset($room_slot))
                {
                    return $room_slot->hrstart_time();
                }
                else
                {
                    return false;
                }
            })
            ->addColumn('hrend_time', function ($module_schedule_slot_list) { 
                $room_slot = RoomSlot::where('id',$module_schedule_slot_list->id)->first();
                if(isset($room_slot))
                {
                    return $room_slot->hrend_time();
                }
                else
                {
                    return false;
                }
            })
            ->addColumn('start_time_end_time', function ($module_schedule_slot_list) { 
                $room_slot = RoomSlot::where('id',$module_schedule_slot_list->id)->first();
                if(isset($room_slot))
                {
                    return $room_slot->start_time_end_time();
                }
                else
                {
                    return false;
                }
            })
            ->addColumn('created_by', function ($module_schedule_slot_list) {
                $module_schedule_slot = ModuleScheduleSlot::where('slot_id',$module_schedule_slot_list->id)->first();
                if(isset($module_schedule_slot))
                {
                    $user = User::where('id',$module_schedule_slot->created_by)->first();
                    if(isset($user))
                    {
                        return $user->name.'<br>( '.$user->phone_number.' ) ';
                    }
                    else
                    {
                        return false;
                    }
                    
                }
                else
                {
                    return '';
                } 
                
            })
            ->addColumn('program', function ($module_schedule_slot_list) {
                $module_schedule_slot = ModuleScheduleSlot::where('slot_id',$module_schedule_slot_list->id)->first();
                if(isset($module_schedule_slot))
                {
                    $program = Program::where(['id'=>$module_schedule_slot->program_id])->first();
                    if(isset($program))
                    {
                        return $program->name;
                    }
                    else
                    {
                        return '';
                    }
                    
                }
                else
                {
                    return '';
                }
            })
            ->addColumn('action', function ($module_schedule_slot_list) {

                $data['checked'] = "";
                $data['module_schedule_slot_add_info'] = "";
                
                //$module_schedule_slot = ModuleScheduleSlot::where(['module_schedule_id'=>$module_schedule_slot_list->module_schedule_id,'slot_id'=>$module_schedule_slot_list->id])->first();
                $module_schedule_slot = ModuleScheduleSlot::where(['slot_id'=>$module_schedule_slot_list->id])->first();
                if(isset($module_schedule_slot))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }

                $data['module_schedule_slot_list'] = $module_schedule_slot_list;
                
                return view('admin.module_schedule_slot.module_schedule_slot_add_ajax_list', $data);
            })
            ->rawColumns(['action','created_by'])
            ->make(true);

    }

    /**
     * Module Schedule Save
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_slot_save(Request $request)
    {

    //echo "<pre>";print_r($data['module_schedule_id'] = $request->module_schedule_id);exit;
        $data['content_type_id'] = 1;
        $data['slot_id'] = $request->slot_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {
            //$module_schedule_slot = ModuleScheduleSlot::where([ 'module_schedule_id' => $request->module_schedule_id, 'slot_id' => $request->slot_id])->first();            
            $module_schedule_slot = ModuleScheduleSlot::where(['slot_id' => $request->slot_id])->first();
            if(!isset($module_schedule_slot))
            {
                $module_schedule_slot = ModuleScheduleSlot::insert([ 'module_schedule_id' => $request->module_schedule_id, 'slot_id' => $request->slot_id, 'created_by'=>Auth::id()]);
                if(isset($module_schedule_slot))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added room slot.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This room slot is not available !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $module_schedule_slot = ModuleScheduleSlot::where([ 'module_schedule_id' => $request->module_schedule_id, 'slot_id' => $request->slot_id])->update(['deleted_by'=>Auth::id()]);
            $module_schedule_slot = ModuleScheduleSlot::where([ 'module_schedule_id' => $request->module_schedule_id, 'slot_id' => $request->slot_id])->delete();
            if(isset($module_schedule_slot))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed room slot.</span';
            }

        }
        
        return response()->json($data);
    }

    /**
     * module schedule edit
     *
     * @param  int  $module_schedule_slot_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_slot_edit($module_schedule_slot_id)
    {        
        $data['module_schedule_slot'] = ModuleScheduleSlot::where('id',$module_schedule_slot_id)->first();
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');
        $data['floors'] = Room::select('floor')->groupBy('floor')->pluck('floor','floor');
        $data['rooms'] = Room::where(['status'=>'1'])->pluck('name','id');
        
        $data['module_name'] = 'ModuleScheduleSlot';
        $data['title'] = 'ModuleScheduleSlot Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';

        return view('admin.module_schedule_slot.module_schedule_slot_edit', $data);
    }

    public function module_schedule_slot_edit_list(Request $request)
    {
        $module_schedule_slot_list = DB::table('room_slot as d1')->leftJoin('room as d2','d2.id','d1.room_id')->where('d2.status','1')->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $module_schedule_slot_list = $module_schedule_slot_list->select('d1.*','d2.name','d2.branch_id','d2.location_id','d2.floor','d2.deleted_at');
        $module_schedule_slot_list = $module_schedule_slot_list->addSelect(DB::raw('"'.$request->module_schedule_id.'"'." as `module_schedule_id`"));
        
        if($request->branch_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d2.branch_id',$request->branch_id);
        }

        if($request->location_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d2.location_id',$request->location_id);
        }

        if($request->floor)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d2.floor',$request->floor);
        }

        if($request->room_id)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d1.room_id',$request->room_id);
        }

        if($request->search_date)
        {
            $module_schedule_slot_list = $module_schedule_slot_list->where('d1.start_time','like',"%{$request->search_date}%");
        }

        return Datatables::of($module_schedule_slot_list)
            ->addColumn('date', function ($module_schedule_slot_list) { 
                $room_slot = RoomSlot::where('id',$module_schedule_slot_list->id)->first();
                if(isset($room_slot))
                {
                    return $room_slot->date();
                }
                else
                {
                    return false;
                }
            })
            ->addColumn('start_time_end_time', function ($module_schedule_slot_list) { 
                $room_slot = RoomSlot::where('id',$module_schedule_slot_list->id)->first();
                if(isset($room_slot))
                {
                    return $room_slot->start_time_end_time();
                }
                else
                {
                    return false;
                }
            })
            ->addColumn('action', function ($module_schedule_slot_list) {

                $data['checked'] = "";
                $data['module_schedule_slot_add_info'] = "";
                
                //$module_schedule_slot = ModuleScheduleSlot::where(['module_schedule_id'=>$module_schedule_slot_list->module_schedule_id,'slot_id'=>$module_schedule_slot_list->id])->first();
                $module_schedule_slot = ModuleScheduleSlot::where(['slot_id'=>$module_schedule_slot_list->id])->first();
                if(isset($module_schedule_slot))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }

                $data['module_schedule_slot_list'] = $module_schedule_slot_list;
                
                return view('admin.module_schedule_slot.module_schedule_slot_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }



    /**
     * Module Schedule update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_slot_update(Request $request)
    {

        $data['module_schedule_id'] = $request->module_schedule_id;
        $data['module_schedule_slot_id'] = $request->module_schedule_slot_id;
        $data['content_type_id'] = 1;
        $data['slot_id'] = $request->slot_id;         
        $data['status'] = "incomplete";
        
        $module_schedule_slot = ModuleScheduleSlot::where(['id'=>$data['module_schedule_slot_id']])->first();
        if(isset($module_schedule_slot))
        {
            $module_schedule_slot = ModuleScheduleSlot::where(['id'=>$data['module_schedule_slot_id']])->update([ 'module_schedule_id' => $request->module_schedule_id, 'slot_id' => $request->slot_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited module schedule slot.</span';
            
            return response()->json($data);
        }
        
    }

    /**
     * module schedule delete
     *
     * @param  int  $module_schedule_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_slot_delete($module_schedule_slot_id)
    {
        $module_schedule_slot = ModuleScheduleSlot::where(['id'=>$module_schedule_slot_id])->first();
        $module_schedule = ModuleSchedule::where(['id'=>$module_schedule_slot->module_schedule->id])->first();
        ModuleScheduleSlot::where(['id'=>$module_schedule_slot_id])->update(['deleted_by'=>Auth::id()]);        
        ModuleScheduleSlot::where(['id'=>$module_schedule_slot_id])->delete();

        Session::flash('message', 'Record has been removed successfully!!!');
        return redirect(url('admin/module-schedule-slot-list/'.$module_schedule->id));
    }

    /**
     * module schedule list
     *
     * @param  int  $module_schedule_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_program_list($module_schedule_id)
    {       
        $data['module_schedule'] = ModuleSchedule::where('id',$module_schedule_id)->first(); 
        $data['module_schedule_program_lists'] = ModuleScheduleSlot::where('module_schedule_id',$module_schedule_id)->get();
        
        $data['module_name'] = 'ModuleScheduleProgram';
        $data['title'] = 'ModuleScheduleProgram add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Save';
        
        return view('admin.module_schedule_slot.module_schedule_program_list', $data);
    }

    public function module_schedule_program_ajax_list(Request $request)
    {
        $module_schedule_id = $request->module_schedule_id; 
        $module_schedule_program_list = DB::table('module_schedule_slot as d1')->join('program as d2','d2.id','d1.program_id')->where('d2.content_type_id','1')->where('d2.module_schedule_id',$module_schedule_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $module_schedule_program_list = $module_schedule_program_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name','d2.id as module_schedule_program_id','d2.deleted_at','d3.deleted_at','d4.deleted_at','d5.deleted_at');
        
        if($request->institute_id)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d1.session_id',$request->session_id);
        }

        return Datatables::of($module_schedule_program_list)
            ->addColumn('action', function ($module_schedule_program_list) {

                $data['module_schedule_program_list'] = $module_schedule_program_list;
                
                return view('admin.module_schedule_slot.module_schedule_program_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    /**
     * module schedule add
     *
     * @param  int  $module_schedule_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_program_add($module_schedule_slot_id)
    {        
        $data['module_schedule_slot'] = ModuleScheduleSlot::where('id',$module_schedule_slot_id)->first();
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');
        $data['floors'] = Room::select('floor')->groupBy('floor')->pluck('floor','floor');
        $data['rooms'] = Room::where(['status'=>'1'])->pluck('name','id');
        
        $data['module_name'] = 'ModuleScheduleSlot';
        $data['title'] = 'ModuleScheduleSlot Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Save';

        return view('admin.module_schedule_slot.module_schedule_program_add', $data);
    }

    public function module_schedule_program_add_list(Request $request)
    {
        $module_schedule_program_list = DB::table('module_content as d1')->where(['d1.module_id'=>$request->module_id,'d1.content_type_id'=>'7'])->leftJoin('program as d2','d2.id','d1.content_id')->where(['d2.status'=>'1'])->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $module_schedule_program_list = $module_schedule_program_list->select('d1.*','d2.name','d2.deleted_at','d2.status','d2.id as program_id');
        $module_schedule_program_list = $module_schedule_program_list->addSelect(DB::raw('"'.$request->module_schedule_id.'"'." as `module_schedule_id`"));
        
        if($request->branch_id)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d2.branch_id',$request->branch_id);
        }

        if($request->location_id)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d2.location_id',$request->location_id);
        }

        if($request->floor)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d2.floor',$request->floor);
        }

        if($request->room_id)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d1.room_id',$request->room_id);
        }

        if($request->search_date)
        {
            $module_schedule_program_list = $module_schedule_program_list->where('d1.start_time','like',"%{$request->search_date}%");
        }

        return Datatables::of($module_schedule_program_list)
            ->addColumn('action', function ($module_schedule_program_list) {

                $data['checked'] = "";
                $data['module_schedule_program_add_info'] = "";
                
                //$module_schedule_slot = ModuleScheduleSlot::where(['module_schedule_id'=>$module_schedule_program_list->module_schedule_id,'slot_id'=>$module_schedule_program_list->id])->first();
                $module_schedule_slot = ModuleScheduleSlot::where(['module_schedule_id'=>$module_schedule_program_list->module_schedule_id,'program_id'=>$module_schedule_program_list->program_id])->first();
                if(isset($module_schedule_slot))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }

                $data['module_schedule_program_list'] = $module_schedule_program_list;
                
                return view('admin.module_schedule_slot.module_schedule_program_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    /**
     * Module Schedule Save
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_program_save(Request $request)
    {

    //echo "<pre>";print_r($data['module_schedule_id'] = $request->module_schedule_id);exit;
        $data['content_type_id'] = 1;
        $data['module_id'] = $request->module_id;
        $data['module_schedule_id'] = $request->module_schedule_id;
        $data['module_schedule_program_id'] = $request->module_schedule_program_id;
        $data['program_id'] = $request->program_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {
            //$module_schedule_slot = ModuleScheduleSlot::where([ 'module_schedule_id' => $request->module_schedule_id, 'slot_id' => $request->slot_id])->first();            
            $module_schedule_program = ModuleScheduleSlot::where([ 'id' => $request->module_schedule_program_id])->first();
            if(!isset($module_schedule_program))
            {
                if($this->check_mentor_available($request))
                {
                    $module_schedule_program = ModuleScheduleSlot::insert([ 'module_schedule_id' => $request->module_schedule_id, 'program_id' => $request->program_id,'created_by'=>Auth::id()]);
                    $data['status'] = "completed";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added module schedule program.</span';
                }
                else
                {
                    $data['status'] = "success";
                    $data['message'] = '<br><span style="color:red;font-weight:700">Program Mentor is not available in this slot.</span';
                }                               

            } 
            else if(isset($module_schedule_program))
            {
                if($this->check_mentor_available($request))
                {
                    $module_schedule_program = ModuleScheduleSlot::where(['id'=>$request->module_schedule_program_id])->update([ 'module_schedule_id' => $request->module_schedule_id, 'program_id' => $request->program_id,'updated_by'=>Auth::id()]);
                    $data['status'] = "completed";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully updated module schedule program.</span';
                }
                else
                {
                    $data['status'] = "success";
                    $data['message'] = '<br><span style="color:red;font-weight:700">Program Mentor is not available in this slot.</span';
                }

                
            }         

        }
        else if($request->operation == "delete")
        {
            $module_schedule_program = ModuleScheduleSlot::where([ 'module_schedule_id' => $request->module_schedule_id, 'program_id' => $request->program_id])->first();
            if(isset($module_schedule_program))
            {
                $module_schedule_program = ModuleScheduleSlot::where(['id'=>$module_schedule_program->id])->update([ 'program_id' => '', 'updated_by'=>Auth::id()]);
                if(isset($module_schedule_program))
                {
                    $data['status'] = "delete_success";
                    $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed slot program.</span';
                }

            }

        }
        
        return response()->json($data);
    }

    public function check_mentor_available($request)
    {
        $module_schedule_requested_slot = ModuleScheduleSlot::where([ 'id' => $request->module_schedule_program_id])->first();
        $program = Program::where(['id'=>$request->program_id])->first();
        $media_types = $program->media_types();
        if(isset($media_types) && count($media_types) && (in_array('Offline',$media_types) || in_array('Live',$media_types)))
        {
            
            $mentors = $program->mentors();
            
            if(isset($mentors) && count($mentors))
            {
                foreach($mentors as $mentor)
                {
                    $programs = $mentor->programs();
                    
                    if(isset($programs) && count($programs))
                    {
                        foreach($programs as $program)
                        {
                            $module_schedule_slots = ModuleScheduleSlot::with('slot')->where(['program_id'=>$program->id])->get();
                            if(isset($module_schedule_slots) && count($module_schedule_slots))
                            {
                                foreach($module_schedule_slots as $module_schedule_slot)
                                {
                                    if(trim($module_schedule_requested_slot->slot->start_time) == trim($module_schedule_slot->slot->start_time) && trim($module_schedule_requested_slot->slot->end_time) == trim($module_schedule_slot->slot->end_time))
                                    {
                                        return false;
                                    } 
                                }
                            }                        
                        }
                    }                
                }
            }

        }        
        
        return true;
    }

    public function check_mentor_availability($module_schedule_program_id,$program_id)
    {
        
        $module_schedule_requested_slot = ModuleScheduleSlot::where([ 'id' => $module_schedule_program_id])->first();
        $program = Program::where(['id'=>$program_id])->first();
        $media_types = $program->media_types();
        if(isset($media_types) && count($media_types) && (in_array('Offline',$media_types) || in_array('Live',$media_types)))
        {
            
            $mentors = $program->mentors();
            
            if(isset($mentors) && count($mentors))
            {
                foreach($mentors as $mentor)
                {
                    $programs = $mentor->programs();
                    
                    if(isset($programs) && count($programs))
                    {
                        foreach($programs as $program)
                        {
                            $module_schedule_slots = ModuleScheduleSlot::with('slot')->where(['program_id'=>$program->id])->get();
                            if(isset($module_schedule_slots) && count($module_schedule_slots))
                            {
                                foreach($module_schedule_slots as $module_schedule_slot)
                                {
                                    if(trim($module_schedule_requested_slot->slot->start_time) == trim($module_schedule_slot->slot->start_time) && trim($module_schedule_requested_slot->slot->end_time) == trim($module_schedule_slot->slot->end_time))
                                    {
                                        return false;
                                    } 
                                }
                            }                        
                        }
                    }                
                }
            }

        }        
        
        return true;
    }

    /**
     * module schedule edit
     *
     * @param  int  $module_schedule_slot_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_program_edit($module_schedule_program_id)
    {        
        $data['module_schedule_program'] = ModuleScheduleSlot::where('id',$module_schedule_program_id)->first();
                
        $data['module_name'] = 'ModuleScheduleSlot';
        $data['title'] = 'ModuleScheduleSlot Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';

        return view('admin.module_schedule_slot.module_schedule_program_edit', $data);
    }

    public function module_schedule_program_edit_list(Request $request)
    {
        $module_schedule_program_list = DB::table('program as d1')->leftJoin('module_content as d2','d2.content_id','d1.id')->where(['d2.module_id'=>$request->module_id,'d2.content_type_id'=>'7'])->where(['d1.status'=>'1'])->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $module_schedule_program_list = $module_schedule_program_list->select('d1.*','d2.deleted_at');
        $module_schedule_program_list = $module_schedule_program_list->addSelect(DB::raw('"'.$request->module_schedule_id.'"'." as `module_schedule_id`"));

        return Datatables::of($module_schedule_program_list)
            ->addColumn('action', function ($module_schedule_program_list) {

                $data['checked'] = "";
                $data['module_schedule_program_edit_info'] = "";
                
                //$module_schedule_slot = ModuleScheduleSlot::where(['module_schedule_id'=>$module_schedule_program_list->module_schedule_id,'slot_id'=>$module_schedule_program_list->id])->first();
                $module_schedule_program = ModuleScheduleSlot::where(['module_schedule_id'=>$module_schedule_program_list->module_schedule_id,'program_id'=>$module_schedule_program_list->id])->first();
                if(isset($module_schedule_program))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }

                $data['module_schedule_program_list'] = $module_schedule_program_list;
                
                return view('admin.module_schedule_slot.module_schedule_program_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);
    }



    /**
     * Module Schedule update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_program_update(Request $request)
    {

        $data['module_schedule_id'] = $request->module_schedule_id;
        $data['module_schedule_program_id'] = $request->module_schedule_program_id;
        $data['content_type_id'] = 1;
        $data['program_id'] = $request->program_id;         
        $data['status'] = "incomplete";
        
        $module_schedule_program = ModuleScheduleSlot::where(['id'=>$data['module_schedule_program_id']])->first();
        if(isset($module_schedule_program))
        {
            if($this->check_mentor_available($request))
            {
                $module_schedule_program = ModuleScheduleSlot::where(['id'=>$data['module_schedule_program_id']])->update([ 'module_schedule_id' => $request->module_schedule_id, 'program_id' => $request->program_id,'updated_by'=>Auth::id()]);
                $data['status'] = "completed";
                $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited module schedule program.</span';
            }
            else
            {
                $data['status'] = "success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Program Mentor is not available in this slot.</span';
            }            
            
            return response()->json($data);
        }
        
    }

    
    /**
     * module schedule delete
     *
     * @param  int  $module_schedule_id
     * @return \Illuminate\Http\Response
     */
    public function module_schedule_program_delete($module_schedule_program_id)
    {
        $module_schedule_program = ModuleScheduleSlot::where(['id'=>$module_schedule_program_id])->first();
        if(isset($module_schedule_program))
        {
            ModuleScheduleSlot::where(['id'=>$module_schedule_program_id])->update([ 'program_id' => '','updated_by'=>Auth::id()]);
        }
        
        Session::flash('message', 'Record has been removed successfully!!!');
        return redirect(url('admin/module-schedule-slot-list/'.$module_schedule_program->module_schedule->id));
    }
}  