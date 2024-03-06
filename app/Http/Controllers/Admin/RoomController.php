<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\RoomAssign;
use App\RoomLink;
use App\Room;
use App\RoomDiscipline;
use App\RoomFaculty;
use App\RoomBatchRoom;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\RoomContent;
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
use App\ScheduleMediaType;
use App\ScheduleRoomType;
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


class RoomController extends Controller
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
        
        $data['module_name'] = 'Room';
        $data['title'] = 'Room List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.room.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function room_list(Request $request)
    {        
        $room_list = DB::table('room as d1')->where('d1.status','1')->whereNull('d1.deleted_at')->join('branches as d2','d2.id','d1.branch_id')->where('d2.status','1')->whereNull('d2.deleted_at')->join('location as d3','d3.id','d1.location_id')->where('d3.status','1')->whereNull('d3.deleted_at');
        $room_list = $room_list->select('d1.*','d2.name as branch_name','d3.name as location_name','d2.status','d2.deleted_at','d3.status','d3.deleted_at');
                
        return Datatables::of($room_list)
            ->editColumn('status', function ($room_list) {
                return $room_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($room_list) {
                $data['room_list'] = $room_list;               
                return view('admin.room.room_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function branch_change_in_room(Request $request)
    {
        $branch = Branches::where(['id'=>$request->branch_id,'status'=>'1'])->first();

        $locations = Location::where(['branch_id'=>$branch->id,'status'=>'1'])->pluck('name','id');

        $view_name = $request->view_name;
        
        return  json_encode(array('location'=>view('admin.room.ajax.'.$view_name,['locations'=>$locations])->render()), JSON_FORCE_OBJECT);

    }

    public function location_change_in_room(Request $request)
    {
        $floors = Room::where(['branch_id'=>$request->branch_id,'location_id'=>$request->location_id,'status'=>'1'])->groupBy('floor')->pluck('floor','floor');
        $view_name = $request->view_name;
        return  json_encode(array('floor'=>view('admin.room.ajax.'.$view_name,['floors'=>$floors])->render(),'capacity'=>view('admin.room.ajax.room_location_floor_capacity',['request'=>$request])->render()), JSON_FORCE_OBJECT);
    }

    public function floor_change_in_room(Request $request)
    {
        $rooms = Room::where(['branch_id'=>$request->branch_id,'location_id'=>$request->location_id,'floor'=>$request->floor,'status'=>'1'])->pluck('name','id');
        $view_name = $request->view_name;
        return  json_encode(array('room'=>view('admin.room.ajax.'.$view_name,['rooms'=>$rooms])->render()), JSON_FORCE_OBJECT);
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
        $data['module_name'] = 'Room';
        $data['title'] = 'Room Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.room.create',$data);
        //echo "Room create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
            'branch_id' => ['required'],
            'location_id' => ['required'],
            'floor' => ['required'],
            'capacity' => ['required'],
        ]);
    }

    public function check_request($request)
    {
        $room = Room::where(['name'=>$request->name,'branch_id'=>$request->branch_id,'location_id'=>$request->location_id,'floor'=>$request->floor])->first();
        if(isset($room))
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
            return redirect()->action('Admin\RoomController@create')->withInput();
        }        

        if ($this->check_request($request)){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This record already exists');
            return redirect()->action('Admin\RoomController@create')->withInput();
        }
        else
        {

            $room = new Room();
            $room->name = $request->name;
            $room->branch_id = $request->branch_id;
            $room->location_id = $request->location_id;
            $room->floor = $request->floor;
            $room->capacity = $request->capacity;
            $room->live_link = $request->live_link;
            $room->live_link_username = $request->live_link_username;
            $room->live_link_password = $request->live_link_password;
            $room->status = $request->status;
            $room->created_by = Auth::id();
            $room->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\RoomController@index');
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
        $data['room']=Room::find($id);
        return view('admin.room.show',$data);
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
        $data['room'] = Room::find($id);
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');

        $data['module_name'] = 'Room';
        $data['title'] = 'Room Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.room.edit', $data);
        
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

        $room = Room::find($id);

        if( $room->name != $request->name || $room->branch_id != $request->branch_id || $room->location_id != $request->location_id || $room->floor != $request->floor ) {

            if ($this->check_request($request)){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This record already exists');
                return redirect()->action('Admin\RoomController@edit',[$id])->withInput();
            }

        }
        
        $room->name = $request->name;
        $room->branch_id = $request->branch_id;
        $room->location_id = $request->location_id;
        $room->floor = $request->floor;
        $room->capacity = $request->capacity;
        $room->live_link = $request->live_link;
        $room->live_link_username = $request->live_link_username;
        $room->live_link_password = $request->live_link_password;
        $room->status = $request->status;
        $room->updated_by=Auth::id();
        $room->push();

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
        Room::destroy($id); // 1 way

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\RoomController@index');
    }
}  