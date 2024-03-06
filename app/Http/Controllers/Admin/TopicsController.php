<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\TopicAssign;
use App\TopicLink;
use App\Topic;
use App\TopicDiscipline;
use App\TopicFaculty;
use App\TopicBatchTopic;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\TopicContent;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\CourseYear;
use App\LectureSheet;
use App\LectureVideo;
use App\ModuleContent;
use App\ProgramContent;
use App\ScheduleDefs;
use App\ScheduleProgramContentType;
use App\Teacher;
use App\Topics;
use Session;
use Auth;
use Illuminate\Support\Collection;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;


class TopicsController extends Controller
{
    use ScheduleDefs;
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
        $data['topics'] = Topic::get();
        $data['module_name'] = 'Topic';
        $data['title'] = 'Topic List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.topics.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function topic_list(Request $request)
    {        
        $topic_list = DB::table('topic as d1')->whereNull('d1.deleted_at');
        $topic_list = $topic_list->select('d1.*');
                
        return Datatables::of($topic_list)
            ->editColumn('status', function ($topic_list) {
                return $topic_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($topic_list) {
                $data['topic_list'] = $topic_list;               
                return view('admin.topics.topic_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function institute_change_in_topic(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->active()->pluck('name','id');
        
        $view_name = $request->view_name;
        
        return  json_encode(array('course'=>view('admin.topics.ajax.'.$view_name,['courses'=>$data['courses']])->render()), JSON_FORCE_OBJECT);

    }

    public function course_change_in_topic(Request $request)
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
        
        return  json_encode(array('year'=>view('admin.topics.ajax.'.$view_name,['years'=>$data['years']])->render()), JSON_FORCE_OBJECT);


    }

    public function year_change_in_topic(Request $request)
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
        
            return  json_encode(array('session'=>view('admin.topics.ajax.'.$view_name,['sessions'=>$data['sessions']])->render()), JSON_FORCE_OBJECT);

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
        
        //echo "<pre>";print_r($data['types']);exit;
        $data['module_name'] = 'Topic';
        $data['title'] = 'Topic Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.topics.create',$data);
        //echo "Topic create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
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
            return redirect()->action('Admin\TopicsController@create')->withInput();
        }        

        if (Topic::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Topic name already exists');
            return redirect()->action('Admin\TopicsController@create')->withInput();
        }
        else{

            $topic = new Topic();
            $topic->name = $request->name;
            $topic->status = $request->status;
            $topic->created_by=Auth::id();
            $topic->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\TopicsController@index');
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
        $topic=Topic::find($id);
        return view('admin.topics.show',['topic'=>$topic]);
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
        $data['topic'] = Topic::find($id);

        $data['module_name'] = 'Topic';
        $data['title'] = 'Topic Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.topics.edit', $data);
        
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

        $topic = Topic::find($id);

        if($topic->name != $request->name) {

            if (Topic::where(['name'=>$request->name])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Topic name already exists');
                return redirect()->action('Admin\TopicsController@edit',[$id])->withInput();
            }

        }

        
        $topic->name = $request->name;
        $topic->status = $request->status;
        $topic->updated_by=Auth::id();
        $topic->push();

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
        Topic::destroy($id); // 1 way        
                
        ProgramContent::where(['content_type_id'=>'1','content_id'=>$id])->update(['deleted_by'=>Auth::id()]);
        ProgramContent::where(['content_type_id'=>'1','content_id'=>$id])->delete();

        $topic_contents = TopicContent::where(['topic_id'=>$id])->get();
        if(isset($topic_contents) && count($topic_contents))
        {
            foreach($topic_contents as $topic_content)
            {
                $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
            }

        }
        
        ModuleContent::where(['content_type_id'=>'4','content_id'=>$id])->update(['deleted_by'=>Auth::id()]);
        ModuleContent::where(['content_type_id'=>'4','content_id'=>$id])->delete();

        TopicContent::where(['topic_id'=>$id])->update(['deleted_by'=>Auth::id()]); // 1 way
        TopicContent::where(['topic_id'=>$id])->delete();        
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\TopicsController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function topics_contents($id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['topic'] = Topic::find($id);

        $data['module_name'] = 'Topic Contents Add';
        $data['title'] = 'Topic Contents Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.topics.contents', $data);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function topic_content_add($topic_id,$content_type_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['topic'] = Topic::find($topic_id);

        if($content_type_id == 1)
        {
            $data['module_name'] = 'Topic Mentors Add';
            $data['title'] = 'Topic Mentors Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

            return redirect(url('admin/topic-mentor-list/'.$topic_id));
        }

        if($content_type_id == 2)
        {
            $data['module_name'] = 'Topic Lecture Videos Add';
            $data['title'] = 'Topic Lecture Videos Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/topic-lecture-video-list/'.$topic_id));
        }

        if($content_type_id == 3)
        {
            $data['module_name'] = 'Topic Exams Add';
            $data['title'] = 'Topic Exams Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/topic-exam-list/'.$topic_id));
        }

        if($content_type_id == 4)
        {
            $data['module_name'] = 'Topic Lecture Sheets Add';
            $data['title'] = 'Topic Lecture Sheets Add';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            
            return redirect(url('admin/topics-contents/'.$topic_id));
            //return redirect(url('admin/topic-lecture-sheet-list/'.$topic_id));
        }

        
        
    }

    
    public function topic_mentor_list($topic_id)
    {
        $data['topic'] = Topic::where(['id'=>$topic_id])->first();
        $data['content_type'] = 1;      
        return view('admin.topics.mentors.topic_mentor_list', $data);

    }
    
    public function topic_mentor_ajax_list(Request $request)
    {
        $topic_id = $request->topic_id; 
        $topic_mentor_list = DB::table('teacher as d1')->join('topic_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','1')->where('d2.topic_id',$topic_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $topic_mentor_list = $topic_mentor_list->select('d1.*','d2.id as topic_content_id','d2.deleted_at');

        return Datatables::of($topic_mentor_list)
            ->addColumn('action', function ($topic_mentor_list) {

                $data['topic_mentor_list'] = $topic_mentor_list;
                
                return view('admin.topics.mentors.topic_mentor_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function topic_mentor_add($topic_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['topic'] = Topic::find($topic_id);

        $data['module_name'] = 'Topic Mentors Add';
        $data['title'] = 'Topic Mentors Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.topics.mentors.topic_mentor_add', $data);
        
    }

    public function topic_mentor_add_list(Request $request)
    {
        $topic_id = $request->topic_id;        
        $topic_mentor_list = DB::table('teacher as d1')->whereNull('d1.deleted_at');
        $topic_mentor_list = $topic_mentor_list->select('d1.*');
        $topic_mentor_list = $topic_mentor_list->addSelect(DB::raw($topic_id." as topic_id"));        
                
        return Datatables::of($topic_mentor_list)
            ->addColumn('action', function ($topic_mentor_list) {
                $data['checked'] = "";
                $data['topic_mentor_add_info'] = "";
                
                $topic_content = TopicContent::where(['topic_id'=>$topic_mentor_list->topic_id,'content_type_id'=>'1','content_id'=>$topic_mentor_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['topic_mentor_list'] = $topic_mentor_list;
                
                return view('admin.topics.mentors.topic_mentor_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function topic_mentor_save(Request $request)
    {
        $data['topic_id'] = $request->topic_id;
        $data['content_type_id'] = 1;
        $data['mentor_id'] = $request->mentor_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $topic_mentor = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->mentor_id])->first();
            if(!isset($topic_mentor))
            {
                $topic_mentor = TopicContent::insert([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->mentor_id,'created_by'=>Auth::id()]);
                if(isset($topic_mentor))
                {
                    $topic_mentors = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'] ])->get();
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
            $topic_contents = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->mentor_id])->get();
            if(isset($topic_contents) && count($topic_contents))
            {
                foreach($topic_contents as $topic_content)
                {
                    $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                    ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                    ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
                }

            }

            $topic_mentor = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->mentor_id])->update(['deleted_by'=>Auth::id()]);
            $topic_mentor = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->mentor_id])->delete();
            if(isset($topic_mentor))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed mentor.</span';
            }

        }
        
        return response()->json($data);

    }

    public function topic_mentor_edit($topic_content_id)
    {
        $data['topic_content'] = TopicContent::where(['id'=>$topic_content_id])->first();
        $data['topic'] = Topic::find($data['topic_content']->topic->id);
        $data['module_name'] = 'Topic Mentor Edit';
        $data['title'] = 'Topic Mentor Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.topics.mentors.topic_mentor_edit', $data );
    }

    public function topic_mentor_edit_list(Request $request)
    {
        $topic_id = $request->topic_id;        
        $topic_mentor_list = DB::table('teacher as d1')->whereNull('d1.deleted_at');
        $topic_mentor_list = $topic_mentor_list->select('d1.*');
        $topic_mentor_list = $topic_mentor_list->addSelect(DB::raw($topic_id." as topic_id"));        
                
        return Datatables::of($topic_mentor_list)
            ->addColumn('action', function ($topic_mentor_list) {
                $data['checked'] = "";
                $data['topic_mentor_add_info'] = "";
                
                $topic_content = TopicContent::where(['topic_id'=>$topic_mentor_list->topic_id,'content_type_id'=>'1','content_id'=>$topic_mentor_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['topic_mentor_list'] = $topic_mentor_list;
                
                return view('admin.topics.mentors.topic_mentor_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function topic_mentor_update(Request $request)
    {
        $data['topic_id'] = $request->topic_id;
        $data['topic_content_id'] = $request->topic_content_id;
        $data['content_type_id'] = 1;
        $data['mentor_id'] = $request->mentor_id;         
        $data['status'] = "incomplete";
        
        $topic_content = TopicContent::where(['id'=>$data['topic_content_id']])->first();
        if(isset($topic_content))
        {
            $topic_content = TopicContent::where(['id'=>$data['topic_content_id']])->update([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->mentor_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited mentor.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function topic_mentor_delete($topic_content_id)
    {        
        $topic_contents = TopicContent::where(['id'=>$topic_content_id])->get();
        if(isset($topic_contents) && count($topic_contents))
        {
            foreach($topic_contents as $topic_content)
            {
                $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
            }

        }
        
        $data['topic_content'] = TopicContent::where(['id'=>$topic_content_id])->first();
        $data['deleted'] = TopicContent::where(['id'=>$topic_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = TopicContent::where(['id'=>$topic_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Mentor has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Mentor remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/topic-mentor-list/'.$data['topic_content']->topic->id));
    }

    public function topic_lecture_video_list($topic_id)
    {
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);
        $data['topic'] = Topic::where(['id'=>$topic_id])->first();
        $data['content_type'] = 2;      
        return view('admin.topics.lecture_videos.topic_lecture_video_list', $data);

    }
    
    public function topic_lecture_video_ajax_list(Request $request)
    {
        $topic_id = $request->topic_id; 
        $topic_lecture_video_list = DB::table('lecture_video as d1')->join('topic_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','2')->where('d2.topic_id',$topic_id)->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $topic_lecture_video_list = $topic_lecture_video_list->select('d1.*','d2.id as topic_content_id','d2.deleted_at');

        $topic = Topic::where(['id'=>$request->topic_id])->first();

        $topic_lecture_video_list = LectureVideo::with(['institute','course','session'])->join('topic_content','topic_content.content_id','lecture_video.id')->select('lecture_video.*','topic_content.id as topic_content_id')->where(['topic_content.content_type_id'=>'2','topic_content.topic_id'=>$request->topic_id])->whereNull('topic_content.deleted_at');

        if($request->institute_id)
        {
            $topic_lecture_video_list->where('institute_id', $request->institute_id);
        }

        if($request->course_id)
        {
            $topic_lecture_video_list->where('course_id', $request->course_id);
        }

        if($request->year)
        {
            $topic_lecture_video_list->where('year',$request->year);
        }

        if($request->session_id)
        {
            $topic_lecture_video_list->where('session_id', $request->session_id);
        }

        return Datatables::of($topic_lecture_video_list)
            ->addColumn('action', function ($topic_lecture_video_list) {

                $data['topic_lecture_video_list'] = $topic_lecture_video_list;
                
                return view('admin.topics.lecture_videos.topic_lecture_video_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function topic_lecture_video_add($topic_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['topic'] = Topic::find($topic_id);

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['module_name'] = 'Topic Lecture Video Add';
        $data['title'] = 'Topic Lecture Video Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.topics.lecture_videos.topic_lecture_video_add', $data);
        
    }

    public function topic_lecture_video_add_list(Request $request)
    {
        // $topic_id = $request->topic_id;        
        // $topic_lecture_video_list = DB::table('lecture_video as d1')->whereNull('d1.deleted_at');
        // $topic_lecture_video_list = $topic_lecture_video_list->select('d1.*');
        // $topic_lecture_video_list = $topic_lecture_video_list->addSelect(DB::raw($topic_id." as topic_id"));

        $topic_lecture_video_list = LectureVideo::with(['institute','course','session']);
    
        if($request->institute_id)
        {
            $topic_lecture_video_list->where('institute_id', $request->institute_id);
        }

        if($request->course_id)
        {
            $topic_lecture_video_list->where('course_id', $request->course_id);
        }

        if($request->year)
        {
            $topic_lecture_video_list->where('year',$request->year);
        }

        if($request->session_id)
        {
            $topic_lecture_video_list->where('session_id', $request->session_id);
        }
                
        return Datatables::of($topic_lecture_video_list)
            ->addColumn('action', function ($topic_lecture_video_list) use($request){
                $data['checked'] = "";
                $data['topic_lecture_video_add_info'] = "";
                
                $topic_content = TopicContent::where(['topic_id'=>$request->topic_id,'content_type_id'=>'2','content_id'=>$topic_lecture_video_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['topic_lecture_video_list'] = $topic_lecture_video_list;
                
                return view('admin.topics.lecture_videos.topic_lecture_video_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function topic_lecture_video_save(Request $request)
    {
        $data['topic_id'] = $request->topic_id;
        $data['content_type_id'] = 2;
        $data['lecture_video_id'] = $request->lecture_video_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $topic_lecture_video = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_video_id])->first();
            if(!isset($topic_lecture_video))
            {
                $topic_lecture_video = TopicContent::insert([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_video_id,'created_by'=>Auth::id()]);
                if(isset($topic_lecture_video))
                {
                    $topic_lecture_videos = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'] ])->get();
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
            $topic_contents = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_video_id])->get();
            if(isset($topic_contents) && count($topic_contents))
            {
                foreach($topic_contents as $topic_content)
                {
                    $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                    ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                    ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
                }

            }

            $topic_lecture_video = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_video_id])->update(['deleted_by'=>Auth::id()]);
            $topic_lecture_video = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_video_id])->delete();
            if(isset($topic_lecture_video))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed lecture video.</span';
            }

        }
        
        return response()->json($data);

    }

    public function topic_lecture_video_edit($topic_content_id)
    {
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);
        $data['topic_content'] = TopicContent::where(['id'=>$topic_content_id])->first();
        $data['topic'] = Topic::find($data['topic_content']->topic->id);
        $data['module_name'] = 'Topic Lecture Video Edit';
        $data['title'] = 'Topic Lecture Video Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.topics.lecture_videos.topic_lecture_video_edit', $data );
    }

    public function topic_lecture_video_edit_list(Request $request)
    {
        // $topic_id = $request->topic_id;        
        // $topic_lecture_video_list = DB::table('lecture_video as d1')->whereNull('d1.deleted_at');
        // $topic_lecture_video_list = $topic_lecture_video_list->select('d1.*');
        // $topic_lecture_video_list = $topic_lecture_video_list->addSelect(DB::raw($topic_id." as topic_id"));
        
        $topic_lecture_video_list = LectureVideo::with(['institute','course','session']);
    
        if($request->institute_id)
        {
            $topic_lecture_video_list->where('institute_id', $request->institute_id);
        }

        if($request->course_id)
        {
            $topic_lecture_video_list->where('course_id', $request->course_id);
        }

        if($request->year)
        {
            $topic_lecture_video_list->where('year',$request->year);
        }

        if($request->session_id)
        {
            $topic_lecture_video_list->where('session_id', $request->session_id);
        }
                
        return Datatables::of($topic_lecture_video_list)
            ->addColumn('action', function ($topic_lecture_video_list) use($request){
                $data['checked'] = "";
                $data['topic_lecture_video_add_info'] = "";
                
                $topic_content = TopicContent::where(['topic_id'=>$request->topic_id,'content_type_id'=>'2','content_id'=>$topic_lecture_video_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['topic_lecture_video_list'] = $topic_lecture_video_list;
                
                return view('admin.topics.lecture_videos.topic_lecture_video_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function topic_lecture_video_update(Request $request)
    {
        $data['topic_id'] = $request->topic_id;
        $data['topic_content_id'] = $request->topic_content_id;
        $data['content_type_id'] = 2;
        $data['lecture_video_id'] = $request->lecture_video_id;         
        $data['status'] = "incomplete";
        
        $topic_content = TopicContent::where(['id'=>$data['topic_content_id']])->first();
        if(isset($topic_content))
        {
            $topic_content = TopicContent::where(['id'=>$data['topic_content_id']])->update([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_video_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited lecture_video.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function topic_lecture_video_delete($topic_content_id)
    {

        $topic_contents = TopicContent::where(['id'=>$topic_content_id])->get();
        if(isset($topic_contents) && count($topic_contents))
        {
            foreach($topic_contents as $topic_content)
            {
                $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
            }

        }
        
        $data['topic_content'] = TopicContent::where(['id'=>$topic_content_id])->first();
        $data['deleted'] = TopicContent::where(['id'=>$topic_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = TopicContent::where(['id'=>$topic_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Lecture Video has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Lecture Video remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/topic-lecture-video-list/'.$data['topic_content']->topic->id));
    }


    public function topic_exam_list($topic_id)
    {
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);
        $data['topic'] = Topic::where(['id'=>$topic_id])->first();
        $data['content_type'] = 3;      
        return view('admin.topics.exams.topic_exam_list', $data);

    }
    
    public function topic_exam_ajax_list(Request $request)
    {
        $topic_id = $request->topic_id; 
        $topic_exam_list = DB::table('exam as d1')->join('topic_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','3')->where('d2.topic_id',$topic_id)->where('d1.status','1')->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $topic_exam_list = $topic_exam_list->select('d1.*','d2.id as topic_content_id','d2.deleted_at');

        $topic_exam_list = Exam::with(['institute','course','session'])->join('topic_content','topic_content.content_id','exam.id')->select('exam.*','topic_content.id as topic_content_id')->where(['topic_content.content_type_id'=>'3','topic_content.topic_id'=>$request->topic_id])->whereNull('topic_content.deleted_at');

        if($request->institute_id)
        {
            $topic_exam_list->where('institute_id', $request->institute_id);
        }

        if($request->course_id)
        {
            $topic_exam_list->where('course_id', $request->course_id);
        }

        if($request->year)
        {
            $topic_exam_list->where('year',$request->year);
        }

        if($request->session_id)
        {
            $topic_exam_list->where('session_id', $request->session_id);
        }

        return Datatables::of($topic_exam_list)
            ->addColumn('action', function ($topic_exam_list) {

                $data['topic_exam_list'] = $topic_exam_list;
                
                return view('admin.topics.exams.topic_exam_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function topic_exam_add($topic_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['topic'] = Topic::find($topic_id);

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['module_name'] = 'Topic Exam Add';
        $data['title'] = 'Topic Exam Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.topics.exams.topic_exam_add', $data);
        
    }

    public function topic_exam_add_list(Request $request)
    {
        $topic_id = $request->topic_id;        
        $topic_exam_list = DB::table('exam as d1')->where('d1.status','1')->whereNull('d1.deleted_at');
        $topic_exam_list = $topic_exam_list->select('d1.*');
        $topic_exam_list = $topic_exam_list->addSelect(DB::raw($topic_id." as topic_id"));
        
        $topic_exam_list = Exam::with(['institute','course','session']);
    
        if($request->institute_id)
        {
            $topic_exam_list->where('institute_id', $request->institute_id);
        }

        if($request->course_id)
        {
            $topic_exam_list->where('course_id', $request->course_id);
        }

        if($request->year)
        {
            $topic_exam_list->where('year',$request->year);
        }

        if($request->session_id)
        {
            $topic_exam_list->where('session_id', $request->session_id);
        }
                
        return Datatables::of($topic_exam_list)
            ->addColumn('action', function ($topic_exam_list) use($request){
                $data['checked'] = "";
                $data['topic_exam_add_info'] = "";
                
                $topic_content = TopicContent::where(['topic_id'=>$request->topic_id,'content_type_id'=>'3','content_id'=>$topic_exam_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['topic_exam_list'] = $topic_exam_list;
                
                return view('admin.topics.exams.topic_exam_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function topic_exam_save(Request $request)
    {
        $data['topic_id'] = $request->topic_id;
        $data['content_type_id'] = 3;
        $data['exam_id'] = $request->exam_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $topic_exam = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->exam_id])->first();
            if(!isset($topic_exam))
            {
                $topic_exam = TopicContent::insert([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->exam_id,'created_by'=>Auth::id()]);
                if(isset($topic_exam))
                {
                    $topic_exams = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'] ])->get();
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
            $topic_contents = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->exam_id])->get();
            if(isset($topic_contents) && count($topic_contents))
            {
                foreach($topic_contents as $topic_content)
                {
                    $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                    ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                    ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
                }

            }

            $topic_exam = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->exam_id])->update(['deleted_by'=>Auth::id()]);
            $topic_exam = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->exam_id])->delete();
            if(isset($topic_exam))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed exam.</span';
            }

        }
        
        return response()->json($data);

    }

    public function topic_exam_edit($topic_content_id)
    {
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);
        $data['topic_content'] = TopicContent::where(['id'=>$topic_content_id])->first();
        $data['topic'] = Topic::find($data['topic_content']->topic->id);
        $data['module_name'] = 'Topic Exam Edit';
        $data['title'] = 'Topic Exam Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.topics.exams.topic_exam_edit', $data );
    }

    public function topic_exam_edit_list(Request $request)
    {
        $topic_id = $request->topic_id;        
        $topic_exam_list = DB::table('exam as d1')->where('d1.status','1')->whereNull('d1.deleted_at');
        $topic_exam_list = $topic_exam_list->select('d1.*');
        $topic_exam_list = $topic_exam_list->addSelect(DB::raw($topic_id." as topic_id")); 
        
        $topic_exam_list = Exam::with(['institute','course','session']);
    
        if($request->institute_id)
        {
            $topic_exam_list->where('institute_id', $request->institute_id);
        }

        if($request->course_id)
        {
            $topic_exam_list->where('course_id', $request->course_id);
        }

        if($request->year)
        {
            $topic_exam_list->where('year',$request->year);
        }

        if($request->session_id)
        {
            $topic_exam_list->where('session_id', $request->session_id);
        }
                
        return Datatables::of($topic_exam_list)
            ->addColumn('action', function ($topic_exam_list) use($request){
                $data['checked'] = "";
                $data['topic_exam_add_info'] = "";
                
                $topic_content = TopicContent::where(['topic_id'=>$request->topic_id,'content_type_id'=>'3','content_id'=>$topic_exam_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['topic_exam_list'] = $topic_exam_list;
                
                return view('admin.topics.exams.topic_exam_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function topic_exam_update(Request $request)
    {
        $data['topic_id'] = $request->topic_id;
        $data['topic_content_id'] = $request->topic_content_id;
        $data['content_type_id'] = 3;
        $data['exam_id'] = $request->exam_id;         
        $data['status'] = "incomplete";
        
        $topic_content = TopicContent::where(['id'=>$data['topic_content_id']])->first();
        if(isset($topic_content))
        {
            $topic_content = TopicContent::where(['id'=>$data['topic_content_id']])->update([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->exam_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited exam.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function topic_exam_delete($topic_content_id)
    {

        $topic_contents = TopicContent::where(['id'=>$topic_content_id])->get();
        if(isset($topic_contents) && count($topic_contents))
        {
            foreach($topic_contents as $topic_content)
            {
                $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
            }

        }
        
        $data['topic_content'] = TopicContent::where(['id'=>$topic_content_id])->first();
        $data['deleted'] = TopicContent::where(['id'=>$topic_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = TopicContent::where(['id'=>$topic_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Mentor has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Mentor remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/topic-exam-list/'.$data['topic_content']->topic->id));
    }

    public function topic_lecture_sheet_list($topic_id)
    {
        $data['topic'] = Topic::where(['id'=>$topic_id])->first();
        $data['content_type'] = 4;      
        return view('admin.topics.lecture_sheets.topic_lecture_sheet_list', $data);

    }
    
    public function topic_lecture_sheet_ajax_list(Request $request)
    {
        $topic_id = $request->topic_id; 
        $topic_lecture_sheet_list = DB::table('lecture_sheet as d1')->join('topic_content as d2','d2.content_id','d1.id')->where('d2.content_type_id','4')->where('d2.topic_id',$topic_id)->where('d1.status','1')->whereNull('d1.deleted_at')->whereNull('d2.deleted_at');
        $topic_lecture_sheet_list = $topic_lecture_sheet_list->select('d1.*','d2.id as topic_content_id','d2.deleted_at');

        return Datatables::of($topic_lecture_sheet_list)
            ->addColumn('action', function ($topic_lecture_sheet_list) {

                $data['topic_lecture_sheet_list'] = $topic_lecture_sheet_list;
                
                return view('admin.topics.lecture_sheets.topic_lecture_sheet_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function topic_lecture_sheet_add($topic_id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['topic'] = Topic::find($topic_id);

        $data['module_name'] = 'Topic Lecture Sheet Add';
        $data['title'] = 'Topic Lecture Sheet Add';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        
        return view('admin.topics.lecture_sheets.topic_lecture_sheet_add', $data);
        
    }

    public function topic_lecture_sheet_add_list(Request $request)
    {
        $topic_id = $request->topic_id;        
        $topic_lecture_sheet_list = DB::table('lecture_sheet as d1')->where('d1.status','1')->whereNull('d1.deleted_at');
        $topic_lecture_sheet_list = $topic_lecture_sheet_list->select('d1.*');
        $topic_lecture_sheet_list = $topic_lecture_sheet_list->addSelect(DB::raw($topic_id." as topic_id"));        
                
        return Datatables::of($topic_lecture_sheet_list)
            ->addColumn('action', function ($topic_lecture_sheet_list) {
                $data['checked'] = "";
                $data['topic_lecture_sheet_add_info'] = "";
                
                $topic_content = TopicContent::where(['topic_id'=>$topic_lecture_sheet_list->topic_id,'content_type_id'=>'4','content_id'=>$topic_lecture_sheet_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['topic_lecture_sheet_list'] = $topic_lecture_sheet_list;
                
                return view('admin.topics.lecture_sheets.topic_lecture_sheet_add_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function topic_lecture_sheet_save(Request $request)
    {
        $data['topic_id'] = $request->topic_id;
        $data['content_type_id'] = 4;
        $data['lecture_sheet_id'] = $request->lecture_sheet_id;         
        $data['status'] = "incomplete";
        if($request->operation == "insert")
        {            
            $topic_lecture_sheet = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_sheet_id])->first();
            if(!isset($topic_lecture_sheet))
            {
                $topic_lecture_sheet = TopicContent::insert([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_sheet_id,'created_by'=>Auth::id()]);
                if(isset($topic_lecture_sheet))
                {
                    $topic_lecture_sheets = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'] ])->get();
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
            $topic_contents = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_sheet_id])->get();
            if(isset($topic_contents) && count($topic_contents))
            {
                foreach($topic_contents as $topic_content)
                {
                    $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                    ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                    ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
                }

            }

            $topic_lecture_sheet = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_sheet_id])->update(['deleted_by'=>Auth::id()]);
            $topic_lecture_sheet = TopicContent::where([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_sheet_id])->delete();
            if(isset($topic_lecture_sheet))
            {
                $data['status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed lecture sheet.</span';
            }

        }
        
        return response()->json($data);

    }

    public function topic_lecture_sheet_edit($topic_content_id)
    {
        $data['topic_content'] = TopicContent::where(['id'=>$topic_content_id])->first();
        $data['topic'] = Topic::find($data['topic_content']->topic->id);
        $data['module_name'] = 'Topic Lecture Sheet Edit';
        $data['title'] = 'Topic Lecture Sheet Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.topics.lecture_sheets.topic_lecture_sheet_edit', $data );
    }

    public function topic_lecture_sheet_edit_list(Request $request)
    {
        $topic_id = $request->topic_id;        
        $topic_lecture_sheet_list = DB::table('lecture_sheet as d1')->where('d1.status','1')->whereNull('d1.deleted_at');
        $topic_lecture_sheet_list = $topic_lecture_sheet_list->select('d1.*');
        $topic_lecture_sheet_list = $topic_lecture_sheet_list->addSelect(DB::raw($topic_id." as topic_id"));        
                
        return Datatables::of($topic_lecture_sheet_list)
            ->addColumn('action', function ($topic_lecture_sheet_list) {
                $data['checked'] = "";
                $data['topic_lecture_sheet_add_info'] = "";
                
                $topic_content = TopicContent::where(['topic_id'=>$topic_lecture_sheet_list->topic_id,'content_type_id'=>'4','content_id'=>$topic_lecture_sheet_list->id])->first();
                if(isset($topic_content))
                {
                    $data['checked'] = "checked disabled";
                }
                else
                {
                    $data['checked'] = "";
                }
                
                $data['topic_lecture_sheet_list'] = $topic_lecture_sheet_list;
                
                return view('admin.topics.lecture_sheets.topic_lecture_sheet_edit_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function topic_lecture_sheet_update(Request $request)
    {
        $data['topic_id'] = $request->topic_id;
        $data['topic_content_id'] = $request->topic_content_id;
        $data['content_type_id'] = 4;
        $data['lecture_sheet_id'] = $request->lecture_sheet_id;         
        $data['status'] = "incomplete";
        
        $topic_content = TopicContent::where(['id'=>$data['topic_content_id']])->first();
        if(isset($topic_content))
        {
            $topic_content = TopicContent::where(['id'=>$data['topic_content_id']])->update([ 'topic_id' => $request->topic_id,'content_type_id' => $data['content_type_id'], 'content_id' => $request->lecture_sheet_id,'updated_by'=>Auth::id()]);
            $data['status'] = "completed";
            $data['message'] = '<br><span style="color:green;font-weight:700">Successfully edited lecture_sheet.</span';
            
            return response()->json($data);
        }
        
    }
    
    public function topic_lecture_sheet_delete($topic_content_id)
    {

        $topic_contents = TopicContent::where(['id'=>$topic_content_id])->get();
        if(isset($topic_contents) && count($topic_contents))
        {
            foreach($topic_contents as $topic_content)
            {
                $program_content_type = ScheduleProgramContentType::where('topic_content_type_id',$topic_content->content_type_id)->first();
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->update(['deleted_by'=>Auth::id()]);
                ProgramContent::where(['content_type_id'=>$program_content_type->id,'content_id'=>$topic_content->id])->delete();
            }

        }
        
        $data['topic_content'] = TopicContent::where(['id'=>$topic_content_id])->first();
        $data['deleted'] = TopicContent::where(['id'=>$topic_content_id])->update(['deleted_by'=>Auth::id()]);
        $data['deleted'] = TopicContent::where(['id'=>$topic_content_id])->delete();
        if( $data['deleted']){
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Mentor has been removed successfully !!!');
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Mentor remove unsuccessfull !!!');
        }
        
        return redirect(url('admin/topic-lecture-sheet-list/'.$data['topic_content']->topic->id));
    }


    
}  