<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\Http\Traits\ContentSelector;
use App\LectureVideoAssign;
use App\LectureVideoLink;
use App\LectureVideo;
use App\LectureVideoDiscipline;
use App\LectureVideoFaculty;
use App\LectureVideoBatchLectureVideo;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\LectureVideoPrice;
use App\Topics;
use App\Teacher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Session;
use Auth;
use Validator;
use App\TopicTeachers;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Response;


class LectureVideoController extends Controller
{
    use ContentSelector;
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
        $data['lecture_videos'] = LectureVideo::with( 'class' )->get( );
        $data['module_name'] = 'Lecture Video';
        $data['title'] = 'Lecture Video List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_video.list',$data);
    }

    public function lecture_video_list(Request $request) {
        $year = (int) $request->year;
        $course_id = (int) $request->course_id;
        $session_id = (int) $request->session_id;
        $price = $request->price;

        $lecture_video_list = DB::table('lecture_video as d1' )
            ->leftjoin('topics as d2', 'd1.class_id', '=','d2.id' )
            ->leftjoin('courses as c', 'd2.course_id', '=','c.id' )
            ->leftjoin('sessions as s', 'd2.session_id', '=','s.id' )
            ->leftjoin('teacher as t', 't.id', '=','d1.teacher_id' );

        if(isset($price)){
            if($price) {
                $lecture_video_list
                    ->join('lecture_video_price as lvp', 'lvp.lecture_video_id', '=', 'd1.id')
                    ->distinct('d1.id');
            } else {
                $lecture_video_list
                    ->leftJoin('lecture_video_price as lvp', 'lvp.lecture_video_id', '=', 'd1.id')
                    ->whereNull('lvp.id');
            }
        }

        if($year) {
            $lecture_video_list = $lecture_video_list->where('d2.year', $year);
        }

        if($course_id) {
            $lecture_video_list = $lecture_video_list->where('d2.course_id', $course_id);
        }

        if($session_id) {
            $lecture_video_list = $lecture_video_list->where('d2.session_id', $session_id);
        }

        $lecture_video_list->select(
            'd1.id as id',
            'd1.name as lecture_video_name',
            'd2.name as class_name',
            'd2.year as year',
            'c.name as course_name',
            's.name as session_name',
            'd1.type as type_name',
            'd1.lecture_address as lecture_link_address',
            'd1.password as video_password',
            'd1.pdf_file as pdf_file_link',
            'd1.status as status',
            't.name as teacher_name',
        );

        $lecture_video_list = $lecture_video_list->whereNull('d1.deleted_at');

        return DataTables::of($lecture_video_list)
            ->addColumn('action', function ($lecture_video_list) {
                return view('admin.lecture_video.lecture_video_ajax_list',(['lecture_video_list'=>$lecture_video_list]));
            })

            ->addColumn('play', function ($lecture_video_list) {
                return '
                    <a
                        target="_blank"
                        href="' . ($lecture_video_list->lecture_link_address ?? '#') . '">

                        <svg width="30" class="text-info" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                ';
            })

            ->addColumn('status',function($lecture_video_list){
                return '<span style="color:' .( $lecture_video_list->status == 1 ? 'green;':'red;' ).' font-size: 14px;  ">'
                        . ($lecture_video_list->status == 1 ? 'Active':'Inactive') . '</span>';
            })

            ->addColumn('pdf_file_link',function($lecture_video_list){
                return '<span>'. '<a href="'.('/pdf/'.$lecture_video_list->pdf_file_link ).' " target="_blank">'.( $lecture_video_list->pdf_file_link ).'</a>' . '</span>';
            })

            ->addColumn('type_name',function($lecture_video_list) {

                if($lecture_video_list->type_name == "1") {
                    return "Regular Class";
                }
                else if($lecture_video_list->type_name == "2") {
                    return "Solve Class";
                }
                else if($lecture_video_list->type_name == "3") {
                    return "Feedback Class";
                }
                else {
                    return "Others";
                }
            })

            // ->addColumn('type_name',function($lecture_video_list) {
            //     $types = [  '1' => 'Regular Class','2' => 'Solve Class', '3' => 'Feedback Class', '4' => 'Others' ];
            //     return $types[$lecture_video_list->type_name];
            // })
            ->rawColumns(['action', 'play', 'status', 'pdf_file_link', 'type_name'])

        ->make(true);
    }

    public function subscriptionStatus(LectureVideo $lecture_video)
    {
        $lecture_video->timestamps = false;
        $lecture_video->is_show_subscription = $lecture_video->is_show_subscription ? 0 : 1;
        $lecture_video->subscription_change_by = Auth::id();
        $lecture_video->save();

        return response([
            'message'   => (string) 'Success',
            'status'    => (boolean) ($lecture_video->is_show_subscription ?? 0),
        ]);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscription_video()
    {
      /*  if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['lecture_videos'] = LectureVideo::with( 'class' )->get( );
        $data['module_name'] = 'Lecture Video';
        $data['title'] = 'Lecture Video List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_video.subscription_list',$data);
    }


    public function subscription_video_list(Request $request) {
        $year = (int) $request->year;
        $course_id = (int) $request->course_id;
        $session_id = (int) $request->session_id;

        $lecture_video_list = DB::table('lecture_video as d1' )
            ->leftjoin('topics as d2', 'd1.class_id', '=','d2.id' )
            ->leftjoin('courses as c', 'd2.course_id', '=','c.id' )
            ->leftjoin('sessions as s', 'd2.session_id', '=','s.id' )
            ->leftjoin('teacher as t', 't.id', '=','d1.teacher_id' )
            ->join('lecture_video_price as lvp', 'lvp.lecture_video_id', '=', 'd1.id')
            ->whereNull('lvp.deleted_at')
            ->where('d1.status', 1)
            ->whereNotNull('d1.lecture_address')
            ->distinct('d1.id');

        if($year) {
            $lecture_video_list = $lecture_video_list->where('d2.year', $year);
        }

        if($course_id) {
            $lecture_video_list = $lecture_video_list->where('d2.course_id', $course_id);
        }

        if($session_id) {
            $lecture_video_list = $lecture_video_list->where('d2.session_id', $session_id);
        }

        $lecture_video_list->select(
            'd1.id as id',
            'd1.name as lecture_video_name',
            'd2.name as class_name',
            'd2.year as year',
            'c.name as course_name',
            's.name as session_name',
            'd1.type as type_name',
            'd1.lecture_address as lecture_link_address',
            'd1.password as video_password',
            'd1.pdf_file as pdf_file_link',
            'd1.status as status',
            't.name as teacher_name',
            'd1.is_show_subscription as is_show_subscription',
        );

        $lecture_video_list = $lecture_video_list->whereNull('d1.deleted_at');

        return DataTables::of($lecture_video_list)
            ->addColumn('action', function ($lecture_video_list) {
                return view('admin.lecture_video.subscription_ajax_list',(['lecture_video_list'=>$lecture_video_list]));
            })

            ->addColumn('play', function ($lecture_video_list) {
                return '
                    <a
                        target="_blank"
                        href="' . ($lecture_video_list->lecture_link_address ?? '#') . '">

                        <svg width="30" class="text-info" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                ';
            })

            ->addColumn('status',function($lecture_video_list){
                return '<span style="color:' .( $lecture_video_list->status == 1 ? 'green;':'red;' ).' font-size: 14px;  ">'
                        . ($lecture_video_list->status == 1 ? 'Active':'Inactive') . '</span>';
            })

            ->addColumn('allchecked',function($lecture_video_list){
                return '<input type="checkbox" class="subscriptionUpdate" name="language" id="language1" value="English" data-lecture-video-id="'.$lecture_video_list->id.'"/>';
            })

            ->addColumn('allcheckedunsubscribe',function($lecture_video_list){
                return '<input type="checkbox" class="subscriptionUpdate" name="language" id="language1" value="English" data-lecture-video-id="'.$lecture_video_list->id.'"/>';
            })

            ->addColumn('is_show_subscription',function($lecture_video_list){
                return '<button type="button" onclick="subscriptionStatus(this, ' . $lecture_video_list->id . ')" class="btn btn-xs ' . ( $lecture_video_list->is_show_subscription == 1 ? 'btn-info':'btn-danger' ) . '">'
                        . ($lecture_video_list->is_show_subscription == 1 ? 'Yes':'No') . '</button>';
            })

            ->addColumn('pdf_file_link',function($lecture_video_list){
                return '<span>'. '<a href="'.('/pdf/'.$lecture_video_list->pdf_file_link ).' " target="_blank">'.( $lecture_video_list->pdf_file_link ).'</a>' . '</span>';
            })

            ->addColumn('type_name',function($lecture_video_list) {

                if($lecture_video_list->type_name == "1") {
                    return "Regular Class";
                }
                else if($lecture_video_list->type_name == "2") {
                    return "Solve Class";
                }
                else if($lecture_video_list->type_name == "3") {
                    return "Feedback Class";
                }
                else {
                    return "Others";
                }
            })

            // ->addColumn('type_name',function($lecture_video_list) {
            //     $types = [  '1' => 'Regular Class','2' => 'Solve Class', '3' => 'Feedback Class', '4' => 'Others' ];
            //     return $types[$lecture_video_list->type_name];
            // })
            ->rawColumns(['action', 'play', 'status', 'is_show_subscription', 'pdf_file_link', 'type_name', 'allchecked'])

        ->make(true);
    }
    

    const VideoTypes = [  '1' => 'Regular Class','2' => 'Solve Class', '3' => 'Feedback Class', '4' => 'Others' ];

    public static function getVideoTypes(){
        return Collection::make(self::VideoTypes );
    }


    public static function getVideoType($value){
        $videoTypes = self::getVideoTypes();
        return $videoTypes[$value] ?? ' ';
    }

    protected function selection_config( )
    {
        return [
            'institutes' => [
                'label_column_count' => 3,
                'column_count' => 7,
                'label' => 'Class Institute',
            ],
            'courses' => [
                'label' => 'Class Course',
                'label_column_count' => 3,
                'column_count' => 7,
            ],
            'sessions' => [
                'label' => 'Class Session',
                'label_column_count' => 3,
                'column_count' => 7,
            ],
            'batches' => [
                'label' => 'Class Batch',
                'label_column_count' => 3,
                'column_count' => 7,
            ],
        ];
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
        $data['video_types'] = self::getVideoTypes();
        $data['module_name'] = 'Lecture Video';
        $data['title'] = 'Lecture Video Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';
        $data[ 'has_class_id_column' ] = Schema::hasColumn( 'lecture_video', 'class_id' );

        $data[ 'years' ] = $this->years( );
        $data[ 'institutes_view' ] = $this->institutes( request( ) )->render( );
        $data[ 'courses_view' ] = $this->courses( request( ) )->render( );
        $data[ 'sessions_view' ] = $this->sessions( request( ) )->render( );
        $data['teachers'] = Teacher::pluck('name','id');
        
        if( old('classes') ) {
            $selected_topic = Topics::find( old('classes' ) );
            $data[ 'topic_name' ] = $selected_topic->name ?? '';
        }else {
            $data[ 'topic_name' ] = '';
        }



        return view('admin.lecture_video.create',$data);
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
            'name' => [ 'required' ],
            'description' => [ 'required' ],
            'type' => [ 'required' ],
//            'lecture_address' => [ 'required' ],
        ]);

        if ( $validator->fails( ) ){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return  back()->withInput();
        }

        if( $msg = $this->lecture_video_exists($request ) ) {
            return  back()->with( [ 'class' => 'alert-danger', 'message' => $msg ] )->withInput( );
        }


        else{

            $lecture_video = new LectureVideo( );
            $lecture_video->name = $request->name;
            $lecture_video->nickname = $request->nickname;
            $lecture_video->description = $request->description;
            $lecture_video->type = $request->type;
            $lecture_video->lecture_address = $request->lecture_address ?? '';
            $lecture_video->password = $request->password ?? '';
            $lecture_video->teacher_id = $request->teacher_id;
            $lecture_video->is_show_subscription = $request->is_show_subscription ?? '0';

            $lecture_video->year = $request->year;
            $lecture_video->institute_id = $request->institute_id;
            $lecture_video->course_id = $request->course_id;
            $lecture_video->session_id = $request->session_id;

            if( Schema::hasColumn( 'lecture_video', 'class_id' )) {
                $lecture_video->class_id = $request->classes;
            }

            if( $request->hasFile( 'pdf' ) ){
                $file = $request->file('pdf');
                $extension = $file->getClientOriginalExtension();
                $filename = date("ymd").'_'.time().'.'.$extension;
                $file->move('pdf/',$filename);
                $lecture_video->pdf_file = $filename;
            }
            else {
                $lecture_video->pdf_file = '';
            }

            $lecture_video->status=$request->status;
            $lecture_video->created_by=Auth::id();
            $lecture_video->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureVideoController@index');
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
        $lecture_video=LectureVideo::select('lecture_videos.*')->find($id);
        return view('admin.lecture_video.show',['lecture_video'=>$lecture_video]);
    }

    public function edit_data( $id ){
        $lecture_video = LectureVideo::with('class' )->find($id);
        if( old('classes' ) ) {
            $lecture_video->class = Topics::find( old('classes' ) );
        }

        $data['teachers'] =  DB::table('topic_teachers')
        ->join('teacher', 'teacher.id' ,'topic_teachers.teacher_id' )
        ->where('topic_teachers.deleted_by', NULL)
        ->where('topic_teachers.topic_id', $lecture_video->class_id)
        ->pluck('teacher.name','teacher.id');

        $data['lecture_video'] = $lecture_video;
        $data['module_name'] = 'Lecture Video';
        $data['video_types'] = self::getVideoTypes();
        $data['title'] = 'Lecture Video Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        $data[ 'has_class_id_column' ] = Schema::hasColumn( 'lecture_video', 'class_id' );


        //dd( $lecture_video->class );

        $institute_id = old('institute_id', $lecture_video->class->institute_id ?? null);
        $course_id = old('course_id', $lecture_video->class->course_id ?? null);
        $session_id = old('session_id', $lecture_video->class->session_id ?? null);
        $year = old('year', $lecture_video->class->year ?? null);

        $data[ 'years' ] = $this->years( );
        $data[ 'institutes_view' ] = $this->institutes( request( ), $institute_id )->render( );
        $data[ 'courses_view' ] = $this->courses( request( ),$course_id,$institute_id )->render( );
        $data[ 'sessions_view' ] = $this->sessions( request( ),$session_id ,$course_id, $year)->render( );
      
    
        return  $data;

        //return view('admin.lecture_video.edit', $data);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function  duplicate( $id ){

        $data = $this->edit_data( $id );
        $data['submit_value'] = 'Duplicate';
        $data[ 'lecture_video' ]->class = null;
        $data[ 'action' ] = 'duplicate';

        return view('admin.lecture_video.edit', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data = $this->edit_data( $id );
        $data[ 'action' ] = 'edit';

  

        return view('admin.lecture_video.edit', $data);

    }


    protected function lecture_video_exists( Request $request, $id = null ){

        $name_exists_where = [ 'name'=>$request->name , 'type' => $request->type, 'class_id' => $request->classes ];

        if ( LectureVideo::where( 'id', '!=', $id )->where( $name_exists_where ) ->exists() ){
            return 'This Lecture Address Name already exists';
        }

        $topic = Topics::where( 'id', $request->classes )->first( );
        $link_exists_where = [ 'lecture_address' => $request->lecture_address, 'class_id' => $request->classes ];

        if ( $topic && LectureVideo::where( 'id', '!=', $id )->where( $link_exists_where )->exists( )) {
            return 'This Lecture Address already exists in <strong><em>"' . $topic->name . '" </em></strong> class';
        }
        return null;
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
            'description' => ['required'],
            'type' => ['required'],
//            'lecture_address' => ['required'],
        ]);
        if ($validator->fails()){
            //dd( $validator->errors() );

            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }
// return $request->year;
        $lecture_video = LectureVideo::find($id);

        if( $msg = $this->lecture_video_exists($request, $id ) ) {
//            Session::flash( 'class', 'alert-danger' );
//            session()->flash( 'message', $msg );
            return  back()->with( [ 'class' => 'alert-danger', 'message' => $msg ] )->withInput();
        }

//        if( !($lecture_video->name == $request->name && $lecture_video->type == $request->type) ) {
//
//            if ( LectureVideo::where([ 'name'=>$request->name , 'type' =>$request->type ])->exists( ) ){
//                Session::flash('class', 'alert-danger');
//                session()->flash('message','This Lecture Address Name already exists');
//                return redirect()->action('Admin\LectureVideoController@edit',[$id])->withInput();
//            }
//
//        }
//
//        if( !($lecture_video->lecture_address == $request->lecture_address && $lecture_video->class_id == $request->classes) ) {
//            if (  $message = $this->lecture_video_exists( $request )){
//                Session::flash('class', 'alert-danger');
//                session()->flash( 'message', $message );
//                return redirect()->action('Admin\LectureVideoController@edit',[$id])->withInput();
//            }
//        }

        $lecture_video->name = $request->name;
        $lecture_video->nickname = $request->nickname;
        $lecture_video->description = $request->description;
        $lecture_video->type = $request->type;
        $lecture_video->teacher_id = $request->teacher_id;
        $lecture_video->lecture_address = $request->lecture_address ?? '';
        $lecture_video->password = $request->password ?? '';
        $lecture_video->is_show_subscription = $request->is_show_subscription ?? '0';

        $lecture_video->year = $request->year;
        $lecture_video->institute_id = $request->institute_id;
        $lecture_video->course_id = $request->course_id;
        $lecture_video->session_id = $request->session_id;

        if( Schema::hasColumn('lecture_video', 'class_id')) {
            if( !empty( $request->classes ) ) {
                $lecture_video->class_id = $request->classes;
            }
        }

        if($request->hasFile('pdf')){
            $file = $request->file('pdf');
            $extension = $file->getClientOriginalExtension();
            $filename = date("ymd").'_'.time().'.'.$extension;
            $file->move('pdf/',$filename);
            $lecture_video->pdf_file = $filename;
        }

        $lecture_video->status=$request->status;
        $lecture_video->updated_by=Auth::id();
        $lecture_video->push();

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
       if( LectureVideo::find( $id ) ) {
            LectureVideo::where( 'id', $id )->update( [ 'deleted_by' => Auth::id() ]);
            LectureVideo::where( 'id', $id )->delete( ); //1 way
        }


        if (LectureVideoAssign::where('lecture_video_id', $id)->first()) {
            LectureVideoAssign::where('lecture_video_id', $id)->delete();
        }

        if (LectureVideoFaculty::where('lecture_video_id', $id)->first()) {
            LectureVideoFaculty::where('lecture_video_id', $id)->delete();
        }

        if (LectureVideoDiscipline::where('lecture_video_id', $id)->first()) {
            LectureVideoDiscipline::where('lecture_video_id', $id)->delete();
        }

        if (LectureVideoBatchLectureVideo::where('lecture_video_id', $id)->first()) {
            LectureVideoBatchLectureVideo::where('lecture_video_id', $id)->delete();
        }

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureVideoController@index');
    }

    public function download_emails($id){

        $lecture_video = LectureVideo::find($id);
        $emails = array();

        foreach($lecture_video->batches as $batch){
            unset($lecture_video_batch_id);
            $lecture_link = LectureVideoLink::where('id',$batch->lecture_video_batch_id)->get()[0];
            unset($doctors_courses);
            $doctors_courses = DoctorsCourses::where(['year'=>$lecture_link->year,'session_id'=>$lecture_link->session_id,'institute_id'=>$lecture_link->institute_id,'course_id'=>$lecture_link->course_id,'batch_id'=>$lecture_link->batch_id])->get();

            foreach($doctors_courses as $doctor_course){
                $emails[] = $doctor_course->doctor->email;
            }
        }

        $content = implode(',',$emails);
        $file_name = $lecture_video->name.'.csv';
        $headers = [
                        'Content-type'        => 'text/csv',
                        'Content-Disposition' => 'attachment; filename='.$file_name,
                ];

        return Response::make($content, 200, $headers);

    }

    public function lecture_video_trash()
    {
        $data = LectureVideo::onlyTrashed()->orderBy('deleted_at', 'asc')->get();
        return view('admin.lecture_video.lecture_video_trash',['data'=>$data , 'trash'=> true]);
    }

    public function lecture_video_restore($id)
    {
        LectureVideo::withTrashed()->where('id', $id)->restore();
        return redirect()->action('Admin\LectureVideoController@index')->withInput();
    }

    public function change_mentor(Request $request){

        $data['topic_teachers'] =  DB::table('topic_teachers')
        ->join('teacher', 'teacher.id' ,'topic_teachers.teacher_id' )
        ->where('topic_teachers.deleted_by', NULL)
        ->where('topic_teachers.topic_id', $request->class_id)
        ->pluck('teacher.name','teacher.id');

// return  $data['topic_teachers'] ;
    //    $topic_teachers = TopicTeachers::with('teacher')->where(['topic_id'=> $request->topic_id , 'deleted_at' => NULL] )->pluck('teacher.name','teacher.id');
       return view('admin.lecture_video.mentor',  $data);
    }

    public function lecture_video_price($id)
    {
        if(LectureVideo::where(['id'=>$id])->first() === null) 
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'This lecture video doesn\'t exist!!!');
            return redirect('/admin/lecture-video');
        }

        $data['lecture_video'] = LectureVideo::where(['id'=>$id])->first();
        $lecture_video_prices = $data['lecture_video']->lecture_video_prices;
        $today = date("Ymd",time());
        $data['active_index'] = '0';
        if(isset($lecture_video_prices))
        {
            foreach($lecture_video_prices as $key=>$lecture_video_price)
            {
                if(str_replace("-","",trim($lecture_video_price->active_from)) <= $today)
                {
                    $data['active_index'] = $key;
                }
            }
        }
        $data['title'] = "Lecture Video Price";
        return view('admin.lecture_video.lecture_video_price', $data);
    }

    public function lecture_video_price_save(Request $request)
    {
        $lecture_video = LectureVideo::where(['id'=>$request->lecture_video_id])->first();
        if(!isset($lecture_video)) 
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'This lecture video doesn\'t exist!!!');
            return redirect('/admin/lecture-video');
        }

        if(LectureVideoPrice::where(['lecture_video_id'=>$request->lecture_video_id,'active_from'=>$request->active_from])->first())
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'This lecture video price activation date already exist!!!');
            return redirect('/admin/lecture-video-price/'.$request->lecture_video_id);
        }
        
        $lecture_video_price = new LectureVideoPrice();
        $lecture_video_price->lecture_video_id = $request->lecture_video_id;
        $lecture_video_price->active_from = $request->active_from;
        $lecture_video_price->price = $request->price;
        $lecture_video_price->created_by = Auth::id();
        $lecture_video_price->save();
        
        
        return redirect('/admin/lecture-video-price/'.$request->lecture_video_id);
    }

    public function lecture_video_price_edit($id)
    {
        if(LectureVideoPrice::where(['id'=>$id])->first() === null) 
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'This lecture video price doesn\'t exist!!!');
            return redirect('/admin/lecture-video');
        }

        $data['lecture_video_price'] = LectureVideoPrice::where(['id'=>$id])->first();
        $data['title'] = "Lecture Video Price Edit";
        return view('admin.lecture_video.lecture_video_price_edit', $data);
    }

    public function lecture_video_price_update(Request $request)
    {
        $lecture_video_price = LectureVideoPrice::where(['id'=>$request->lecture_video_price_id])->first();
        if(!isset($lecture_video_price)) 
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'This lecture video price doesn\'t exist!!!');
            return redirect('/admin/lecture-video-price/'.$lecture_video_price->lecture_video->id);
        }

        if($lecture_video_price->active_from != $request->active_from)
        {
            if(LectureVideoPrice::where(['lecture_video_id'=>$request->lecture_video_id,'active_from'=>$request->active_from])->first())
            {
                Session::flash('class', 'alert-danger');
                Session::flash('message', $request->active_from.' - This Price activation date already exist for the lecture video... If you want edit that!!!');
                return redirect('/admin/lecture-video-price/'.$request->lecture_video_id);
            }

        }        

        LectureVideoPrice::where(['id'=>$request->lecture_video_price_id])->update(['lecture_video_id'=>$request->lecture_video_id,'active_from'=>$request->active_from,'price'=>$request->price,'updated_by'=>Auth::id()]);
        
        return redirect('/admin/lecture-video-price/'.$request->lecture_video_id);
    }
}
