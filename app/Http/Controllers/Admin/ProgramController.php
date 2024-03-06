<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\ProgramAssign;
use App\ProgramLink;
use App\Program;
use App\ProgramDiscipline;
use App\ProgramFaculty;
use App\ProgramBatchProgram;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\ProgramContent;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\CourseYear;
use App\CourseYearSession;
use App\LectureSheet;
use App\LectureVideo;
use App\ModuleContent;
use App\ModuleScheduleSlot;
use App\ScheduleMediaType;
use App\ScheduleProgramType;
use App\Teacher;
use App\Topic;
use App\TopicContent;
use App\Topics;
use Session;
use Auth;
use Illuminate\Support\Collection;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;


class ProgramController extends Controller
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

        $data['topics'] = Program::get();
        $data['module_name'] = 'Program';
        $data['title'] = 'Program List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function program_list(Request $request)
    {

        $program_list = Program::with(['program_type','institute','course','session']);

        if($request->institute_id)
        {
            $program_list->where('institute_id', $request->institute_id);
        }

        if($request->course_id)
        {
            $program_list->where('course_id', $request->course_id);
        }

        if($request->year)
        {
            $program_list->where('year',$request->year);
        }

        if($request->session_id)
        {
            $program_list->where('session_id', $request->session_id);
        }
                
        return Datatables::of($program_list)
            ->editColumn('status', function ($program_list) {
                return $program_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($program_list) {
                $data['program_list'] = $program_list;               
                return view('admin.program.program_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_program(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->active()->pluck('name','id');
        
        $view_name = $request->view_name;
        
        return  json_encode(array('course'=>view('admin.program.ajax.'.$view_name,['courses'=>$data['courses']])->render()), JSON_FORCE_OBJECT);

    }

    public function course_change_in_program(Request $request)
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
        
        return  json_encode(array('year'=>view('admin.program.ajax.'.$view_name,['years'=>$data['years']])->render()), JSON_FORCE_OBJECT);


    }

    public function year_change_in_program(Request $request)
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
        
            return  json_encode(array('session'=>view('admin.program.ajax.'.$view_name,['sessions'=>$data['sessions']])->render()), JSON_FORCE_OBJECT);

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

        $data['program_types'] = ScheduleProgramType::pluck('name','id');
        $data['media_types'] = ScheduleMediaType::pluck('name','id');

        $data['module_name'] = 'Program';
        $data['title'] = 'Program Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.program.create',$data);
        //echo "Program create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
            'program_type_id' => ['required'],
        ]);
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
            return redirect()->action('Admin\ProgramController@create')->withInput();
        }        

        if (Program::where(['name'=>$request->name,'program_type_id'=>$request->program_type_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Program name already exists');
            return redirect()->action('Admin\ProgramController@create')->withInput();
        }
        else
        {

            $program = new Program();
            $program->name = $request->name;
            $program->nickname = $request->nickname;
            $program->program_type_id = $request->program_type_id;
            $program->institute_id = $request->institute_id;
            $program->course_id = $request->course_id;
            $program->year = $request->year;
            $program->session_id = $request->session_id;
            $program->status = $request->status;
            $program->created_by = Auth::id();
            $program->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\ProgramController@index');
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
        $data['program']=Program::find($id);
        return view('admin.program.show',$data);
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
        $data['program'] = Program::find($id);
        $data['program_types'] = ScheduleProgramType::pluck('name','id');
        $data['media_types'] = ScheduleMediaType::pluck('name','id');

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::where(['institute_id'=>$data['program']->institute_id,'status'=>'1'])->pluck('name','id');
        
        $years = CourseYear::where(['course_id'=>$data['program']->course_id,'status'=>'1'])->distinct()->orderBy('year','desc')->pluck('year');

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
            ->where('course_year.course_id',$data['program']->course_id)
            ->where('course_year.year',$data['program']->year)
            ->where('course_year.status',1)
            ->pluck('sessions.name',  'sessions.id');

        $data['module_name'] = 'Program';
        $data['title'] = 'Program Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.program.edit', $data);
        
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

        $program = Program::find($id);

        if($program->name != $request->name) {

            if (Program::where(['name'=>$request->name,'program_type_id'=>$request->program_type_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Program name already exists');
                return redirect()->action('Admin\ProgramController@edit',[$id])->withInput();
            }

        }

        
        $program->name = $request->name;
        $program->nickname = $request->nickname;
        $program->program_type_id = $request->program_type_id;
        $program->institute_id = $request->institute_id;
        $program->course_id = $request->course_id;
        $program->year = $request->year;
        $program->session_id = $request->session_id;
        $program->status = $request->status;
        $program->updated_by=Auth::id();
        $program->push();

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
        Program::destroy($id); // 1 way

        ProgramContent::where(['program_id'=>$id])->update(['deleted_by'=>Auth::id()]); // 1 way
        ProgramContent::where(['program_id'=>$id])->delete();
        ModuleContent::where(['content_type_id'=>'7','content_id'=>$id])->update(['deleted_by'=>Auth::id()]);
        ModuleContent::where(['content_type_id'=>'7','content_id'=>$id])->delete();
        ModuleScheduleSlot::where(['program_id'=>$id])->update(['program_id'=>'']); 
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ProgramController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function program_content($id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::find($id);

        $data['module_name'] = 'Program Contents Add';
        $data['title'] = 'Program Contents Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.contents', $data);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function program_content_add($program_id,$content_type_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::find($program_id);

        if($content_type_id == 1)
        {
            $data['module_name'] = 'Program Media Type Add';
            $data['title'] = 'Program Media Type Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

            return redirect(url('admin/program-media-type-list/'.$program_id));
        }

        if($content_type_id == 2)
        {
            $data['module_name'] = 'Program Topics Add';
            $data['title'] = 'Program Topics Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

            return redirect(url('admin/program-topic-list/'.$program_id));
        }

        if($content_type_id == 3)
        {
            $data['module_name'] = 'Program Mentors Add';
            $data['title'] = 'Program Mentors Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/program-mentor-list/'.$program_id));
        }

        if($content_type_id == 4)
        {
            $data['module_name'] = 'Program Lecture Videos Add';
            $data['title'] = 'Program Lecture Videos Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/program-lecture-video-list/'.$program_id));
        }

        if($content_type_id == 5)
        {
            $data['module_name'] = 'Program Exams Add';
            $data['title'] = 'Program Exams Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/program-exam-list/'.$program_id));
        }

        if($content_type_id == 6)
        {
            $data['module_name'] = 'Program Lecture Sheets Add';
            $data['title'] = 'Program Lecture Sheets Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/program-content/'.$program_id));
            //return redirect(url('admin/program-lecture-sheet-list/'.$program_id));
        }
        
        if($content_type_id == 7)
        {
            $data['module_name'] = 'Program Batch Add';
            $data['title'] = 'Program Batch Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/program-batch-list/'.$program_id));
        }
        
    }

    public function program_media_type_list($program_id)
    {
        $data['program'] = Program::where(['id'=>$program_id])->first();
        
        $data['content_type'] = 1;
        return view('admin.program.media_type.program_media_type_list', $data);

    }

    public function program_media_type_ajax_list(Request $request)
    {
        $program_id = $request->program_id; 
        $program_media_type_list = DB::table('schedule_media_type as d1')->join('program_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','1')->where('d2.program_id',$program_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $program_media_type_list = $program_media_type_list->select('d1.*','d2.id as program_content_id','d2.deleted_at');

        return Datatables::of($program_media_type_list)
            ->addColumn('action', function ($program_media_type_list) {

                $data['program_media_type_list'] = $program_media_type_list;
                
                return view('admin.program.media_type.program_media_type_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function program_media_type_add($program_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['program_name'] = 'Program media_type Add';
        $data['title'] = 'Program media_type Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.media_type.program_media_type_add', $data);
        
    }

    public function program_media_type_add_list(Request $request)
    {
        $program_id = $request->program_id;
        $program_media_type_list = DB::table('schedule_media_type as d1')->whereNull('d1.deleted_at');
        $program_media_type_list = $program_media_type_list->select('d1.*');
        $program_media_type_list = $program_media_type_list->addSelect(DB::raw($program_id." as program_id"));
                
        return Datatables::of($program_media_type_list)
            ->addColumn('action', function ($program_media_type_list) {
                $data['checked'] = "";
                $data['program_media_type_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_media_type_list->program_id,'content_type_id'=>'1','content_id'=>$program_media_type_list->id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_media_type_list'] = $program_media_type_list;
                
                return view('admin.program.media_type.program_media_type_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_media_type_save(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['content_type_id'] = 1;
        $data['media_type_id'] = $request->media_type_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $program_media_type = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id])->first();
            if(!isset($program_media_type))
            {
                $program_media_type = ProgramContent::insert([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id,'created_by'=>Auth::id()]);
                if(isset($program_media_type))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added media type.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This media type already exist in this program !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $program_media_type = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id])->update(['deleted_by'=>Auth::id()]);
            $program_media_type = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id])->delete();
            if(isset($program_media_type))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed media type.</span';
            }

        }
        
        return response()->json($data);

    }

    public function program_media_type_edit($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['program'] = Program::find($data['program_content']->program->id);

        $data['program_name'] = 'Program Mentor Edit';
        $data['title'] = 'Program Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.media_type.program_media_type_edit', $data );
    }

    public function program_media_type_edit_list(Request $request)
    {
        $program_id = $request->program_id;        
        $program_media_type_list = DB::table('schedule_media_type as d1')->whereNull('d1.deleted_at');
        $program_media_type_list = $program_media_type_list->select('d1.*');
        $program_media_type_list = $program_media_type_list->addSelect(DB::raw($program_id." as program_id"));
                
        return Datatables::of($program_media_type_list)
            ->addColumn('action', function ($program_media_type_list) {
                $data['checked'] = "";
                $data['program_media_type_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_media_type_list->program_id,'content_type_id'=>'1','content_id'=>$program_media_type_list->id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_media_type_list'] = $program_media_type_list;
                
                return view('admin.program.media_type.program_media_type_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_media_type_update(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['program_content_id'] = $request->program_content_id;
        $data['content_type_id'] = 1;
        $data['media_type_id'] = $request->media_type_id;         
        $data['status'] = "incomplete";
        
        $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->first();
        if(isset($program_content))
        {
            $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->update([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->media_type_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited media type.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function program_media_type_delete($program_content_id)
    {
        
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Media Type has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Media Type remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/program-media-type-list/'.$data['program_content']->program->id));
    }


    public function program_topic_list($program_id)
    {
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['content_type'] = 2;      
        return view('admin.program.topics.program_topic_list', $data);

    }
    
    public function program_topic_ajax_list(Request $request)
    {
        $program_id = $request->program_id; 
        $program_topic_list = DB::table('topic as d1')->join('program_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','2')->where('d2.program_id',$program_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $program_topic_list = $program_topic_list->select('d1.*','d2.id as program_content_id','d2.deleted_at');

        return Datatables::of($program_topic_list)
            ->addColumn('action', function ($program_topic_list) {

                $data['program_topic_list'] = $program_topic_list;
                
                return view('admin.program.topics.program_topic_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function program_topic_add($program_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::find($program_id);

        $data['module_name'] = 'Program Topics Add';
        $data['title'] = 'Program Topics Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.topics.program_topic_add', $data);
        
    }

    public function program_topic_add_list(Request $request)
    {
        $program_id = $request->program_id;        
        $program_topic_list = DB::table('topic as d1')->whereNull('d1.deleted_at');
        $program_topic_list = $program_topic_list->select('d1.*');
        $program_topic_list = $program_topic_list->addSelect(DB::raw($program_id." as program_id"));        
                
        return Datatables::of($program_topic_list)
            ->addColumn('action', function ($program_topic_list) {
                $data['checked'] = "";
                $data['program_topic_add_info'] = "";
                
                $topic_content = ProgramContent::where(['program_id'=>$program_topic_list->program_id,'content_type_id'=>'2','content_id'=>$program_topic_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_topic_list'] = $program_topic_list;
                
                return view('admin.program.topics.program_topic_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_topic_save(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['content_type_id'] = 2;
        $data['topic_id'] = $request->topic_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $program_topic = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id])->first();
            if(!isset($program_topic))
            {
                $program_topic = ProgramContent::insert([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id,'created_by'=>Auth::id()]);
                if(isset($program_topic))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added mentor.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This mentor already exist in this topic !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {
            $topic_content_type_ids = TopicContent::where(['topic_id'=>$request->topic_id])->pluck('content_type_id');
            $topic_content_ids = TopicContent::where(['topic_id'=>$request->topic_id])->pluck('id');
            ProgramContent::where('program_id',$request->program_id)->whereIn('content_type_id',$topic_content_type_ids)->whereIn('content_id',$topic_content_ids)->update(['deleted_by'=>Auth::id()]);
            ProgramContent::where('program_id',$request->program_id)->whereIn('content_type_id',$topic_content_type_ids)->whereIn('content_id',$topic_content_ids)->delete();

            $program_topic = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id])->update(['deleted_by'=>Auth::id()]);
            $program_topic = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id])->delete();
            if(isset($program_topic))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed mentor.</span';
            }

        }
        
        return response()->json($data);

    }

    public function program_topic_edit($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['program'] = Program::find($data['program_content']->program->id);
        $data['module_name'] = 'Program Topic Edit';
        $data['title'] = 'Program Topic Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.topics.program_topic_edit', $data );
    }

    public function program_topic_edit_list(Request $request)
    {
        $program_id = $request->program_id;        
        $program_topic_list = DB::table('topic as d1')->whereNull('d1.deleted_at');
        $program_topic_list = $program_topic_list->select('d1.*');
        $program_topic_list = $program_topic_list->addSelect(DB::raw($program_id." as program_id"));        
                
        return Datatables::of($program_topic_list)
            ->addColumn('action', function ($program_topic_list) {
                $data['checked'] = "";
                $data['program_topic_add_info'] = "";
                
                $topic_content = ProgramContent::where(['program_id'=>$program_topic_list->program_id,'content_type_id'=>'2','content_id'=>$program_topic_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_topic_list'] = $program_topic_list;
                
                return view('admin.program.topics.program_topic_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_topic_update(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['program_content_id'] = $request->program_content_id;
        $data['content_type_id'] = 2;
        $data['topic_id'] = $request->topic_id;         
        $data['status'] = "incomplete";
        
        $topic_content = ProgramContent::where(['id'=>$data['program_content_id']])->first();
        if(isset($topic_content))
        {
            $topic_content = ProgramContent::where(['id'=>$data['program_content_id']])->update([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited mentor.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function program_topic_delete($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['topic'] = Topic::where(['id'=>$data['program_content']->content_id])->first();
        $topic_content_type_ids = TopicContent::where(['topic_id'=>$data['program_content']->content_id])->pluck('content_type_id');
        $topic_content_ids = TopicContent::where(['topic_id'=>$data['program_content']->content_id])->pluck('id');
        ProgramContent::where('program_id',$data['program_content']->program_id)->whereIn('content_type_id',$topic_content_type_ids)->whereIn('content_id',$topic_content_ids)->update(['deleted_by'=>Auth::id()]);
        ProgramContent::where('program_id',$data['program_content']->program_id)->whereIn('content_type_id',$topic_content_type_ids)->whereIn('content_id',$topic_content_ids)->delete();

        $data['deleted'] = ProgramContent::where(['program_id'=>$data['program_content']->content_id,'content_type_id'=>'2'])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ProgramContent::where(['program_id'=>$data['program_content']->content_id,'content_type_id'=>'2'])->delete();
        
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Program Topic has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Program Topic remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/program-topic-list/'.$data['program_content']->program->id));
    }

    
    public function program_mentor_list($program_id)
    {
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');

        $data['content_type'] = 3;      
        return view('admin.program.mentors.program_mentor_list', $data);

    }
    
    public function program_mentor_ajax_list(Request $request)
    {
        $program_id = $request->program_id; 
        $topic_id = $request->topic_id;
        $program_mentor_list = DB::table('teacher as d1')->join('topic_content as d2','d2.content_id','d1.id')->join('program_content as d3','d3.content_id','d2.id')->where('d2.content_type_id','1')->where('d3.content_type_id','3')->where('d3.program_id',$program_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_mentor_list = $program_mentor_list->where('d2.topic_id',$topic_id);
        }

        $program_mentor_list = $program_mentor_list->select('d1.*','d2.topic_id as program_topic_id','d3.id as program_content_id','d3.deleted_at');

        return Datatables::of($program_mentor_list)
            ->addColumn('topic_name', function ($program_mentor_list) {

                $topic = Topic::where(['id'=>$program_mentor_list->program_topic_id])->first();
                
                return $topic->name??'';
            })
            ->addColumn('action', function ($program_mentor_list) {

                $data['program_mentor_list'] = $program_mentor_list;
                
                return view('admin.program.mentors.program_mentor_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function program_mentor_add($topic_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::find($topic_id);

        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');

        $data['module_name'] = 'Program Mentors Add';
        $data['title'] = 'Program Mentors Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.mentors.program_mentor_add', $data);
        
    }

    public function program_mentor_add_list(Request $request)
    {
        $program_id = $request->program_id;
        $topic_id = $request->topic_id;
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program_mentor_list = DB::table('teacher as d1')->join('topic_content as d2','d2.content_id','=','d1.id')->where('d2.content_type_id','1')->join('topic as d3','d3.id','d2.topic_id')->whereIn('d2.topic_id',$topic_ids)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_mentor_list = $program_mentor_list->where('d2.topic_id',$topic_id);
        }

        $program_mentor_list = $program_mentor_list->select('d1.*','d3.name as topic_name','d2.id as topic_content_id');
        $program_mentor_list = $program_mentor_list->addSelect(DB::raw($program_id." as program_id"));
                 
                
        return Datatables::of($program_mentor_list)
            ->addColumn('action', function ($program_mentor_list) {
                $data['checked'] = "";
                $data['program_mentor_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_mentor_list->program_id,'content_type_id'=>'3','content_id'=>$program_mentor_list->topic_content_id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_mentor_list'] = $program_mentor_list;
                
                return view('admin.program.mentors.program_mentor_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_mentor_save(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['content_type_id'] = 3;
        $data['topic_content_id'] = $request->topic_content_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $program_mentor = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->first();
            if(!isset($program_mentor))
            {
                $program_mentor = ProgramContent::insert([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id,'created_by'=>Auth::id()]);
                if(isset($program_mentor))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added mentor.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This mentor already exist in this topic !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {
            $program_mentor = ProgramContent::where(['program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->update(['deleted_by'=>Auth::id()]);
            $program_mentor = ProgramContent::where(['program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->delete();
            if(isset($program_mentor))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed mentor.</span';
            }

        }
        
        return response()->json($data);

    }

    public function program_mentor_edit($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['topic_content'] = TopicContent::where(['id'=>$data['program_content']->content_id])->first();
        $data['program'] = Program::find($data['program_content']->program->id);
        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        $data['module_name'] = 'Program Mentor Edit';
        $data['title'] = 'Program Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.mentors.program_mentor_edit', $data );
    }

    public function program_mentor_edit_list(Request $request)
    {
        $program_id = $request->program_id;
        $program_content_id = $request->program_content_id;
        $topic_id = $request->topic_id;
        
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program_mentor_list = DB::table('teacher as d1')->join('topic_content as d2','d2.content_id','=','d1.id')->where('d2.content_type_id','1')->join('topic as d3','d3.id','d2.topic_id')->whereIn('d2.topic_id',$topic_ids)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_mentor_list = $program_mentor_list->where('d2.topic_id',$topic_id);
        }

        $program_mentor_list = $program_mentor_list->select('d1.*','d3.name as topic_name','d2.id as topic_content_id');
        $program_mentor_list = $program_mentor_list->addSelect(DB::raw($program_id." as program_id"));        
                
        return Datatables::of($program_mentor_list)
            ->addColumn('action', function ($program_mentor_list) {
                $data['checked'] = "";
                $data['program_mentor_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_mentor_list->program_id,'content_type_id'=>'3','content_id'=>$program_mentor_list->topic_content_id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_mentor_list'] = $program_mentor_list;
                
                return view('admin.program.mentors.program_mentor_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_mentor_update(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['program_content_id'] = $request->program_content_id;
        $data['content_type_id'] = 3;
        $data['topic_content_id'] = $request->topic_content_id;         
        $data['status'] = "incomplete";
        
        $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->first();
        if(isset($program_content))
        {
            $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->update([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited mentor.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function program_mentor_delete($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Mentor has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Mentor remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/program-mentor-list/'.$data['program_content']->program->id));
    }



    public function program_lecture_video_list($program_id)
    {
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        
        $data['content_type'] = 4;      
        return view('admin.program.lecture_videos.program_lecture_video_list', $data);

    }
    
    public function program_lecture_video_ajax_list(Request $request)
    {
        $program_id = $request->program_id; 
        $topic_id = $request->topic_id;
        $program_lecture_video_list = DB::table('lecture_video as d1')->join('topic_content as d2','d2.content_id','d1.id')->join('program_content as d3','d3.content_id','d2.id')->where('d2.content_type_id','2')->where('d3.content_type_id','4')->where('d3.program_id',$program_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        $program_lecture_video_list = $program_lecture_video_list->select('d1.*','d2.topic_id as program_topic_id','d3.id as program_content_id','d3.deleted_at');

        $program = Program::where(['id'=>$request->program_id])->first();
        $program_lecture_video_list = LectureVideo::with(['institute','course','session'])->select('lecture_video.*','topic_content.topic_id as program_topic_id','program_content.id as program_content_id','topic_content.deleted_at','program_content.deleted_at')->join('topic_content','topic_content.content_id','lecture_video.id')->join('program_content','program_content.content_id','topic_content.id')->where('topic_content.content_type_id','2')->where('program_content.content_type_id','4')->where('program_content.program_id',$program_id)->whereNull('lecture_video.deleted_at')->whereNull('topic_content.deleted_at')->whereNull('program_content.deleted_at')
                                        ->where(['lecture_video.institute_id'=>$program->institute_id,'lecture_video.course_id'=>$program->course_id,'lecture_video.year'=>$program->year,'lecture_video.session_id'=>$program->session_id]);
        
        if($topic_id){
            $program_lecture_video_list = $program_lecture_video_list->where('topic_content.topic_id',$topic_id);
        }

        return Datatables::of($program_lecture_video_list)
            ->addColumn('topic_name', function ($program_lecture_video_list) {

                $topic = Topic::where(['id'=>$program_lecture_video_list->program_topic_id])->first();
                
                return $topic->name??'';
            })
            ->addColumn('action', function ($program_lecture_video_list) {

                $data['program_lecture_video_list'] = $program_lecture_video_list;
                
                return view('admin.program.lecture_videos.program_lecture_video_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function program_lecture_video_add($program_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::find($program_id);

        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        
        $data['module_name'] = 'Program Lecture Video Add';
        $data['title'] = 'Program Lecture Video Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.lecture_videos.program_lecture_video_add', $data);
        
    }

    public function program_lecture_video_add_list(Request $request)
    {
        $program_id = $request->program_id;
        $topic_id = $request->topic_id;
        
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program_lecture_video_list = DB::table('lecture_video as d1')->join('topic_content as d2','d2.content_id','=','d1.id')->where('d2.content_type_id','2')->join('topic as d3','d3.id','d2.topic_id')->whereIn('d2.topic_id',$topic_ids)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        $program_lecture_video_list = $program_lecture_video_list->select('d1.*','d3.name as topic_name','d2.id as topic_content_id');
        $program_lecture_video_list = $program_lecture_video_list->addSelect(DB::raw($program_id." as program_id"));

        $program = Program::where(['id'=>$request->program_id])->first();
        $program_lecture_video_list = LectureVideo::with(['institute','course','session'])->select('lecture_video.*','topic.name as topic_name','topic_content.id as topic_content_id','topic_content.deleted_at','topic.deleted_at')->join('topic_content','topic_content.content_id','=','lecture_video.id')->where('topic_content.content_type_id','2')->join('topic','topic.id','topic_content.topic_id')->whereIn('topic_content.topic_id',$topic_ids)->whereNull('lecture_video.deleted_at')->whereNull('topic_content.deleted_at')->whereNull('topic.deleted_at')
                                        ->where(['lecture_video.institute_id'=>$program->institute_id,'lecture_video.course_id'=>$program->course_id,'lecture_video.year'=>$program->year,'lecture_video.session_id'=>$program->session_id]);

        if($topic_id){
            $program_lecture_video_list = $program_lecture_video_list->where('topic_content.topic_id',$topic_id);
        }
                 
                
        return Datatables::of($program_lecture_video_list)
            ->addColumn('action', function ($program_lecture_video_list) use($request){
                $data['checked'] = "";
                $data['program_lecture_video_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$request->program_id,'content_type_id'=>'4','content_id'=>$program_lecture_video_list->topic_content_id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_lecture_video_list'] = $program_lecture_video_list;
                
                return view('admin.program.lecture_videos.program_lecture_video_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_lecture_video_save(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['content_type_id'] = 4;
        $data['topic_content_id'] = $request->topic_content_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $program_lecture_video = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->first();
            if(!isset($program_lecture_video))
            {
                $program_lecture_video = ProgramContent::insert([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id,'created_by'=>Auth::id()]);
                if(isset($program_lecture_video))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added lecture video.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This lecture video already exist in this topic !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {
            $program_lecture_video = ProgramContent::where(['program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->update(['deleted_by'=>Auth::id()]);
            $program_lecture_video = ProgramContent::where(['program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->delete();
            if(isset($program_lecture_video))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed lecture_video.</span';
            }

        }
        
        return response()->json($data);

    }

    public function program_lecture_video_edit($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['topic_content'] = TopicContent::where(['id'=>$data['program_content']->content_id])->first();
        $data['program'] = Program::find($data['program_content']->program->id);
        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        $data['module_name'] = 'Program Lecture Video Edit';
        $data['title'] = 'Program Lecture Video Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.lecture_videos.program_lecture_video_edit', $data );
    }

    public function program_lecture_video_edit_list(Request $request)
    {
        $program_id = $request->program_id;
        $program_content_id = $request->program_content_id;
        $topic_id = $request->topic_id;
        
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program_lecture_video_list = DB::table('lecture_video as d1')->join('topic_content as d2','d2.content_id','=','d1.id')->where('d2.content_type_id','2')->join('topic as d3','d3.id','d2.topic_id')->whereIn('d2.topic_id',$topic_ids)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        $program_lecture_video_list = $program_lecture_video_list->select('d1.*','d3.name as topic_name','d2.id as topic_content_id');
        $program_lecture_video_list = $program_lecture_video_list->addSelect(DB::raw($program_id." as program_id"));

        $program = Program::where(['id'=>$request->program_id])->first();
        $program_lecture_video_list = LectureVideo::with(['institute','course','session'])->select('lecture_video.*','topic.name as topic_name','topic_content.id as topic_content_id','topic_content.deleted_at','topic.deleted_at')->join('topic_content','topic_content.content_id','=','lecture_video.id')->where('topic_content.content_type_id','2')->join('topic','topic.id','topic_content.topic_id')->whereIn('topic_content.topic_id',$topic_ids)->whereNull('lecture_video.deleted_at')->whereNull('topic_content.deleted_at')->whereNull('topic.deleted_at')
                                        ->where(['lecture_video.institute_id'=>$program->institute_id,'lecture_video.course_id'=>$program->course_id,'lecture_video.year'=>$program->year,'lecture_video.session_id'=>$program->session_id]);

        if($topic_id){
            $program_lecture_video_list = $program_lecture_video_list->where('topic_content.topic_id',$topic_id);
        }                
                
        return Datatables::of($program_lecture_video_list)
            ->addColumn('action', function ($program_lecture_video_list) use($request){
                $data['checked'] = "";
                $data['program_lecture_video_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$request->program_id,'content_type_id'=>'4','content_id'=>$program_lecture_video_list->topic_content_id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_lecture_video_list'] = $program_lecture_video_list;
                
                return view('admin.program.lecture_videos.program_lecture_video_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_lecture_video_update(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['program_content_id'] = $request->program_content_id;
        $data['content_type_id'] = 4;
        $data['topic_content_id'] = $request->topic_content_id;         
        $data['status'] = "incomplete";
        
        $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->first();
        if(isset($program_content))
        {
            $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->update([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited lecture_video.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function program_lecture_video_delete($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Mentor has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Mentor remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/program-lecture-video-list/'.$data['program_content']->program->id));
    }


    public function program_exam_list($program_id)
    {
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        
        $data['content_type'] = 4;      
        return view('admin.program.exams.program_exam_list', $data);

    }
    
    public function program_exam_ajax_list(Request $request)
    {
        $program_id = $request->program_id; 
        $topic_id = $request->topic_id;
        $program_exam_list = DB::table('exam as d1')->join('topic_content as d2','d2.content_id','d1.id')->join('program_content as d3','d3.content_id','d2.id')->where('d2.content_type_id','3')->where('d3.content_type_id','5')->where('d3.program_id',$program_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_exam_list = $program_exam_list->where('d2.topic_id',$topic_id);
        }

        $program_exam_list = $program_exam_list->select('d1.*','d2.topic_id as program_topic_id','d3.id as program_content_id','d3.deleted_at');

        $program = Program::where(['id'=>$request->program_id])->first();
        $program_exam_list = Exam::with(['institute','course','session'])->select('exam.*','topic_content.topic_id as program_topic_id','program_content.id as program_content_id','topic_content.deleted_at','program_content.deleted_at')->join('topic_content','topic_content.content_id','exam.id')->join('program_content','program_content.content_id','topic_content.id')->where('topic_content.content_type_id','3')->where('program_content.content_type_id','5')->where('program_content.program_id',$program_id)->whereNull('exam.deleted_at')->whereNull('topic_content.deleted_at')->whereNull('program_content.deleted_at')
                                        ->where(['exam.institute_id'=>$program->institute_id,'exam.course_id'=>$program->course_id,'exam.year'=>$program->year,'exam.session_id'=>$program->session_id]);

        if($topic_id){
            $program_exam_list = $program_exam_list->where('topic_content.topic_id',$topic_id);
        }

        return Datatables::of($program_exam_list)
            ->addColumn('topic_name', function ($program_exam_list) {

                $topic = Topic::where(['id'=>$program_exam_list->program_topic_id])->first();
                
                return $topic->name??'';
            })
            ->addColumn('action', function ($program_exam_list) {

                $data['program_exam_list'] = $program_exam_list;
                
                return view('admin.program.exams.program_exam_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function program_exam_add($topic_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::find($topic_id);

        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        
        $data['module_name'] = 'Program Exams Add';
        $data['title'] = 'Program Exams Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.exams.program_exam_add', $data);
        
    }

    public function program_exam_add_list(Request $request)
    {
        $program_id = $request->program_id;
        $topic_id = $request->topic_id;
        
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program_exam_list = DB::table('exam as d1')->join('topic_content as d2','d2.content_id','=','d1.id')->where('d2.content_type_id','3')->join('topic as d3','d3.id','d2.topic_id')->whereIn('d2.topic_id',$topic_ids)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_exam_list = $program_exam_list->where('d2.topic_id',$topic_id);
        }

        $program_exam_list = $program_exam_list->select('d1.*','d3.name as topic_name','d2.id as topic_content_id');
        $program_exam_list = $program_exam_list->addSelect(DB::raw($program_id." as program_id"));

        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program = Program::where(['id'=>$request->program_id])->first();
        $program_exam_list = Exam::with(['institute','course','session'])->select('exam.*','topic.name as topic_name','topic_content.id as topic_content_id','topic_content.deleted_at','topic.deleted_at')->join('topic_content','topic_content.content_id','=','exam.id')->where('topic_content.content_type_id','3')->join('topic','topic.id','topic_content.topic_id')->whereIn('topic_content.topic_id',$topic_ids)->whereNull('exam.deleted_at')->whereNull('topic_content.deleted_at')->whereNull('topic.deleted_at')
                                        ->where(['exam.institute_id'=>$program->institute_id,'exam.course_id'=>$program->course_id,'exam.year'=>$program->year,'exam.session_id'=>$program->session_id]);

        if($topic_id){
            $program_exam_list = $program_exam_list->where('topic_content.topic_id',$topic_id);
        }
                 
                
        return Datatables::of($program_exam_list)

            ->addColumn('action', function ($program_exam_list) use($request){
                $data['checked'] = "";
                $data['program_exam_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$request->program_id,'content_type_id'=>'5','content_id'=>$program_exam_list->topic_content_id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_exam_list'] = $program_exam_list;
                
                return view('admin.program.exams.program_exam_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_exam_save(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['content_type_id'] = 5;
        $data['topic_content_id'] = $request->topic_content_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $program_exam = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->first();
            if(!isset($program_exam))
            {
                $program_exam = ProgramContent::insert([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id,'created_by'=>Auth::id()]);
                if(isset($program_exam))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added exam.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This exam already exist in this topic !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {
            $program_exam = ProgramContent::where(['program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->update(['deleted_by'=>Auth::id()]);
            $program_exam = ProgramContent::where(['program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->delete();
            if(isset($program_exam))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed exam.</span';
            }

        }
        
        return response()->json($data);

    }

    public function program_exam_edit($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['topic_content'] = TopicContent::where(['id'=>$data['program_content']->content_id])->first();
        $data['program'] = Program::find($data['program_content']->program->id);
        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        $data['module_name'] = 'Program Exam Edit';
        $data['title'] = 'Program Exam Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.exams.program_exam_edit', $data );
    }

    public function program_exam_edit_list(Request $request)
    {
        $program_id = $request->program_id;
        $program_content_id = $request->program_content_id;
        $topic_id = $request->topic_id;
        
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program_exam_list = DB::table('exam as d1')->join('topic_content as d2','d2.content_id','=','d1.id')->where('d2.content_type_id','3')->join('topic as d3','d3.id','d2.topic_id')->whereIn('d2.topic_id',$topic_ids)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_exam_list = $program_exam_list->where('d2.topic_id',$topic_id);
        }

        $program_exam_list = $program_exam_list->select('d1.*','d3.name as topic_name','d2.id as topic_content_id');
        $program_exam_list = $program_exam_list->addSelect(DB::raw($program_id." as program_id")); 
        
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program = Program::where(['id'=>$request->program_id])->first();
        $program_exam_list = Exam::with(['institute','course','session'])->select('exam.*','topic.name as topic_name','topic_content.id as topic_content_id','topic_content.deleted_at','topic.deleted_at')->join('topic_content','topic_content.content_id','=','exam.id')->where('topic_content.content_type_id','3')->join('topic','topic.id','topic_content.topic_id')->whereIn('topic_content.topic_id',$topic_ids)->whereNull('exam.deleted_at')->whereNull('topic_content.deleted_at')->whereNull('topic.deleted_at')
                                        ->where(['exam.institute_id'=>$program->institute_id,'exam.course_id'=>$program->course_id,'exam.year'=>$program->year,'exam.session_id'=>$program->session_id]);

        if($topic_id){
            $program_exam_list = $program_exam_list->where('topic_content.topic_id',$topic_id);
        }
                
        return Datatables::of($program_exam_list)
            ->addColumn('action', function ($program_exam_list) use($request){
                $data['checked'] = "";
                $data['program_exam_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$request->program_id,'content_type_id'=>'5','content_id'=>$program_exam_list->topic_content_id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_exam_list'] = $program_exam_list;
                
                return view('admin.program.exams.program_exam_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_exam_update(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['program_content_id'] = $request->program_content_id;
        $data['content_type_id'] = 5;
        $data['topic_content_id'] = $request->topic_content_id;         
        $data['status'] = "incomplete";
        
        $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->first();
        if(isset($program_content))
        {
            $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->update([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited exam.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function program_exam_delete($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Mentor has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Mentor remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/program-exam-list/'.$data['program_content']->program->id));
    }

    public function program_lecture_sheet_list($program_id)
    {
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        
        $data['content_type'] = 6;      
        return view('admin.program.lecture_sheets.program_lecture_sheet_list', $data);

    }
    
    public function program_lecture_sheet_ajax_list(Request $request)
    {
        $program_id = $request->program_id; 
        $topic_id = $request->topic_id;
        $program_lecture_sheet_list = DB::table('lecture_sheet as d1')->join('topic_content as d2','d2.content_id','d1.id')->join('program_content as d3','d3.content_id','d2.id')->where('d2.content_type_id','4')->where('d3.content_type_id','6')->where('d3.program_id',$program_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_lecture_sheet_list = $program_lecture_sheet_list->where('d2.topic_id',$topic_id);
        }

        $program_lecture_sheet_list = $program_lecture_sheet_list->select('d1.*','d2.topic_id as program_topic_id','d3.id as program_content_id','d3.deleted_at');

        return Datatables::of($program_lecture_sheet_list)
            ->addColumn('topic_name', function ($program_lecture_sheet_list) {

                $topic = Topic::where(['id'=>$program_lecture_sheet_list->program_topic_id])->first();
                
                return $topic->name??'';
            })
            ->addColumn('action', function ($program_lecture_sheet_list) {

                $data['program_lecture_sheet_list'] = $program_lecture_sheet_list;
                
                return view('admin.program.lecture_sheets.program_lecture_sheet_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function program_lecture_sheet_add($program_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::find($program_id);

        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        
        $data['module_name'] = 'Program Sheets Add';
        $data['title'] = 'Program Sheets Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.lecture_sheets.program_lecture_sheet_add', $data);
        
    }

    public function program_lecture_sheet_add_list(Request $request)
    {
        $program_id = $request->program_id;
        $topic_id = $request->topic_id;
        
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program_lecture_sheet_list = DB::table('lecture_sheet as d1')->join('topic_content as d2','d2.content_id','=','d1.id')->where('d2.content_type_id','4')->join('topic as d3','d3.id','d2.topic_id')->whereIn('d2.topic_id',$topic_ids)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_lecture_sheet_list = $program_lecture_sheet_list->where('d2.topic_id',$topic_id);
        }

        $program_lecture_sheet_list = $program_lecture_sheet_list->select('d1.*','d3.name as topic_name','d2.id as topic_content_id');
        $program_lecture_sheet_list = $program_lecture_sheet_list->addSelect(DB::raw($program_id." as program_id"));
                 
                
        return Datatables::of($program_lecture_sheet_list)
            ->addColumn('action', function ($program_lecture_sheet_list) {
                $data['checked'] = "";
                $data['program_lecture_sheet_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_lecture_sheet_list->program_id,'content_type_id'=>'6','content_id'=>$program_lecture_sheet_list->topic_content_id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_lecture_sheet_list'] = $program_lecture_sheet_list;
                
                return view('admin.program.lecture_sheets.program_lecture_sheet_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_lecture_sheet_save(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['content_type_id'] = 6;
        $data['topic_content_id'] = $request->topic_content_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $program_lecture_sheet = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->first();
            if(!isset($program_lecture_sheet))
            {
                $program_lecture_sheet = ProgramContent::insert([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id,'created_by'=>Auth::id()]);
                if(isset($program_lecture_sheet))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added lecture sheet.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This lecture sheet already exist in this topic !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {
            $program_lecture_sheet = ProgramContent::where(['program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->update(['deleted_by'=>Auth::id()]);
            $program_lecture_sheet = ProgramContent::where(['program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id])->delete();
            if(isset($program_lecture_sheet))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed lecture_sheet.</span';
            }

        }
        
        return response()->json($data);

    }

    public function program_lecture_sheet_edit($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['topic_content'] = TopicContent::where(['id'=>$data['program_content']->content_id])->first();
        $data['program'] = Program::find($data['program_content']->program->id);
        $data['topics'] = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$data['program']->id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.name','topic.id');
        $data['module_name'] = 'Program Sheet Edit';
        $data['title'] = 'Program Sheet Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.lecture_sheets.program_lecture_sheet_edit', $data );
    }

    public function program_lecture_sheet_edit_list(Request $request)
    {
        $program_id = $request->program_id;
        $program_content_id = $request->program_content_id;
        $topic_id = $request->topic_id;
        
        $topic_ids = ProgramContent::join('topic','program_content.content_id','=','topic.id')->where(['program_id'=>$program_id,'content_type_id'=>'2'])->whereNull('topic.deleted_at')->whereNull('program_content.deleted_at')->pluck('topic.id');
        $program_lecture_sheet_list = DB::table('lecture_sheet as d1')->join('topic_content as d2','d2.content_id','=','d1.id')->where('d2.content_type_id','4')->join('topic as d3','d3.id','d2.topic_id')->whereIn('d2.topic_id',$topic_ids)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->whereNull('d3.deleted_at');
        
        if($topic_id){
            $program_lecture_sheet_list = $program_lecture_sheet_list->where('d2.topic_id',$topic_id);
        }

        $program_lecture_sheet_list = $program_lecture_sheet_list->select('d1.*','d3.name as topic_name','d2.id as topic_content_id');
        $program_lecture_sheet_list = $program_lecture_sheet_list->addSelect(DB::raw($program_id." as program_id"));        
                
        return Datatables::of($program_lecture_sheet_list)
            ->addColumn('action', function ($program_lecture_sheet_list) {
                $data['checked'] = "";
                $data['program_lecture_sheet_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_lecture_sheet_list->program_id,'content_type_id'=>'6','content_id'=>$program_lecture_sheet_list->topic_content_id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_lecture_sheet_list'] = $program_lecture_sheet_list;
                
                return view('admin.program.lecture_sheets.program_lecture_sheet_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_lecture_sheet_update(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['program_content_id'] = $request->program_content_id;
        $data['content_type_id'] = 6;
        $data['topic_content_id'] = $request->topic_content_id;         
        $data['status'] = "incomplete";
        
        $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->first();
        if(isset($program_content))
        {
            $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->update([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->topic_content_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited lecture_sheet.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function program_lecture_sheet_delete($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Mentor has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Mentor remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/program-lecture-sheet-list/'.$data['program_content']->program->id));
    }

    public function program_batch_list($program_id)
    {
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);
        $data['content_type'] = 7;      
        return view('admin.program.batch.program_batch_list', $data);

    }
    
    public function program_batch_ajax_list(Request $request)
    {
        $program_id = $request->program_id; 
        $program_batch_list = DB::table('batches as d1')->join('program_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','7')->where('d2.program_id',$program_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $program_batch_list = $program_batch_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name','d2.id as program_content_id','d2.deleted_at','d3.deleted_at','d4.deleted_at','d5.deleted_at');
        
        if($request->institute_id)
        {
            $program_batch_list = $program_batch_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $program_batch_list = $program_batch_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $program_batch_list = $program_batch_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $program_batch_list = $program_batch_list->where('d1.session_id',$request->session_id);
        }

        return Datatables::of($program_batch_list)
            ->addColumn('action', function ($program_batch_list) {

                $data['program_batch_list'] = $program_batch_list;
                
                return view('admin.program.batch.program_batch_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_program_batch(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->pluck('name','id');
        
        return view('admin.program.batch.ajax.program_batch_course',$data);

    }

    public function course_change_in_program_batch(Request $request)
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
        
        return view('admin.program.batch.ajax.program_batch_year',$data);

    }

    public function year_change_in_program_batch(Request $request)
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
        
        return view('admin.program.batch.ajax.program_batch_session',$data);

    }
    
    public function program_batch_add($program_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['program_name'] = 'Program batch Add';
        $data['title'] = 'Program batch Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.batch.program_batch_add', $data);
        
    }

    public function program_batch_add_list(Request $request)
    {
        $program_id = $request->program_id;
        $program_batch_list = DB::table('batches as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $program_batch_list = $program_batch_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name');
        $program_batch_list = $program_batch_list->addSelect(DB::raw($program_id." as program_id"));
        
        if($request->institute_id)
        {
            $program_batch_list = $program_batch_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $program_batch_list = $program_batch_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $program_batch_list = $program_batch_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $program_batch_list = $program_batch_list->where('d1.session_id',$request->session_id);
        }
                
        return Datatables::of($program_batch_list)
            ->addColumn('action', function ($program_batch_list) {
                $data['checked'] = "";
                $data['program_batch_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_batch_list->program_id,'content_type_id'=>'7','content_id'=>$program_batch_list->id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_batch_list'] = $program_batch_list;
                
                return view('admin.program.batch.program_batch_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_batch_save(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['content_type_id'] = 7;
        $data['batch_id'] = $request->batch_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $program_batch = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id])->first();
            if(!isset($program_batch))
            {
                $program_batch = ProgramContent::insert([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id,'created_by'=>Auth::id()]);
                if(isset($program_batch))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added batch.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This batch already exist in this program !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $program_batch = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id])->update(['deleted_by'=>Auth::id()]);
            $program_batch = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id])->delete();
            if(isset($program_batch))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed batch.</span';
            }

        }
        
        return response()->json($data);

    }

    public function program_batch_edit($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['program'] = Program::find($data['program_content']->program->id);

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['program_name'] = 'Program Batch Edit';
        $data['title'] = 'Program Batch Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.batch.program_batch_edit', $data );
    }

    public function program_batch_edit_list(Request $request)
    {
        $program_id = $request->program_id;        
        $program_batch_list = DB::table('batches as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $program_batch_list = $program_batch_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name');
        $program_batch_list = $program_batch_list->addSelect(DB::raw($program_id." as program_id"));
        
        if($request->institute_id)
        {
            $program_batch_list = $program_batch_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $program_batch_list = $program_batch_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $program_batch_list = $program_batch_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $program_batch_list = $program_batch_list->where('d1.session_id',$request->session_id);
        }
                
        return Datatables::of($program_batch_list)
            ->addColumn('action', function ($program_batch_list) {
                $data['checked'] = "";
                $data['program_batch_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_batch_list->program_id,'content_type_id'=>'7','content_id'=>$program_batch_list->id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_batch_list'] = $program_batch_list;
                
                return view('admin.program.batch.program_batch_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_batch_update(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['program_content_id'] = $request->program_content_id;
        $data['content_type_id'] = 7;
        $data['batch_id'] = $request->batch_id;         
        $data['status'] = "incomplete";
        
        $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->first();
        if(isset($program_content))
        {
            $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->update([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->batch_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited batch.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function program_batch_delete($program_content_id)
    {
        
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Batch has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Batch remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/program-batch-list/'.$data['program_content']->program->id));
    }

    public function program_module_list($program_id)
    {
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);
        $data['content_type'] = 7;      
        return view('admin.program.module.program_module_list', $data);

    }
    
    public function program_module_ajax_list(Request $request)
    {
        $program_id = $request->program_id; 
        $program_module_list = DB::table('modules as d1')->join('program_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','7')->where('d2.program_id',$program_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $program_module_list = $program_module_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name','d2.id as program_content_id','d2.deleted_at','d3.deleted_at','d4.deleted_at','d5.deleted_at');
        
        if($request->institute_id)
        {
            $program_module_list = $program_module_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $program_module_list = $program_module_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $program_module_list = $program_module_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $program_module_list = $program_module_list->where('d1.session_id',$request->session_id);
        }

        return Datatables::of($program_module_list)
            ->addColumn('action', function ($program_module_list) {

                $data['program_module_list'] = $program_module_list;
                
                return view('admin.program.module.program_module_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_program_module(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->pluck('name','id');
        
        return view('admin.program.module.ajax.program_module_course',$data);

    }

    public function course_change_in_program_module(Request $request)
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
        
        return view('admin.program.module.ajax.program_module_year',$data);

    }

    public function year_change_in_program_module(Request $request)
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
        
        return view('admin.program.module.ajax.program_module_session',$data);

    }
    
    public function program_module_add($program_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['program'] = Program::where(['id'=>$program_id])->first();
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['program_name'] = 'Program module Add';
        $data['title'] = 'Program module Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.program.module.program_module_add', $data);
        
    }

    public function program_module_add_list(Request $request)
    {
        $program_id = $request->program_id;
        $program_module_list = DB::table('modules as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $program_module_list = $program_module_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name');
        $program_module_list = $program_module_list->addSelect(DB::raw($program_id." as program_id"));
        
        if($request->institute_id)
        {
            $program_module_list = $program_module_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $program_module_list = $program_module_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $program_module_list = $program_module_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $program_module_list = $program_module_list->where('d1.session_id',$request->session_id);
        }
                
        return Datatables::of($program_module_list)
            ->addColumn('action', function ($program_module_list) {
                $data['checked'] = "";
                $data['program_module_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_module_list->program_id,'content_type_id'=>'7','content_id'=>$program_module_list->id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_module_list'] = $program_module_list;
                
                return view('admin.program.module.program_module_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_module_save(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['content_type_id'] = 7;
        $data['module_id'] = $request->module_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $program_module = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->module_id])->first();
            if(!isset($program_module))
            {
                $program_module = ProgramContent::insert([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->module_id,'created_by'=>Auth::id()]);
                if(isset($program_module))
                {
                    $data['status'] = "insert_success";
                    $data['message'] = '<br><span style="color:green;font-weight:700">Successfully added module.</span';
                }

            } 
            else
            {

                $data['status'] = "data_already_exist";
                $data['message'] = '<br><span style="color:red;font-weight:700">This module already exist in this program !!!</span';
                
            }            

        }
        else if($request->operation == "delete")
        {

            $program_module = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->module_id])->update(['deleted_by'=>Auth::id()]);
            $program_module = ProgramContent::where([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->module_id])->delete();
            if(isset($program_module))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed module.</span';
            }

        }
        
        return response()->json($data);

    }

    public function program_module_edit($program_content_id)
    {
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['program'] = Program::find($data['program_content']->program->id);

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['program_name'] = 'Program Module Edit';
        $data['title'] = 'Program Module Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.program.module.program_module_edit', $data );
    }

    public function program_module_edit_list(Request $request)
    {
        $program_id = $request->program_id;        
        $program_module_list = DB::table('modules as d1')->whereNull('d1.deleted_at')->join('institutes as d3','d3.id','d1.institute_id')->join('courses as d4','d4.id','d1.course_id')->join('sessions as d5','d5.id','d1.session_id')->whereNull('d3.deleted_at')->whereNull('d4.deleted_at')->whereNull('d5.deleted_at');
        $program_module_list = $program_module_list->select('d1.*','d3.name as institute_name','d4.name as course_name','d5.name as session_name');
        $program_module_list = $program_module_list->addSelect(DB::raw($program_id." as program_id"));
        
        if($request->institute_id)
        {
            $program_module_list = $program_module_list->where('d1.institute_id',$request->institute_id);
        }

        if($request->course_id)
        {
            $program_module_list = $program_module_list->where('d1.course_id',$request->course_id);
        }

        if($request->year)
        {
            $program_module_list = $program_module_list->where('d1.year',$request->year);
        }

        if($request->session_id)
        {
            $program_module_list = $program_module_list->where('d1.session_id',$request->session_id);
        }
                
        return Datatables::of($program_module_list)
            ->addColumn('action', function ($program_module_list) {
                $data['checked'] = "";
                $data['program_module_add_info'] = "";
                
                $program_content = ProgramContent::where(['program_id'=>$program_module_list->program_id,'content_type_id'=>'7','content_id'=>$program_module_list->id])->first();
                if(isset($program_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['program_module_list'] = $program_module_list;
                
                return view('admin.program.module.program_module_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function program_module_update(Request $request)
    {
        $data['program_id'] = $request->program_id;
        $data['program_content_id'] = $request->program_content_id;
        $data['content_type_id'] = 7;
        $data['module_id'] = $request->module_id;         
        $data['status'] = "incomplete";
        
        $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->first();
        if(isset($program_content))
        {
            $program_content = ProgramContent::where(['id'=>$data['program_content_id']])->update([ 'program_id' => $request->program_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->module_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited module.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function program_module_delete($program_content_id)
    {
        
        $data['program_content'] = ProgramContent::where(['id'=>$program_content_id])->first();
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = ProgramContent::where(['id'=>$program_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Module has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Module remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/program-module-list/'.$data['program_content']->program->id));
    }
    
}  