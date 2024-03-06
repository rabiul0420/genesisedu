<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\ModuleAssign;
use App\ModuleLink;
use App\Module;
use App\ModuleDiscipline;
use App\ModuleFaculty;
use App\ModuleBatchModule;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\ModuleContent;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\CourseYear;
use App\CourseYearSession;
use App\LectureSheet;
use App\LectureVideo;
use App\ModuleSchedule;
use App\ModuleScheduleSlot;
use App\Program;
use App\ScheduleMediaType;
use App\ScheduleModuleType;
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


class ModuleController extends Controller
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

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['module_name'] = 'Module';
        $data['title'] = 'Module List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function module_list(Request $request)
    {        
        $module_list = DB::table('module as d1')->whereNull('d1.deleted_at')
                        ->leftJoin('schedule_module_type as d2','d2.id','d1.module_type_id')->whereNull('d2.deleted_at')
                        ->leftJoin('institutes as d3','d3.id','d1.institute_id')->whereNull('d3.deleted_at')
                        ->leftJoin('courses as d4','d4.id','d1.course_id')->whereNull('d4.deleted_at')
                        ->leftJoin('sessions as d5','d5.id','d1.session_id')->whereNull('d5.deleted_at')
                        ;
        $module_list = $module_list->select('d1.*','d2.name as module_type_name','d3.name as institute_name','d4.name as course_name','d5.name as session_name');

        $module_list = Module::with('institute','course','session','module_type')->whereHas('institute',function($institute) use($request){
            if($request->institute_id)
            {
                $institute->where('id' , $request->institute_id);
            }            
        })->whereHas('course',function($course) use($request){
            if($request->course_id)
            {
                $course->where('id' , $request->course_id);
            }            
        })->whereHas('session',function($session) use($request){
            if($request->session_id)
            {
                $session->where('id' , $request->session_id);
            }            
        });
                
        return Datatables::of($module_list)
            ->editColumn('status', function ($module_list) {
                return $module_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($module_list) {
                $data['module_list'] = $module_list;               
                return view('admin.module.module_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_module(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->active()->pluck('name','id');
        
        $view_name = $request->view_name;
        
        return  json_encode(array('course'=>view('admin.module.ajax.'.$view_name,['courses'=>$data['courses']])->render()), JSON_FORCE_OBJECT);

    }

    public function course_change_in_module(Request $request)
    {
        $years = CourseYear::where(['course_id'=>$request->course_id,'status'=>'1'])->distinct()->orderBy('year','desc')->pluck('year');

        $custom_years = array();
        if(isset($years) && count($years))
        {
            foreach($years as $year)
            {
                $custom_years[$year] = $year;
            }
        }

        $data['years'] = collect($custom_years);

        $view_name = $request->view_name;
        
        return  json_encode(array('year'=>view('admin.module.ajax.'.$view_name,['years'=>$data['years']])->render()), JSON_FORCE_OBJECT);


    }

    public function year_change_in_module(Request $request)
    {

        $data['sessions'] = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
            ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
            ->where('course_year.deleted_at',NULL)
            ->where('course_year_session.deleted_at',NULL)
            ->where('course_year.course_id',$request->course_id)
            ->where('course_year.year',$request->year)
            //->where('show_admission_form','yes')
            ->where('course_year.status',1)
            ->pluck('sessions.name',  'sessions.id');
        
            $view_name = $request->view_name;
        
            return  json_encode(array('session'=>view('admin.module.ajax.'.$view_name,['sessions'=>$data['sessions']])->render()), JSON_FORCE_OBJECT);

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
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);
        $data['module_types'] = ScheduleModuleType::pluck('name','id');
        $data['module_name'] = 'Module';
        $data['title'] = 'Module Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.module.create',$data);
        //echo "Module create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
        ]);
    }

    public function check_request($request)
    {
        $module = Module::where(['name'=>$request->name,'module_type_id'=>$request->module_type_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'year'=>$request->year,'session_id'=>$request->session_id])->first();
        if(isset($module))
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
            return redirect()->action('Admin\ModuleController@create')->withInput();
        }        

        if ($this->check_request($request)){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This record already exists');
            return redirect()->action('Admin\ModuleController@create')->withInput();
        }
        else
        {

            $module = new Module();
            $module->name = $request->name;
            $module->module_type_id = $request->module_type_id;
            $module->institute_id = $request->institute_id;
            $module->course_id = $request->course_id;
            $module->year = $request->year;
            $module->session_id = $request->session_id;
            $module->status = $request->status;
            $module->created_by = Auth::id();
            $module->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\ModuleController@index');
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
        $data['module']=Module::find($id);
        return view('admin.module.show',$data);
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
        $data['module'] = Module::find($id);
        $data['module_types'] = ScheduleModuleType::pluck('name','id');
        $data['media_types'] = ScheduleMediaType::pluck('name','id');
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id,'status'=>'1'])->pluck('name','id');
        
        $years = CourseYear::where(['course_id'=>$data['module']->course_id,'status'=>'1'])->distinct()->orderBy('year','desc')->pluck('year');

        $custom_years = array();
        if(isset($years) && count($years))
        {
            foreach($years as $year)
            {
                $custom_years[$year] = $year;
            }
        }

        $data['years'] = collect($custom_years);
        $data['sessions'] = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
            ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
            ->where('course_year.deleted_at',NULL)
            ->where('course_year_session.deleted_at',NULL)
            ->where('course_year.course_id',$data['module']->course_id)
            ->where('course_year.year',$data['module']->year)
            ->where('course_year.status',1)
            ->pluck('sessions.name',  'sessions.id');

        $data['module_name'] = 'Module';
        $data['title'] = 'Module Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.module.edit', $data);
        
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

        $module = Module::find($id);

        if( $module->name != $request->name || $module->module_type_id != $request->module_type_id || $module->institute_id != $request->institute_id || $module->course_id != $request->course_id || $module->year != $request->year || $module->session_id != $request->session_id ) {

            if ($this->check_request($request)){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This record already exists');
                return redirect()->action('Admin\ModuleController@edit',[$id])->withInput();
            }

        }
        
        $module->name = $request->name;
        $module->module_type_id = $request->module_type_id;
        $module->institute_id = $request->institute_id;
        $module->course_id = $request->course_id;
        $module->year = $request->year;
        $module->session_id = $request->session_id;
        $module->status = $request->status;
        $module->updated_by=Auth::id();
        $module->push();

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
        Module::destroy($id); // 1 way

        ModuleContent::where(['module_id'=>$id])->update(['deleted_by'=>Auth::id()]); // 1 way
        ModuleContent::where(['module_id'=>$id])->delete();

        $module_schedule_ids = ModuleSchedule::where(['module_id'=>$id])->pluck('id');
        ModuleSchedule::where(['module_id'=>$id])->update(['deleted_by'=>Auth::id()]); // 1 way
        ModuleSchedule::where(['module_id'=>$id])->delete();
        
        ModuleScheduleSlot::whereIn('module_schedule_id',$module_schedule_ids)->update(['deleted_by'=>Auth::id()]); // 1 way
        ModuleScheduleSlot::whereIn('module_schedule_id',$module_schedule_ids)->delete();
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ModuleController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function module_content($id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['module'] = Module::find($id);

        $data['module_name'] = 'Module Contents Add';
        $data['title'] = 'Module Contents Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module.contents', $data);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function module_content_add($module_id,$content_type_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['module'] = Module::find($module_id);

        if($content_type_id == 1)
        {
            $data['module_name'] = 'Module Batches Add';
            $data['title'] = 'Module Batches Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

            return redirect(url('admin/module-batch-list/'.$module_id));
        }

        if($content_type_id == 2)
        {
            $data['module_name'] = 'Module Faculties Add';
            $data['title'] = 'Module Faculties Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/module-faculty-list/'.$module_id));
        }

        if($content_type_id == 3)
        {
            $data['module_name'] = 'Module Disciplines Add';
            $data['title'] = 'Module Disciplines Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

            return redirect(url('admin/module-discipline-list/'.$module_id));
        }

        if($content_type_id == 4)
        {
            $data['module_name'] = 'Module Topics Add';
            $data['title'] = 'Module Topics Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/module-topic-list/'.$module_id));
        }

        if($content_type_id == 5)
        {
            $data['module_name'] = 'Module Program Type Add';
            $data['title'] = 'Module Program Type Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/module-program-type-list/'.$module_id));
        }
        
        if($content_type_id == 6)
        {
            $data['module_name'] = 'Module Media Type Add';
            $data['title'] = 'Module Media Type Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/module-media-type-list/'.$module_id));
        }
        
        if($content_type_id == 7)
        {
            $data['module_name'] = 'Module Program Add';
            $data['title'] = 'Module Program Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/module-program-list/'.$module_id));
        }
        
    }
    
    public function module_batch_list($module_id)
    {
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');
        $data['years'] = CourseYear::where(['course_id'=>$data['module']->course_id])->pluck('year','year');
        $session_ids = CourseYearSession::join('course_year','course_year_session.course_year_id','course_year.id')->pluck('session_id');
        $data['sessions'] = Sessions::whereIn('id',$session_ids)->pluck('name','id');
        $data['content_type'] = 1;      
        return view('admin.module.batch.module_batch_list', $data);

    }
    
    public function module_batch_ajax_list(Request $request)
    {
        $module_id = $request->module_id; 
        $module_batch_list = DB::table('batches as d1')->join('module_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','1')->where('d2.module_id',$module_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $module_batch_list = $module_batch_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name','d2.id as module_content_id','d2.deleted_at','d3.deleted_at','d4.deleted_at','d5.deleted_at');
        
        if($request->institute_id)
        {
            $module_batch_list = $module_batch_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $module_batch_list = $module_batch_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $module_batch_list = $module_batch_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $module_batch_list = $module_batch_list->where('d1.session_id',$request->session_id);
        }

        return Datatables::of($module_batch_list)
            ->addColumn('action', function ($module_batch_list) {

                $data['module_batch_list'] = $module_batch_list;
                
                return view('admin.module.batch.module_batch_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_module_batch(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->pluck('name','id');
        
        return view('admin.module.batch.ajax.module_batch_course',$data);

    }

    public function course_change_in_module_batch(Request $request)
    {
        $years = CourseYear::where('course_id',$request->course_id)->distinct()->orderBy('year','desc')->pluck('year');

        $custom_years = array();
        if(isset($years) && count($years))
        {
            foreach($years as $year)
            {
                $custom_years[$year] = $year;
            }
        }

        $data['years'] = collect($custom_years);
        
        return view('admin.module.batch.ajax.module_batch_year',$data);

    }

    public function year_change_in_module_batch(Request $request)
    {

        $data['sessions'] = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
            ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
            ->where('course_year.deleted_at',NULL)
            ->where('course_year_session.deleted_at',NULL)
            ->where('course_year.course_id',$request->course_id)
            ->where('course_year.year',$request->year)
            //->where('show_admission_form','yes')
            ->where('course_year.status',1)
            ->pluck('sessions.name',  'sessions.id');
        
        return view('admin.module.batch.ajax.module_batch_session',$data);

    }
    
    public function module_batch_add($module_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');
        $data['years'] = CourseYear::where(['course_id'=>$data['module']->course_id])->pluck('year','year');
        $session_ids = CourseYearSession::join('course_year','course_year_session.course_year_id','course_year.id')->pluck('session_id');
        $data['sessions'] = Sessions::whereIn('id',$session_ids)->pluck('name','id');

        $data['module_name'] = 'Module batch Add';
        $data['title'] = 'Module batch Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module.batch.module_batch_add', $data);
        
    }

    public function module_batch_add_list(Request $request)
    {
        $module_id = $request->module_id;
        $module_batch_list = DB::table('batches as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $module_batch_list = $module_batch_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name');
        $module_batch_list = $module_batch_list->addSelect(DB::raw($module_id." as module_id"));
        
        if($request->institute_id)
        {
            $module_batch_list = $module_batch_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $module_batch_list = $module_batch_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $module_batch_list = $module_batch_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $module_batch_list = $module_batch_list->where('d1.session_id',$request->session_id);
        }
                
        return Datatables::of($module_batch_list)
            ->addColumn('action', function ($module_batch_list) {
                $data['checked'] = "";
                $data['module_batch_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_batch_list->module_id,'content_type_id'=>'1','content_id'=>$module_batch_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_batch_list'] = $module_batch_list;
                
                return view('admin.module.batch.module_batch_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_batch_save(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['content_type_id'] = 1;
        $data['batch_id'] = $request->batch_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $module_batch = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id])->first();
            if(!isset($module_batch))
            {
                $module_batch = ModuleContent::insert([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id,'created_by'=>Auth::id()]);
                if(isset($module_batch))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added batch.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This batch already exist in this module !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $module_batch = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id])->update(['deleted_by'=>Auth::id()]);
            $module_batch = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id])->delete();
            if(isset($module_batch))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed batch.</span';
            }

        }
        
        return response()->json($data);

    }

    public function module_batch_edit($module_content_id)
    {
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['module'] = Module::find($data['module_content']->module->id);

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');
        $data['years'] = CourseYear::where(['course_id'=>$data['module']->course_id])->pluck('year','year');
        $session_ids = CourseYearSession::join('course_year','course_year_session.course_year_id','course_year.id')->pluck('session_id');
        $data['sessions'] = Sessions::whereIn('id',$session_ids)->pluck('name','id');

        $data['module_name'] = 'Module Mentor Edit';
        $data['title'] = 'Module Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module.batch.module_batch_edit', $data );
    }

    public function module_batch_edit_list(Request $request)
    {
        $module_id = $request->module_id;        
        $module_batch_list = DB::table('batches as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $module_batch_list = $module_batch_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name');
        $module_batch_list = $module_batch_list->addSelect(DB::raw($module_id." as module_id"));
        
        if($request->institute_id)
        {
            $module_batch_list = $module_batch_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $module_batch_list = $module_batch_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $module_batch_list = $module_batch_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $module_batch_list = $module_batch_list->where('d1.session_id',$request->session_id);
        }
                
        return Datatables::of($module_batch_list)
            ->addColumn('action', function ($module_batch_list) {
                $data['checked'] = "";
                $data['module_batch_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_batch_list->module_id,'content_type_id'=>'1','content_id'=>$module_batch_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_batch_list'] = $module_batch_list;
                
                return view('admin.module.batch.module_batch_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_batch_update(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['module_content_id'] = $request->module_content_id;
        $data['content_type_id'] = 1;
        $data['batch_id'] = $request->batch_id;         
        $data['status'] = "incomplete";
        
        $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->first();
        if(isset($module_content))
        {
            $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->update([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited batch.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function module_batch_delete($module_content_id)
    {
        
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Batch has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Batch remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/module-batch-list/'.$data['module_content']->module->id));
    }


    public function module_faculty_list($module_id)
    {
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');
        $data['content_type'] = 2;
        return view('admin.module.faculty.module_faculty_list', $data);

    }
    
    public function module_faculty_ajax_list(Request $request)
    {
        $module_id = $request->module_id; 
        $module_faculty_list = DB::table('faculties as d1')->join('module_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','2')->where('d2.module_id',$module_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at');
        $module_faculty_list = $module_faculty_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d2.id as module_content_id','d2.deleted_at','d3.deleted_at','d4.deleted_at');
        
        $module = Module::where(['id'=>$request->module_id])->first();
        if($module->institute_id == 16)
        {
            $module_faculty_list = $module_faculty_list->where('d1.institute_id','6');
            $module_faculty_list = $module_faculty_list->where('d1.course_id','13');
            $module_faculty_list = $module_faculty_list->where('d1.show_in_combined','1');
        }
        else
        {
            if($request->institute_id)
            {
                $module_faculty_list = $module_faculty_list->where('d1.institute_id',$request->institute_id);
            }

            if($request->course_id)
            {
                $module_faculty_list = $module_faculty_list->where('d1.course_id',$request->course_id);
            }

        }

        return Datatables::of($module_faculty_list)
            ->addColumn('action', function ($module_faculty_list) {

                $data['module_faculty_list'] = $module_faculty_list;
                
                return view('admin.module.faculty.module_faculty_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_module_faculty(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->pluck('name','id');
        
        return view('admin.module.faculty.ajax.module_faculty_course',$data);

    }
    
    public function module_faculty_add($module_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');

        $data['module_name'] = 'Module faculty Add';
        $data['title'] = 'Module faculty Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module.faculty.module_faculty_add', $data);
        
    }

    public function module_faculty_add_list(Request $request)
    {
        $module_id = $request->module_id;
        $module_faculty_list = DB::table('faculties as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at');
        $module_faculty_list = $module_faculty_list->select('d1.*','d3.name as institute_name','d4.name as course_name');
        $module_faculty_list = $module_faculty_list->addSelect(DB::raw($module_id." as module_id"));
        
        $module = Module::where(['id'=>$request->module_id])->first();
        if($module->institute_id == 16)
        {
            $module_faculty_list = $module_faculty_list->where('d1.institute_id','6');
            $module_faculty_list = $module_faculty_list->where('d1.course_id','13');
            $module_faculty_list = $module_faculty_list->where('d1.show_in_combined','1');
        }
        else
        {
            if($request->institute_id)
            {
                $module_faculty_list = $module_faculty_list->where('d1.institute_id',$request->institute_id);
            }

            if($request->course_id)
            {
                $module_faculty_list = $module_faculty_list->where('d1.course_id',$request->course_id);
            }

        }
        
                
        return Datatables::of($module_faculty_list)
            ->addColumn('action', function ($module_faculty_list) {
                $data['checked'] = "";
                $data['module_faculty_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_faculty_list->module_id,'content_type_id'=>'2','content_id'=>$module_faculty_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_faculty_list'] = $module_faculty_list;
                
                return view('admin.module.faculty.module_faculty_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_faculty_save(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['content_type_id'] = 2;
        $data['faculty_id'] = $request->faculty_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $module_faculty = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->faculty_id])->first();
            if(!isset($module_faculty))
            {
                $module_faculty = ModuleContent::insert([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->faculty_id,'created_by'=>Auth::id()]);
                if(isset($module_faculty))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added faculty.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This faculty already exist in this module !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $module_faculty = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->faculty_id])->update(['deleted_by'=>Auth::id()]);
            $module_faculty = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->faculty_id])->delete();
            if(isset($module_faculty))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed faculty.</span';
            }

        }
        
        return response()->json($data);

    }

    public function module_faculty_edit($module_content_id)
    {
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['module'] = Module::find($data['module_content']->module->id);

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');

        $data['module_name'] = 'Module Mentor Edit';
        $data['title'] = 'Module Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module.faculty.module_faculty_edit', $data );
    }

    public function module_faculty_edit_list(Request $request)
    {
        $module_id = $request->module_id;        
        $module_faculty_list = DB::table('faculties as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at');
        $module_faculty_list = $module_faculty_list->select('d1.*','d3.name as institute_name','d4.name as course_name');
        $module_faculty_list = $module_faculty_list->addSelect(DB::raw($module_id." as module_id"));
        
        $module = Module::where(['id'=>$request->module_id])->first();
        if($module->institute_id == 16)
        {
            $module_faculty_list = $module_faculty_list->where('d1.institute_id','6');
            $module_faculty_list = $module_faculty_list->where('d1.course_id','13');
            $module_faculty_list = $module_faculty_list->where('d1.show_in_combined','1');
        }
        else
        {
            if($request->institute_id)
            {
                $module_faculty_list = $module_faculty_list->where('d1.institute_id',$request->institute_id);
            }

            if($request->course_id)
            {
                $module_faculty_list = $module_faculty_list->where('d1.course_id',$request->course_id);
            }

        }
                
        return Datatables::of($module_faculty_list)
            ->addColumn('action', function ($module_faculty_list) {
                $data['checked'] = "";
                $data['module_faculty_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_faculty_list->module_id,'content_type_id'=>'2','content_id'=>$module_faculty_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_faculty_list'] = $module_faculty_list;
                
                return view('admin.module.faculty.module_faculty_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_faculty_update(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['module_content_id'] = $request->module_content_id;
        $data['content_type_id'] = 2;
        $data['faculty_id'] = $request->faculty_id;         
        $data['status'] = "incomplete";
        
        $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->first();
        if(isset($module_content))
        {
            $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->update([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->faculty_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited faculty.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function module_faculty_delete($module_content_id)
    {
        
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Batch has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Batch remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/module-faculty-list/'.$data['module_content']->module->id));
    }

    public function module_discipline_list($module_id)
    {
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');
        $data['content_type'] = 3;
        return view('admin.module.discipline.module_discipline_list', $data);

    }
    
    public function module_discipline_ajax_list(Request $request)
    {
        $module_id = $request->module_id; 
        $module_discipline_list = DB::table('subjects as d1')->join('module_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','3')->where('d2.module_id',$module_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at');
        $module_discipline_list = $module_discipline_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d2.id as module_content_id','d2.deleted_at','d3.deleted_at','d4.deleted_at');
        
        $module = Module::where(['id'=>$request->module_id])->first();
        if($module->institute_id == 16)
        {
            $module_discipline_list = $module_discipline_list->where('d1.institute_id','4');
            $module_discipline_list = $module_discipline_list->where('d1.course_id','19');
            $module_discipline_list = $module_discipline_list->where('d1.show_in_combined','1');
        }
        else
        {
            if($request->institute_id)
            {
                $module_discipline_list = $module_discipline_list->where('d1.institute_id',$request->institute_id);
            }

            if($request->course_id)
            {
                $module_discipline_list = $module_discipline_list->where('d1.course_id',$request->course_id);
            }

        }

        return Datatables::of($module_discipline_list)
            ->addColumn('action', function ($module_discipline_list) {

                $data['module_discipline_list'] = $module_discipline_list;
                
                return view('admin.module.discipline.module_discipline_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_module_discipline(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->pluck('name','id');
        
        return view('admin.module.discipline.ajax.module_discipline_course',$data);

    }
    
    public function module_discipline_add($module_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');

        $data['module_name'] = 'Module discipline Add';
        $data['title'] = 'Module discipline Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module.discipline.module_discipline_add', $data);
        
    }

    public function module_discipline_add_list(Request $request)
    {
        $module_id = $request->module_id;
        $module_discipline_list = DB::table('subjects as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at');
        $module_discipline_list = $module_discipline_list->select('d1.*','d3.name as institute_name','d4.name as course_name');
        $module_discipline_list = $module_discipline_list->addSelect(DB::raw($module_id." as module_id"));
        
        $module = Module::where(['id'=>$request->module_id])->first();
        if($module->institute_id == 16)
        {
            $module_discipline_list = $module_discipline_list->where('d1.institute_id','4');
            $module_discipline_list = $module_discipline_list->where('d1.course_id','19');
            $module_discipline_list = $module_discipline_list->where('d1.show_in_combined','1');
        }
        else
        {
            if($request->institute_id)
            {
                $module_discipline_list = $module_discipline_list->where('d1.institute_id',$request->institute_id);
            }

            if($request->course_id)
            {
                $module_discipline_list = $module_discipline_list->where('d1.course_id',$request->course_id);
            }

        }
                
        return Datatables::of($module_discipline_list)
            ->addColumn('action', function ($module_discipline_list) {
                $data['checked'] = "";
                $data['module_discipline_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_discipline_list->module_id,'content_type_id'=>'3','content_id'=>$module_discipline_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_discipline_list'] = $module_discipline_list;
                
                return view('admin.module.discipline.module_discipline_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_discipline_save(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['content_type_id'] = 3;
        $data['discipline_id'] = $request->discipline_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $module_discipline = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->discipline_id])->first();
            if(!isset($module_discipline))
            {
                $module_discipline = ModuleContent::insert([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->discipline_id,'created_by'=>Auth::id()]);
                if(isset($module_discipline))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added discipline.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This discipline already exist in this module !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $module_discipline = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->discipline_id])->update(['deleted_by'=>Auth::id()]);
            $module_discipline = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->discipline_id])->delete();
            if(isset($module_discipline))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed discipline.</span';
            }

        }
        
        return response()->json($data);

    }

    public function module_discipline_edit($module_content_id)
    {
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['module'] = Module::find($data['module_content']->module->id);

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['module']->institute_id])->pluck('name','id');

        $data['module_name'] = 'Module Mentor Edit';
        $data['title'] = 'Module Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module.discipline.module_discipline_edit', $data );
    }

    public function module_discipline_edit_list(Request $request)
    {
        $module_id = $request->module_id;        
        $module_discipline_list = DB::table('subjects as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at');
        $module_discipline_list = $module_discipline_list->select('d1.*','d3.name as institute_name','d4.name as course_name');
        $module_discipline_list = $module_discipline_list->addSelect(DB::raw($module_id." as module_id"));
        
        $module = Module::where(['id'=>$request->module_id])->first();
        if($module->institute_id == 16)
        {
            $module_discipline_list = $module_discipline_list->where('d1.institute_id','4');
            $module_discipline_list = $module_discipline_list->where('d1.course_id','19');
            $module_discipline_list = $module_discipline_list->where('d1.show_in_combined','1');
        }
        else
        {
            if($request->institute_id)
            {
                $module_discipline_list = $module_discipline_list->where('d1.institute_id',$request->institute_id);
            }

            if($request->course_id)
            {
                $module_discipline_list = $module_discipline_list->where('d1.course_id',$request->course_id);
            }

        }
                
        return Datatables::of($module_discipline_list)
            ->addColumn('action', function ($module_discipline_list) {
                $data['checked'] = "";
                $data['module_discipline_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_discipline_list->module_id,'content_type_id'=>'3','content_id'=>$module_discipline_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_discipline_list'] = $module_discipline_list;
                
                return view('admin.module.discipline.module_discipline_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_discipline_update(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['module_content_id'] = $request->module_content_id;
        $data['content_type_id'] = 3;
        $data['discipline_id'] = $request->discipline_id;         
        $data['status'] = "incomplete";
        
        $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->first();
        if(isset($module_content))
        {
            $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->update([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->discipline_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited discipline.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function module_discipline_delete($module_content_id)
    {
        
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Discipline has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Discipline remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/module-discipline-list/'.$data['module_content']->module->id));
    }

    public function module_topic_list($module_id)
    {
        $data['module'] = Module::where(['id'=>$module_id])->first();
        
        $data['content_type'] = 4;
        return view('admin.module.topic.module_topic_list', $data);

    }

    public function module_topic_ajax_list(Request $request)
    {
        $module_id = $request->module_id; 
        $module_topic_list = DB::table('topic as d1')->join('module_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','4')->where('d2.module_id',$module_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $module_topic_list = $module_topic_list->select('d1.*','d2.id as module_content_id','d2.deleted_at');

        return Datatables::of($module_topic_list)
            ->addColumn('action', function ($module_topic_list) {

                $data['module_topic_list'] = $module_topic_list;
                
                return view('admin.module.topic.module_topic_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function module_topic_add($module_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['module_name'] = 'Module topic Add';
        $data['title'] = 'Module topic Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module.topic.module_topic_add', $data);
        
    }

    public function module_topic_add_list(Request $request)
    {
        $module_id = $request->module_id;
        $module_topic_list = DB::table('topic as d1')->whereNull('d1.deleted_at');
        $module_topic_list = $module_topic_list->select('d1.*');
        $module_topic_list = $module_topic_list->addSelect(DB::raw($module_id." as module_id"));
                
        return Datatables::of($module_topic_list)
            ->addColumn('action', function ($module_topic_list) {
                $data['checked'] = "";
                $data['module_topic_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_topic_list->module_id,'content_type_id'=>'4','content_id'=>$module_topic_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_topic_list'] = $module_topic_list;
                
                return view('admin.module.topic.module_topic_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_topic_save(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['content_type_id'] = 4;
        $data['topic_id'] = $request->topic_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $module_topic = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id])->first();
            if(!isset($module_topic))
            {
                $module_topic = ModuleContent::insert([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id,'created_by'=>Auth::id()]);
                if(isset($module_topic))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added topic.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This topic already exist in this module !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $module_topic = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id])->update(['deleted_by'=>Auth::id()]);
            $module_topic = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id])->delete();
            if(isset($module_topic))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed topic.</span';
            }

        }
        
        return response()->json($data);

    }

    public function module_topic_edit($module_content_id)
    {
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['module'] = Module::find($data['module_content']->module->id);

        $data['module_name'] = 'Module Mentor Edit';
        $data['title'] = 'Module Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module.topic.module_topic_edit', $data );
    }

    public function module_topic_edit_list(Request $request)
    {
        $module_id = $request->module_id;        
        $module_topic_list = DB::table('topic as d1')->whereNull('d1.deleted_at');
        $module_topic_list = $module_topic_list->select('d1.*');
        $module_topic_list = $module_topic_list->addSelect(DB::raw($module_id." as module_id"));
                
        return Datatables::of($module_topic_list)
            ->addColumn('action', function ($module_topic_list) {
                $data['checked'] = "";
                $data['module_topic_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_topic_list->module_id,'content_type_id'=>'4','content_id'=>$module_topic_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_topic_list'] = $module_topic_list;
                
                return view('admin.module.topic.module_topic_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_topic_update(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['module_content_id'] = $request->module_content_id;
        $data['content_type_id'] = 4;
        $data['topic_id'] = $request->topic_id;         
        $data['status'] = "incomplete";
        
        $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->first();
        if(isset($module_content))
        {
            $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->update([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited topic.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function module_topic_delete($module_content_id)
    {
        
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Discipline has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Discipline remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/module-topic-list/'.$data['module_content']->module->id));
    }

    public function module_program_type_list($module_id)
    {
        $data['module'] = Module::where(['id'=>$module_id])->first();
        
        $data['content_type'] = 5;
        return view('admin.module.program_type.module_program_type_list', $data);

    }

    public function module_program_type_ajax_list(Request $request)
    {
        $module_id = $request->module_id; 
        $module_program_type_list = DB::table('schedule_program_type as d1')->join('module_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','5')->where('d2.module_id',$module_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $module_program_type_list = $module_program_type_list->select('d1.*','d2.id as module_content_id','d2.deleted_at');

        return Datatables::of($module_program_type_list)
            ->addColumn('action', function ($module_program_type_list) {

                $data['module_program_type_list'] = $module_program_type_list;
                
                return view('admin.module.program_type.module_program_type_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function module_program_type_add($module_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['module_name'] = 'Module program_type Add';
        $data['title'] = 'Module program_type Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module.program_type.module_program_type_add', $data);
        
    }

    public function module_program_type_add_list(Request $request)
    {
        $module_id = $request->module_id;
        $module_program_type_list = DB::table('schedule_program_type as d1')->whereNull('d1.deleted_at');
        $module_program_type_list = $module_program_type_list->select('d1.*');
        $module_program_type_list = $module_program_type_list->addSelect(DB::raw($module_id." as module_id"));
                
        return Datatables::of($module_program_type_list)
            ->addColumn('action', function ($module_program_type_list) {
                $data['checked'] = "";
                $data['module_program_type_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_program_type_list->module_id,'content_type_id'=>'5','content_id'=>$module_program_type_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_program_type_list'] = $module_program_type_list;
                
                return view('admin.module.program_type.module_program_type_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_program_type_save(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['content_type_id'] = 5;
        $data['program_type_id'] = $request->program_type_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $module_program_type = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_type_id])->first();
            if(!isset($module_program_type))
            {
                $module_program_type = ModuleContent::insert([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_type_id,'created_by'=>Auth::id()]);
                if(isset($module_program_type))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added program type.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This program type already exist in this module !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $module_program_type = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_type_id])->update(['deleted_by'=>Auth::id()]);
            $module_program_type = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_type_id])->delete();
            if(isset($module_program_type))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed program type.</span';
            }

        }
        
        return response()->json($data);

    }

    public function module_program_type_edit($module_content_id)
    {
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['module'] = Module::find($data['module_content']->module->id);

        $data['module_name'] = 'Module Mentor Edit';
        $data['title'] = 'Module Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module.program_type.module_program_type_edit', $data );
    }

    public function module_program_type_edit_list(Request $request)
    {
        $module_id = $request->module_id;        
        $module_program_type_list = DB::table('schedule_program_type as d1')->whereNull('d1.deleted_at');
        $module_program_type_list = $module_program_type_list->select('d1.*');
        $module_program_type_list = $module_program_type_list->addSelect(DB::raw($module_id." as module_id"));
                
        return Datatables::of($module_program_type_list)
            ->addColumn('action', function ($module_program_type_list) {
                $data['checked'] = "";
                $data['module_program_type_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_program_type_list->module_id,'content_type_id'=>'5','content_id'=>$module_program_type_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_program_type_list'] = $module_program_type_list;
                
                return view('admin.module.program_type.module_program_type_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_program_type_update(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['module_content_id'] = $request->module_content_id;
        $data['content_type_id'] = 5;
        $data['program_type_id'] = $request->program_type_id;         
        $data['status'] = "incomplete";
        
        $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->first();
        if(isset($module_content))
        {
            $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->update([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_type_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited program type.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function module_program_type_delete($module_content_id)
    {
        
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Discipline has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Discipline remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/module-program-type-list/'.$data['module_content']->module->id));
    }

    public function module_media_type_list($module_id)
    {
        $data['module'] = Module::where(['id'=>$module_id])->first();
        
        $data['content_type'] = 6;
        return view('admin.module.media_type.module_media_type_list', $data);

    }

    public function module_media_type_ajax_list(Request $request)
    {
        $module_id = $request->module_id; 
        $module_media_type_list = DB::table('schedule_media_type as d1')->join('module_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','6')->where('d2.module_id',$module_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $module_media_type_list = $module_media_type_list->select('d1.*','d2.id as module_content_id','d2.deleted_at');

        return Datatables::of($module_media_type_list)
            ->addColumn('action', function ($module_media_type_list) {

                $data['module_media_type_list'] = $module_media_type_list;
                
                return view('admin.module.media_type.module_media_type_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function module_media_type_add($module_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['module_name'] = 'Module media_type Add';
        $data['title'] = 'Module media_type Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module.media_type.module_media_type_add', $data);
        
    }

    public function module_media_type_add_list(Request $request)
    {
        $module_id = $request->module_id;
        $module_media_type_list = DB::table('schedule_media_type as d1')->whereNull('d1.deleted_at');
        $module_media_type_list = $module_media_type_list->select('d1.*');
        $module_media_type_list = $module_media_type_list->addSelect(DB::raw($module_id." as module_id"));
                
        return Datatables::of($module_media_type_list)
            ->addColumn('action', function ($module_media_type_list) {
                $data['checked'] = "";
                $data['module_media_type_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_media_type_list->module_id,'content_type_id'=>'6','content_id'=>$module_media_type_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_media_type_list'] = $module_media_type_list;
                
                return view('admin.module.media_type.module_media_type_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_media_type_save(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['content_type_id'] = 6;
        $data['media_type_id'] = $request->media_type_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $module_media_type = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id])->first();
            if(!isset($module_media_type))
            {
                $module_media_type = ModuleContent::insert([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id,'created_by'=>Auth::id()]);
                if(isset($module_media_type))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added media type.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This media type already exist in this module !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $module_media_type = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id])->update(['deleted_by'=>Auth::id()]);
            $module_media_type = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id])->delete();
            if(isset($module_media_type))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed media type.</span';
            }

        }
        
        return response()->json($data);

    }

    public function module_media_type_edit($module_content_id)
    {
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['module'] = Module::find($data['module_content']->module->id);

        $data['module_name'] = 'Module Mentor Edit';
        $data['title'] = 'Module Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module.media_type.module_media_type_edit', $data );
    }

    public function module_media_type_edit_list(Request $request)
    {
        $module_id = $request->module_id;        
        $module_media_type_list = DB::table('schedule_media_type as d1')->whereNull('d1.deleted_at');
        $module_media_type_list = $module_media_type_list->select('d1.*');
        $module_media_type_list = $module_media_type_list->addSelect(DB::raw($module_id." as module_id"));
                
        return Datatables::of($module_media_type_list)
            ->addColumn('action', function ($module_media_type_list) {
                $data['checked'] = "";
                $data['module_media_type_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$module_media_type_list->module_id,'content_type_id'=>'6','content_id'=>$module_media_type_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_media_type_list'] = $module_media_type_list;
                
                return view('admin.module.media_type.module_media_type_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_media_type_update(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['module_content_id'] = $request->module_content_id;
        $data['content_type_id'] = 6;
        $data['media_type_id'] = $request->media_type_id;         
        $data['status'] = "incomplete";
        
        $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->first();
        if(isset($module_content))
        {
            $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->update([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited media type.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function module_media_type_delete($module_content_id)
    {
        
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Discipline has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Discipline remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/module-media-type-list/'.$data['module_content']->module->id));
    }


    public function module_program_list($module_id)
    {
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['module'] = Module::where(['id'=>$module_id])->first();
        
        $data['content_type'] = 7;
        return view('admin.module.program.module_program_list', $data);

    }

    public function module_program_ajax_list(Request $request)
    {
        // $module_id = $request->module_id; 
        // $module_program_list = DB::table('program as d1')->join('module_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','7')->where('d2.module_id',$module_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        // $module_program_list = $module_program_list->select('d1.*','d2.id as module_content_id','d2.deleted_at');
        
        $module = Module::where(['id'=>$request->module_id])->first();

        $module_program_list = Program::with(['institute','course','session','program_type'])->join('module_content','module_content.content_id','program.id')->select('program.*','module_content.id as module_content_id')->where(['module_content.content_type_id'=>'7','module_content.module_id'=>$module->id])->whereNull('module_content.deleted_at')->whereHas('institute',function($institute) use($request){
            if($request->institute_id)
            {
                $institute->where('id' , $request->institute_id);
            }            
        })->whereHas('course',function($course) use($request){
            if($request->course_id)
            {
                $course->where('id' , $request->course_id);
            }            
        })->whereHas('session',function($session) use($request){
            if($request->session_id)
            {
                $session->where('id' , $request->session_id);
            }            
        });

        return Datatables::of($module_program_list)
            ->addColumn('action', function ($module_program_list) {

                $data['module_program_list'] = $module_program_list;
                
                return view('admin.module.program.module_program_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function module_program_add($module_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['module'] = Module::where(['id'=>$module_id])->first();
        $data['module_name'] = 'Module Program Add';
        $data['title'] = 'Module Program Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.module.program.module_program_add', $data);
        
    }

    public function module_program_add_list(Request $request)
    {
        // $module_id = $request->module_id;
        // $module_program_list = DB::table('program as d1')->whereNull('d1.deleted_at');
        // $module_program_list = $module_program_list->select('d1.*');
        // $module_program_list = $module_program_list->addSelect(DB::raw($module_id." as module_id"));

        $module = Module::where(['id'=>$request->module_id])->first();

        $module_program_list = Program::with(['institute','course','session','program_type'])->where(['institute_id'=>$module->institute_id,'course_id'=>$module->course_id,'year'=>$module->year,'session_id'=>$module->session_id]);
                        
        return Datatables::of($module_program_list)
            ->addColumn('action', function ($module_program_list) use($request){
                $data['checked'] = "";
                $data['module_program_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$request->module_id,'content_type_id'=>'7','content_id'=>$module_program_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_program_list'] = $module_program_list;
                
                return view('admin.module.program.module_program_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_program_save(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['content_type_id'] = 7;
        $data['program_id'] = $request->program_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $module_program = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_id])->first();
            if(!isset($module_program))
            {
                $module_program = ModuleContent::insert([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_id,'created_by'=>Auth::id()]);
                if(isset($module_program))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added program.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This program already exist in this module !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $module_program = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_id])->update(['deleted_by'=>Auth::id()]);
            $module_program = ModuleContent::where([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_id])->delete();
            if(isset($module_program))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed program.</span';
            }

        }
        
        return response()->json($data);

    }

    public function module_program_edit($module_content_id)
    {
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['module'] = Module::find($data['module_content']->module->id);

        $data['module_name'] = 'Module Mentor Edit';
        $data['title'] = 'Module Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.module.program.module_program_edit', $data );
    }

    public function module_program_edit_list(Request $request)
    {
        // $module_id = $request->module_id;        
        // $module_program_list = DB::table('program as d1')->whereNull('d1.deleted_at');
        // $module_program_list = $module_program_list->select('d1.*');
        // $module_program_list = $module_program_list->addSelect(DB::raw($module_id." as module_id"));

        $module = Module::where(['id'=>$request->module_id])->first();

        $module_program_list = Program::with(['institute','course','session','program_type'])->where(['institute_id'=>$module->institute_id,'course_id'=>$module->course_id,'year'=>$module->year,'session_id'=>$module->session_id]);
        
                
        return Datatables::of($module_program_list)
            ->addColumn('action', function ($module_program_list) use ($request){
                $data['checked'] = "";
                $data['module_program_add_info'] = "";
                
                $module_content = ModuleContent::where(['module_id'=>$request->module_id,'content_type_id'=>'7','content_id'=>$module_program_list->id])->first();
                if(isset($module_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['module_program_list'] = $module_program_list;
                
                return view('admin.module.program.module_program_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function module_program_update(Request $request)
    {
        $data['module_id'] = $request->module_id;
        $data['module_content_id'] = $request->module_content_id;
        $data['content_type_id'] = 7;
        $data['program_id'] = $request->program_id;         
        $data['status'] = "incomplete";
        
        $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->first();
        if(isset($module_content))
        {
            $module_content = ModuleContent::where(['id'=>$data['module_content_id']])->update([ 'module_id' => $request->module_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->program_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited program.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function module_program_delete($module_content_id)
    {
        
        $data['module_content'] = ModuleContent::where(['id'=>$module_content_id])->first();
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ModuleContent::where(['id'=>$module_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Program has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Program remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/module-program-list/'.$data['module_content']->module->id));
    }

    public function module_schedule($module_id)
    {
        $data['module'] = Module::where(['id'=>$module_id])->first();
        
        return redirect(url('admin/module-program-list/'.$data['module_content']->module->id));
    }

    public function module_schedule_save(Request $request)
    {
        $module_schedule = new ModuleSchedule();        
        $module_schedule->module_id = $request->module_id;
        $module_schedule->name = $request->name;
        $module_schedule->schedule_info = $request->schedule_info;
        $module_schedule->contact_details = $request->contact_details;
        $module_schedule->address = $request->address;
        $module_schedule->terms_and_condition = $request->terms_and_condition;        
        $module_schedule->status = $request->status;
        $module_schedule->created_by = Auth::id();
        
        return redirect(url('admin/module-program-list/'.$data['module_content']->module->id));
    }


    
    
}  