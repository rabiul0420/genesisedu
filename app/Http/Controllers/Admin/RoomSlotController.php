<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\RoomSlotAssign;
use App\RoomSlotLink;
use App\Room;
use App\RoomSlot;
use App\RoomSlotDiscipline;
use App\RoomSlotFaculty;
use App\RoomSlotBatchRoomSlot;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\RoomSlotContent;
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
use App\ScheduleRoomSlotType;
use App\ScheduleProgramType;
use App\Teacher;
use App\Topic;
use App\TopicContent;
use Session;
use Auth;
use Illuminate\Support\Collection;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;


class RoomSlotController extends Controller
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
        
        $data['module_name'] = 'RoomSlot';
        $data['title'] = 'RoomSlot List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.room_slot.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function room_slot_ajax_list(Request $request)
    {        
        $room_slot_list = DB::table('room_slot as d1')->where('d1.status','1')->whereNull('d1.deleted_at')->join('branches as d2','d2.id','d1.branch_id')->where('d2.status','1')->whereNull('d2.deleted_at')->join('location as d3','d3.id','d1.location_id')->where('d3.status','1')->whereNull('d3.deleted_at');
        $room_slot_list = $room_slot_list->select('d1.*','d2.name as branch_name','d3.name as location_name','d2.status','d2.deleted_at','d3.status','d3.deleted_at');
                
        return Datatables::of($room_slot_list)
            ->editColumn('status', function ($room_slot_list) {
                return $room_slot_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($room_slot_list) {
                $data['room_slot_list'] = $room_slot_list;               
                return view('admin.room_slot.room_slot_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function branch_change_in_room_slot(Request $request)
    {
        $branch = Branches::where(['id'=>$request->branch_id,'status'=>'1'])->first();

        $locations = Location::where(['branch_id'=>$branch->id,'status'=>'1'])->pluck('name','id');
        
        return  json_encode(array('location'=>view('admin.room_slot.ajax.room_slot_branch_location',['locations'=>$locations])->render()), JSON_FORCE_OBJECT);

    }

    public function location_change_in_room_slot(Request $request)
    {
        return  json_encode(array('floor'=>view('admin.room_slot.ajax.room_slot_location_floor',['request'=>$request])->render(),'capacity'=>view('admin.room_slot.ajax.room_slot_location_floor_capacity',['request'=>$request])->render()), JSON_FORCE_OBJECT);
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
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');
        $data['module_name'] = 'RoomSlot';
        $data['title'] = 'RoomSlot Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.room_slot.create',$data);
        //echo "RoomSlot create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'room_id' => ['required'],
            'start_time' => ['required'],
            'end_time' => ['required'],
        ]);
    }

    public function check_request($request)
    {
        if($request->room_slot_id)
        {
            $room_slot = RoomSlot::where('id','!=',$request->room_slot_id)->where(['room_id'=>$request->room_id])->where('start_time','<=',$request->start_time)->where('end_time','>=',$request->start_time)->first();
            if(isset($room_slot) && $room_slot !== false)
            {
                return $room_slot;
            }

            $room_slot = RoomSlot::where('id','!=',$request->room_slot_id)->where(['room_id'=>$request->room_id])->where('start_time','<=',$request->end_time)->where('end_time','>=',$request->end_time)->first();
            if(isset($room_slot) && $room_slot !== false)
            {
                return $room_slot;
            }

        }
        else
        {
            $room_slot = RoomSlot::where(['room_id'=>$request->room_id])->where('start_time','<=',$request->start_time)->where('end_time','>=',$request->start_time)->first();
            if(isset($room_slot) && $room_slot !== false)
            {
                return $room_slot;
            }

            $room_slot = RoomSlot::where(['room_id'=>$request->room_id])->where('start_time','<=',$request->end_time)->where('end_time','>=',$request->end_time)->first();
            if(isset($room_slot) && $room_slot !== false)
            {
                return $room_slot;
            }
        }
        
        
        return false;
        
    }

    public function check_request_multiple($request)
    {
        if($request->room_slot_id)
        {
            $room_slot = RoomSlot::where('id','!=',$request->room_slot_id)->where(['room_id'=>$request->room_id])->where('start_time','<=',$request->start_time)->where('end_time','>=',$request->start_time)->first();
            if(isset($room_slot) && $room_slot !== false)
            {
                return $room_slot;
            }

            $room_slot = RoomSlot::where('id','!=',$request->room_slot_id)->where(['room_id'=>$request->room_id])->where('start_time','<=',$request->end_time)->where('end_time','>=',$request->end_time)->first();
            if(isset($room_slot) && $room_slot !== false)
            {
                return $room_slot;
            }

        }
        else
        {
            $room_slot = RoomSlot::where(['room_id'=>$request->room_id])->where('start_time','<=',$request->start_time)->where('end_time','>=',$request->start_time)->first();
            if(isset($room_slot) && $room_slot !== false)
            {
                return $room_slot;
            }

            $room_slot = RoomSlot::where(['room_id'=>$request->room_id])->where('start_time','<=',$request->end_time)->where('end_time','>=',$request->end_time)->first();
            if(isset($room_slot) && $room_slot !== false)
            {
                return $room_slot;
            }
        }
        
        
        return false;
        
    }

    public function check_slot($room_id,$start_time,$end_time)
    {        
        $room_slot = RoomSlot::where(['room_id'=>$room_id])->where('start_time','<=',$start_time)->where('end_time','>=',$start_time)->first();
        if(isset($room_slot) && $room_slot !== false)
        {
            return $room_slot;
        }

        $room_slot = RoomSlot::where(['room_id'=>$room_id])->where('start_time','<=',$end_time)->where('end_time','>=',$end_time)->first();
        if(isset($room_slot) && $room_slot !== false)
        {
            return $room_slot;
        }
        
        return false;
        
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
            return redirect()->action('Admin\RoomSlotController@create')->withInput();
        }        

        if ($this->check_request($request)){
            Session::flash('class', 'alert-danger');
            session()->flash('message','The room is not available for given slot');
            return redirect()->action('Admin\RoomSlotController@create')->withInput();
        }
        else
        {

            $room_slot = new RoomSlot();
            $room_slot->room_id = $request->room_id;
            $room_slot->name = $request->name;
            $room_slot->schedule_info = $request->schedule_info;
            $room_slot->contact_details = $request->contact_details;
            $room_slot->address = $request->address;
            $room_slot->terms_and_conditions = $request->terms_and_conditions;        
            $room_slot->status = $request->status;
            $room_slot->created_by = Auth::id();
            $room_slot->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\RoomSlotController@index');
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
        $data['room_slot']=RoomSlot::find($id);
        return view('admin.room_slot.show',$data);
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
        $data['room_slot'] = RoomSlot::find($id);
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');

        $data['module_name'] = 'RoomSlot';
        $data['title'] = 'RoomSlot Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.room_slot.edit', $data);
        
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

        $room_slot = RoomSlot::find($id);

        if( $room_slot->name != $request->name || $room_slot->room_id != $request->room_id )
        {

            if ($this->check_request($request)){
                Session::flash('class', 'alert-danger');
                session()->flash('message','The room is not available for given slot');
                return redirect()->action('Admin\RoomSlotController@edit',[$id])->withInput();
            }

        }
        
        $room_slot->room_id = $request->room_id;
        $room_slot->name = $request->name;
        $room_slot->schedule_info = $request->schedule_info;
        $room_slot->contact_details = $request->contact_details;
        $room_slot->address = $request->address;
        $room_slot->terms_and_conditions = $request->terms_and_conditions;        
        $room_slot->status = $request->status;
        $room_slot->created_by = Auth::id();
        $room_slot->push();

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
        RoomSlot::destroy($id); // 1 way

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\RoomSlotController@index');
    }

    /**
     * module schedule list
     *
     * @param  int  $room_id
     * @return \Illuminate\Http\Response
     */
    public function room_slot_list($room_id)
    {       
        $data['room'] = Room::where('id',$room_id)->first(); 
        $data['room_slot_lists'] = RoomSlot::where('room_id',$room_id)->orderBy('start_time')->get();
        
        $data['module_name'] = 'RoomSlot';
        $data['title'] = 'RoomSlot add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Save';

        return view('admin.room_slot.room_slot_list', $data);
    }

    /**
     * module schedule add
     *
     * @param  int  $room_id
     * @return \Illuminate\Http\Response
     */
    public function room_slot_add($room_id)
    {        
        $data['room'] = Room::where('id',$room_id)->first();
        
        $data['module_name'] = 'RoomSlot';
        $data['title'] = 'RoomSlot Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Save';

        return view('admin.room_slot.room_slot_add', $data);
    }

    /**
     * Module Schedule Save
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function room_slot_save(Request $request)
    {

        $validator = $this->validate_request($request);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect(url('admin/room-slot-add/'.$request->room_id));
        }

        $check_slot = $this->check_request($request);

        if(isset($check_slot) && $check_slot !== false)
        {
            Session::flash('class', 'alert-danger');
            session()->flash('message','The room is not available for given slot');
            return redirect(url('admin/room-slot-add/'.$request->room_id));
        }
        else
        {

            $room_slot = new RoomSlot();
            $room_slot->room_id = $request->room_id;
            $room_slot->start_time = $request->start_time;
            $room_slot->end_time = $request->end_time;
            $room_slot->created_by = Auth::id();
            $room_slot->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect(url('admin/room-slot-add/'.$request->room_id));
        }
        
    }
    public function custom_date($date)
    {
        $custom_date = "";
            
        if(strpos($date, '-') !== false)
        {
            $splited = explode('-',$date);
            
            $custom_date = $splited[2].'-'.$splited[1].'-'.$splited[0];
        }
        
        return $custom_date;

    }

    public function custom_time($time)
    {
        $custom_time = "";

        if(strpos($time,':') !== false)
        {
            $splited_time = explode(':',$time);
            $second_part = $splited_time[1];
            if(strpos($second_part,'PM') !== false)
            {
                if($splited_time[0] == 12)
                {
                    $splited_time[0] = $splited_time[0];
                }
                else
                {
                    $splited_time[0] = $splited_time[0] + 12;                    
                }
                
            }
            else if(strpos($second_part,'AM') !== false)
            {
                if($splited_time[0]<10)
                {
                    $splited_time[0] = '0'.$splited_time[0];
                }
                else
                {
                    $splited_time[0] = $splited_time[0];
                }
            }

            $splited_time[1] = str_replace('AM','',$splited_time[1]);
            $splited_time[1] = str_replace('PM','',$splited_time[1]);

            $custom_time = $splited_time[0].'-'.trim($splited_time[1]);

        }
        
        return $custom_time;
        
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function room_slot_save_multiple(Request $request)
    {
        $initial_date = $request->initial_date;
        $last_date = new \DateTime($request->last_date);
        $weekdays = array('1'=>'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $array_weekday = $request->wd_id;

        $initial_date = Date('Y-m-d',date_create_from_format('d-m-Y',$initial_date)->getTimestamp());
        // Create a new DateTime object
        $date = new \DateTime($initial_date);
        $dates = array();
        $j = array_search($value = $date->format('N'), $array_weekday);

        for($i = 1;$i<500;$i++){
            // Create a new DateTime object
            $date = new \DateTime($date->format('Y-m-d'));
            
            if(count($array_weekday)>1){

                // Modify the date it contains
                if($i==1){
                    $dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');
                    if($j > count($array_weekday)-1 )$j = 0;
                }
                else {
                    if($j == 0 || $j == count($array_weekday)-1){$dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');}
                    else $dates[] = $date->modify('next '.$weekdays[$array_weekday[$j++]])->format('Y-m-d');
                    if($j > count($array_weekday)-1 )$j = 0;
                }

            }
            else if(count($array_weekday)==1){

                if($i==1){
                    $dates[] = $date->modify($weekdays[$array_weekday[$j]])->format('Y-m-d');
                }
                else {

                    $dates[] = $date->modify('next ' . $weekdays[$array_weekday[$j]])->format('Y-m-d');
                }
            }

            if(count($dates) && $dates[count($dates)-1] >= $last_date->format('Y-m-d'))
            {
                break;
            }
        }

        $custom_date_times = array();

        if(isset($dates) && count($dates))
        {
            foreach($dates as $k=>$date)
            {
                $custom_date_times[$k]['start_time'] = $date.'-'.$this->custom_time($request->m_start_time);
                $custom_date_times[$k]['end_time'] = $date.'-'.$this->custom_time($request->m_end_time);
            }
        }

        foreach($custom_date_times as $k=>$custom_date_time)
        {
            unset($collection);
            $collection = new Collection();
            $collection->room_slot_id = '';
            $collection->room_id = $request->room_id;
            $collection->start_time = $custom_date_time['start_time'];
            $collection->end_time = $custom_date_time['end_time'];
            
            if(!$this->check_request($collection))
            {
                $room_slot = new RoomSlot();
                $room_slot->room_id = $request->room_id;
                $room_slot->start_time = $custom_date_time['start_time'];
                $room_slot->end_time = $custom_date_time['end_time'];
                $room_slot->created_by = Auth::id();
                $room_slot->save();
            }
            
        }

        Session::flash('message', 'Record has been added successfully');

        return redirect(url('admin/room-slot-add/'.$request->room_id));
    }

    /**
     * module schedule edit
     *
     * @param  int  $room_slot_id
     * @return \Illuminate\Http\Response
     */
    public function room_slot_edit($room_slot_id)
    {        
        $data['room_slot'] = RoomSlot::where('id',$room_slot_id)->first();
        

        $data['module_name'] = 'RoomSlot';
        $data['title'] = 'RoomSlot Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';

        return view('admin.room_slot.room_slot_edit', $data);
    }

    /**
     * Module Schedule update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function room_slot_update(Request $request)
    {

        $validator = $this->validate_request($request);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect(url('admin/room-slot-edit/'.$request->room_slot_id));
        }

        $room_slot = RoomSlot::find($request->room_slot_id);

        if( $room_slot->room_id != $request->room_id || $room_slot->start_time != $request->start_time || $room_slot->end_time != $request->end_time )
        {
            $check_slot = $this->check_request($request);

            if(isset($check_slot) && $check_slot !== false)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','The room is not available for given slot!!!');
                return redirect(url('admin/room-slot-edit/'.$request->room_slot_id));
            }

        }

        $room_slot->room_id = $request->room_id;
        $room_slot->start_time = $request->start_time;
        $room_slot->end_time = $request->end_time;
        $room_slot->updated_by = Auth::id();
        $room_slot->push();

        Session::flash('message', 'Record has been added successfully');

        return redirect(url('admin/room-slot-edit/'.$request->room_slot_id));
        
    }

    /**
     * module schedule delete
     *
     * @param  int  $room_id
     * @return \Illuminate\Http\Response
     */
    public function room_slot_delete($room_slot_id)
    {
        $room_slot = RoomSlot::where(['id'=>$room_slot_id])->first();
        $room = Room::where(['id'=>$room_slot->room->id])->first();
        RoomSlot::where(['id'=>$room_slot_id])->update(['deleted_by'=>Auth::id()]);        
        RoomSlot::where(['id'=>$room_slot_id])->delete();
        ModuleScheduleSlot::where(['slot_id'=>$room_slot_id])->update(['deleted_by'=>Auth::id()]);
        ModuleScheduleSlot::where(['slot_id'=>$room_slot_id])->delete();

        Session::flash('class', 'alert-danger');
        Session::flash('message', 'Record has been removed successfully!!!');
        return redirect(url('admin/room-slot-list/'.$room->id));
    }
}  