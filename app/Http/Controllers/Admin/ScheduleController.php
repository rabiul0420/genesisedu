<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\ScheduleAssign;
use App\ScheduleLink;
use App\Schedule;
use App\ScheduleDiscipline;
use App\ScheduleFaculty;
use App\ScheduleBatchSchedule;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\ScheduleContent;
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
use App\ScheduleScheduleType;
use App\ScheduleProgramType;
use App\ScheduleType;
use App\ScheduleTypes;
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


class ScheduleController extends Controller
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
        
        $data['module_name'] = 'Schedule';
        $data['title'] = 'Schedule List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.schedule.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function schedule_list(Request $request)
    {        
        $schedule_list = DB::table('schedule as d1')->where('d1.status','1')->whereNull('d1.deleted_at')->join('schedule_type as d2','d2.id','d1.schedule_type_id')->where('d2.status','1')->whereNull('d2.deleted_at');
        $schedule_list = $schedule_list->select('d1.*','d2.name as schedule_type_name','d2.status','d2.deleted_at');
                
        return Datatables::of($schedule_list)
            ->editColumn('status', function ($schedule_list) {
                return $schedule_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($schedule_list) {
                $data['schedule_list'] = $schedule_list;               
                return view('admin.schedule.schedule_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_schedule(Request $request)
    {
        $institute = Institutes::where(['id'=>$request->institute_id,'status'=>'1'])->first();

        $courses = Courses::where(['institute_id'=>$institute->id,'status'=>'1'])->pluck('name','id');
        
        return  json_encode(array('course'=>view('admin.schedule.ajax.schedule_institute_course',['courses'=>$courses])->render()), JSON_FORCE_OBJECT);

    }

    public function location_change_in_schedule(Request $request)
    {
        return  json_encode(array('floor'=>view('admin.schedule.ajax.schedule_location_floor',['request'=>$request])->render(),'capacity'=>view('admin.schedule.ajax.schedule_location_floor_capacity',['request'=>$request])->render()), JSON_FORCE_OBJECT);
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
        $data['schedule_types'] = ScheduleType::pluck('name','id');
        $data['module_name'] = 'Schedule';
        $data['title'] = 'Schedule Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.schedule.create',$data);
        //echo "Schedule create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
            'schedule_type_id' => ['required'],
        ]);
    }

    public function check_request($request)
    {
        $schedule = Schedule::where(['name'=>$request->name,'schedule_type_id'=>$request->schedule_type_id])->first();
        if(isset($schedule))
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
            return redirect()->action('Admin\ScheduleController@create')->withInput();
        }        

        if ($this->check_request($request)){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This record already exists');
            return redirect()->action('Admin\ScheduleController@create')->withInput();
        }
        else
        {

            $schedule = new Schedule();
            $schedule->name = $request->name;
            $schedule->schedule_type_id = $request->schedule_type_id;
            $schedule->status = $request->status;
            $schedule->created_by = Auth::id();
            $schedule->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\ScheduleController@index');
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
        $data['schedule']=Schedule::find($id);
        return view('admin.schedule.show',$data);
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
        $data['schedule'] = Schedule::find($id);
        $data['schedule_types'] = ScheduleType::pluck('name','id');
        
        $data['module_name'] = 'Schedule';
        $data['title'] = 'Schedule Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.schedule.edit', $data);
        
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

        $schedule = Schedule::find($id);

        if( $schedule->name != $request->name || $schedule->schedule_type_id != $request->schedule_type_id) {

            if ($this->check_request($request)){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This record already exists');
                return redirect()->action('Admin\ScheduleController@edit',[$id])->withInput();
            }

        }
        
        $schedule->name = $request->name;
        $schedule->schedule_type_id = $request->schedule_type_id;
        $schedule->status = $request->status;
        $schedule->updated_by=Auth::id();
        $schedule->push();

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
        Schedule::destroy($id); // 1 way

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ScheduleController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function schedule_content($id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['schedule'] = Schedule::find($id);

        $data['module_name'] = 'Schedule Contents Add';
        $data['title'] = 'Schedule Contents Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.schedule.contents', $data);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function schedule_content_add($schedule_id,$content_type_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['schedule'] = Schedule::find($schedule_id);

        if($content_type_id == 1)
        {
            $data['module_name'] = 'Schedule Batches Add';
            $data['title'] = 'Schedule Batches Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

            return redirect(url('admin/schedule-batch-list/'.$schedule_id));
        }

        if($content_type_id == 2)
        {
            $data['module_name'] = 'Schedule Faculties Add';
            $data['title'] = 'Schedule Faculties Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/schedule-faculty-list/'.$schedule_id));
        }

        if($content_type_id == 3)
        {
            $data['module_name'] = 'Schedule Disciplines Add';
            $data['title'] = 'Schedule Disciplines Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

            return redirect(url('admin/schedule-discipline-list/'.$schedule_id));
        }

        if($content_type_id == 4)
        {
            $data['module_name'] = 'Schedule Topics Add';
            $data['title'] = 'Schedule Topics Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/schedule-topic-list/'.$schedule_id));
        }

        if($content_type_id == 5)
        {
            $data['module_name'] = 'Schedule Program Type Add';
            $data['title'] = 'Schedule Program Type Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/schedule-program-type-list/'.$schedule_id));
        }
        
        if($content_type_id == 6)
        {
            $data['module_name'] = 'Schedule Media Type Add';
            $data['title'] = 'Schedule Media Type Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/schedule-media-type-list/'.$schedule_id));
        }    
        
    }
    
}  