<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Institutes;
use App\Subjects;
use App\TopicFaculty;
use App\TopicSubject;
use App\Teacher;
use App\TopicTeachers;
use Illuminate\Http\Request;
use App\Topics;
use App\Courses;
use App\Sessions;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TopicController extends Controller
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
        $user = Auth::user();

        $can_edit = $user->can('Class/Chapter Edit');
        $can_delete = $user->can('Class/Chapter Delete');

        $topics = Topics::with('course', 'institute', 'session' )->get();
        $title = 'Class/Chapter List';

        // $years = $this->getYears();
        // $years = array(''=>'Select year');
        // for($year = date("Y")+1;$year>=2017;$year--){
        //     $years[$year] = $year;
        // }
        // $courses= Courses::get()->pluck('name', 'id');
        // $sessions = Sessions::get()->pluck('name', 'id');
        // $subjects = Subjects::get()->pluck('name', 'id');
        // $subjects = Subjects::get()->pluck('name', 'id');
        $classes = Topics::get()->pluck('name', 'id');
        return view('admin.topic.list',compact(
            'topics',
            'title', 
            'can_edit', 
            'can_delete',
            // 'years',
            // 'courses',
            // 'sessions',
            // 'subjects',
            'classes'
        ));
        
    }

    public function class_chapter_list(Request $request) {

        $year = $request->year;
        $session_id = $request->session_id;
        $course_id = $request->course_id;
        $subject_id = $request->subject_id;
        $bcps_subject_id = $request->bcps_subject_id;
        $faculty_id = $request->faculty_id;

        $class_chapter_list = DB::table('topics as d1' )
            ->leftjoin('institutes as d2', 'd1.institute_id', '=','d2.id')
            ->leftjoin('courses as d3', 'd1.course_id', '=','d3.id')
            ->leftjoin('sessions as d4', 'd1.session_id', '=','d4.id');
        
        if($year){
            $class_chapter_list = $class_chapter_list->where('d1.year', '=', $year);
        }
        if($course_id){
            $class_chapter_list = $class_chapter_list->where('d1.course_id', '=', $course_id);
        }
        if($session_id){
            $class_chapter_list = $class_chapter_list->where('d1.session_id', '=', $session_id);
        }
        if($subject_id){
            $class_chapter_list = $class_chapter_list->rightjoin('topic_subjects as d5', 'd1.id' , 'd5.topic_id');            
            $class_chapter_list = $class_chapter_list->where('d5.subject_id', '=', $subject_id);                        
        }
        
        if($faculty_id){
            
            if(isset($bcps_subject_id))
            {
                $class_chapter_list = $class_chapter_list->rightjoin('topic_faculties as d6', 'd1.id' , 'd6.topic_id');
                $class_chapter_list = $class_chapter_list->where('d6.faculty_id', '=', $faculty_id);
                $class_chapter_list = $class_chapter_list->rightjoin('topic_subjects as d5', 'd1.id' , 'd5.topic_id');
                $class_chapter_list = $class_chapter_list->where('d5.subject_id', '=', $bcps_subject_id);
            }
            else
            {
                $class_chapter_list = $class_chapter_list->rightjoin('topic_faculties as d6', 'd1.id' , 'd6.topic_id');
                $class_chapter_list = $class_chapter_list->where('d6.faculty_id', '=', $faculty_id);
            }          
                
            
        }
        
        $class_chapter_list->select(
            'd1.id as id',
            'd1.year as year',
            'd1.name as chapte_name',
            'd2.name as institutes_name',
            'd3.name as course_name',
            'd4.name as session_name',
           
        );
        $class_chapter_list = $class_chapter_list->whereNull('d1.deleted_at');   

        return DataTables::of($class_chapter_list)
            ->addColumn('action', function ($class_chapter_list) {
                return view('admin.topic.ajax_topic_list',(['class_chapter_list'=>$class_chapter_list]));
            })

        ->make(true);
    }

    private function getYears(){
        $current = date('Y');
        $prev =  $current - 1;
        $prev2 = $current - 2;
        $prev3 = $current - 3;
        $next =  $current + 1;

        return [$prev3=>$prev3 , $prev2=>$prev2, $prev => $prev, $current => $current, $next => $next ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Class/Chapter Create';
        $data['years'] = $this->getYears();
        $data['teachers'] = Teacher::get()->pluck('name', 'id');
        $data['action'] = 'create';

        return view('admin.topic.form',$data );
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
            'institute_id' => ['required'],
            'year' => ['required'],
            'course_id' => ['required'],
            'session_id' => ['required'],
            // 'teacher_id' =>['required'],
        ]);

        if ( $validator->fails( ) ){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter valid data!!!');
            return redirect()->action('Admin\TopicController@create')->withInput();
        }

        if (Topics::where('name',$request->topic_name)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Class/Chapter already exists');
            return redirect()->action('Admin\TopicController@create')->withInput();
        }

        $topic = new Topics;

        $topic->name = $request->name;
        $topic->course_id = $request->course_id;
        $topic->institute_id = $request->institute_id;
        $topic->year = $request->year;
        $topic->session_id = $request->session_id;
        $topic->status = 1;
        $topic->created_by = Auth::id();


        if( $topic->save() ) {
            $this->updateRelations( $topic, $request );
        }

        $teachers_ids = $request->teacher_ids;
        foreach($teachers_ids as $teacher_id){
            if($teacher_id == null){
                continue;
            }else{
                $topic_teacher = new TopicTeachers();
                $topic_teacher->topic_id = $topic->id;
                $topic_teacher->teacher_id = $teacher_id;
                $topic_teacher->push();
            }

        }

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\TopicController@index');
    }


    public function updateRelations( Topics  $topic, Request $request ){
        $topic_id = $topic->id;

        $this->save_relation( TopicFaculty::class,
            [ 'topic_id' => $topic_id ],
            $request->faculty_ids,
            [ 'topic_id' => $topic_id, 'faculty_id' => '@value@' ] );

        $this->save_relation( TopicSubject::class,
            [ 'topic_id' => $topic_id, 'combined_bcps' => 0 ],
            $request->subject_ids,
            [ 'topic_id' => $topic_id, 'subject_id' => '@value@' ] );

        $this->save_relation( TopicSubject::class,
            [ 'topic_id' => $topic_id, 'combined_bcps' => 1 ],
            $request->bcps_subject_ids,
            [ 'topic_id' => $topic_id, 'subject_id' => '@value@', 'combined_bcps' => 1 ] );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=Subjects::select('users.*')->find($id);
        return view('admin.subjects.show',['user'=>$user]);
    }


    public function edit_data( $id ){
        $data = [];
        $data['years'] = $this->getYears();
        $data['title'] = 'GENESIS Admin : Class/Chapter Edit';
        $topic = Topics::with('faculties','topics_ids.teacher','subjects', 'bcps_subjects')->find($id);

        foreach( $topic->topics_ids as $item){
            $data['selected_teachers'][] = $item->teacher->id;
        }
        $data['teachers'] = Teacher::pluck('name','id');

        $data['topic'] = &$topic;
        if( !$topic->institute_id ) {
            $topic->institute_id = Courses::where( 'id', $topic->course_id )->value( 'institute_id' );
        }

        $data['selected_faculty_ids'] = $topic->faculties->pluck('id' );
        $data['selected_subject_ids'] = $topic->subjects->pluck('id' );
        $data['selected_bcps_subject_ids'] = $topic->bcps_subjects->pluck('id' );

        return $data;

    }

    public function edit( $id )
    {
        $data = $this->edit_data( $id );
        // return $data;
        $data['action'] = 'edit';

        return view('admin.topic.form',$data );
    }

    public function duplicate( $id )
    {
        $data = $this->edit_data( $id );
        $data['action'] = 'duplicate';
        // $data['topic']->course_id = null;

        return view('admin.topic.form',$data );

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
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'year' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'session_id' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }
        

        $topic = Topics::find($id);

        if( $request->name != $topic->name ){
            if (Topics::where('name',$request->name)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Class/Chapteralready exists');
                return redirect()->back()->withInput();
            }
        }
        
        $topic->name = $request->name;
        $topic->year = $request->year;
        $topic->institute_id = $request->institute_id;
        $topic->course_id = $request->course_id;
        $topic->session_id = $request->session_id;


        $topic->push();


        $this->updateRelations( $topic, $request );

        
        $teachers_ids_all = $request->teacher_ids;
        // $teachers_ids = [];
        foreach($teachers_ids_all as $single){
            if($single == null){
                continue;
            }else{
                $teachers_ids[] = $single;
            }
            
        }
        // return getType($teachers_ids);
        $topic_teachers_remove = TopicTeachers::where('topic_id', $topic->id)->get();
        if($topic_teachers_remove){

            foreach($topic_teachers_remove as $topic_teacher_remove){
                $topic_teacher_remove->deleted_by = Auth::id();
                $topic_teacher_remove->push();
            }
            foreach($teachers_ids as $teacher_id){

                if( TopicTeachers::where([ 'topic_id' => $topic->id, 'teacher_id' => $teacher_id, 'deleted_by' => NULL ])->doesntExist() ) {
                    $topic_teacher = new TopicTeachers();
                    $topic_teacher->topic_id = $topic->id;
                    $topic_teacher->teacher_id = $teacher_id;
                    $topic_teacher->push();;
                }
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

       /* if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        Topics::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\TopicController@index');
    }
}
