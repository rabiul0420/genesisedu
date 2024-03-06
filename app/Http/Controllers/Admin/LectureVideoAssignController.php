<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\LectureVideoAssignLink;
use App\LectureVideo;
use App\LectureVideoAssign;
use App\LectureVideoDiscipline;
use App\LectureVideoFaculty;
use App\LectureVideoAssignBatchLectureVideoAssign;
use App\Providers\AppServiceProvider;
use App\Sessions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Topics;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class LectureVideoAssignController extends Controller
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
        $data['lecture_video_assigns'] = LectureVideoAssign::with('lecture_video','institute','course' )->get();
        $data['module_name'] = 'Lecture Video Assign';
        $data['title'] = 'Lecture Video Assign List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_video_assign.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    public function lecture_video_assign_list() {
        $lecture_video_assign_list = DB::table('lecture_video_assigns as d1')
                                    ->leftjoin('lecture_video as d2', 'd1.lecture_video_id', 'd2.id')
                                    ->leftjoin('institutes as d3', 'd1.institute_id', 'd3.id')
                                    ->leftjoin('courses as d4', 'd1.course_id', 'd4.id');
        
        $lecture_video_assign_list->select(
            'd1.id as id',
            'd2.name as video_name',
            'd3.name as institute_name',
            'd4.name as course_name',
            'd1.status as status'
        );

        $lecture_video_assign_list = $lecture_video_assign_list->whereNull('d1.deleted_at');

        return DataTables::of($lecture_video_assign_list)
            ->addColumn('action', function ($lecture_video_assign_list) {
                return view('admin.lecture_video_assign.video_assign_ajax_list',(['lecture_video_assign_list'=>$lecture_video_assign_list]));
            })
            
            ->addColumn('status',function($lecture_video_assign_list){
                return '<span style="color:' .( $lecture_video_assign_list->status == 1 ? 'green;':'red;' ).'">'
                        . ($lecture_video_assign_list->status == 1 ? 'Active':'Inactive') . '</span>';
            })
            ->rawColumns(['action','status',])

        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

//        $this->save_relation( LectureVideoDiscipline::class, [ 'lecture_video_assign_id' => 1652 ], [ 130, 153 ],
//            [ 'lecture_video_assign_id' => 1652, 'lecture_video_id' => 27, 'subject_id' => '@value@' ] );


        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['lecture_videos'] = LectureVideo::pluck('name', 'id');

        $data['module_name'] = 'Lecture Video Assign';
        $data['title'] = 'Lecture Video Assign Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_video_assign.create',$data);
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
            'lecture_video_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],    
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\LectureVideoAssignController@create')->withInput();
        }        

        if (LectureVideoAssign::where(['lecture_video_id'=>$request->lecture_video_id,'course_id'=>$request->course_id])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This lecture video assignment already exists');
            return redirect()->action('Admin\LectureVideoAssignController@create')->withInput();
        }
        else{

            $lecture_video_assign = new LectureVideoAssign();

            $lecture_video_assign->lecture_video_id = $request->lecture_video_id;
            $lecture_video_assign->institute_id = $request->institute_id;
            $lecture_video_assign->course_id = $request->course_id;
            $lecture_video_assign->status=$request->status;
            $lecture_video_assign->created_by=Auth::id();
            $lecture_video_assign->save();

            $this->save_subject_faculty_relation( $lecture_video_assign, $request );

            Session::flash( 'message', 'Record has been added successfully' );

            return redirect()->action('Admin\LectureVideoAssignController@index');
        }
    }


    function save_subject_faculty_relation( LectureVideoAssign $lecture_video_assign, Request $request){
        $institute = Institutes::where('id',$request->institute_id)->first();


        if( $institute->type == 1 ) {
            $this->save_relation(
                LectureVideoFaculty::class,
                [ 'lecture_video_assign_id' => $lecture_video_assign->id ], $request->faculty_id,
                [ 'lecture_video_assign_id' => $lecture_video_assign->id,'lecture_video_id' =>  $lecture_video_assign->lecture_video_id, 'faculty_id' => '@value@' ]
            );
        }

        if( $institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

            //dd( $request->subject_id );
            $this->save_relation(
                LectureVideoDiscipline::class,
                [ 'lecture_video_assign_id' => $lecture_video_assign->id ],
                $request->subject_id,
                [ 'lecture_video_assign_id' => $lecture_video_assign->id, 'lecture_video_id' => $lecture_video_assign->lecture_video_id, 'subject_id' => '@value@' ]
            );
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
        $lecture_video_assign=LectureVideoAssign::select('lecture_video_assigns.*')->find($id);
        return view('admin.lecture_video_assign.show',['lecture_video_assign'=>$lecture_video_assign]);
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
        $lecture_video_assign = LectureVideoAssign::find($id);
        $data['lecture_video_assign'] = LectureVideoAssign::find($id);
        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['lecture_videos'] = LectureVideo::pluck('name', 'id');
        
        $institute = Institutes::where('id',$lecture_video_assign->institute_id)->first();
        if($institute)$institute_type = $institute->type;
        else $institute_type = null;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties':'courses-subjects';
        $data['institute_type']= $institute_type;

        $course = Courses::find( $lecture_video_assign->course_id );
        $data['courses'] = Courses::where('institute_id',$lecture_video_assign->institute_id)->pluck('name', 'id');


        $is_combined = $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID;



        if( $data['institute_type'] == 1 ){


            $data['faculties'] = Faculty::where( 'course_id', $lecture_video_assign->course_id )->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id', $lecture_video_assign->faculty_id )->pluck('name', 'id');

            if( $is_combined ) {
                $data[ 'faculties' ] = $course->combined_faculties()->pluck('name', 'id');
            }


            $lecture_video_assign_faculties = LectureVideoFaculty::where('lecture_video_assign_id',$id)->get();
            $selected_faculties = array();
            foreach($lecture_video_assign_faculties as $faculty)
            {
                $selected_faculties[] = $faculty->faculty_id;
            }

            $data['selected_faculties'] = collect($selected_faculties);

        }

        if( $data['institute_type'] == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ){

            $data['subjects'] = Subjects::where('course_id',$lecture_video_assign->course_id)->pluck('name', 'id');

            if( $is_combined ) {
                $data[ 'subjects' ] = $course->combined_disciplines()->pluck('name', 'id');
            }

            $lecture_video_assign_disciplines = LectureVideoDiscipline::where('lecture_video_assign_id',$id)->get();

            //dd( $lecture_video_assign_disciplines );

            $selected_subjects = array();
            foreach($lecture_video_assign_disciplines as $lecture_video_assign_discipline)
            {
                $selected_subjects[] = $lecture_video_assign_discipline->subject_id;
            }

            $data['selected_subjects'] = collect($selected_subjects);
        }

        $data['module_name'] = 'Lecture Video';
        $data['title'] = 'Lecture Video Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.lecture_video_assign.edit', $data);
        
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
            'lecture_video_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
        
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\LectureVideoAssignController@edit',[$id])->withInput();
        }

        $lecture_video_assign = LectureVideoAssign::find($id);

        if($lecture_video_assign->lecture_video_id != $request->lecture_video_id || $lecture_video_assign->institute_id != $request->institute_id || $lecture_video_assign->course_id != $request->course_id) {

            if (LectureVideoAssign::where(['lecture_video_id'=>$request->lecture_video_id,'course_id'=>$request->course_id])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This lecture video assignment already exists');
                return redirect()->action('Admin\LectureVideoAssignController@edit',[$id])->withInput();
            }

        }

        $lecture_video_assign->lecture_video_id = $request->lecture_video_id;
        $lecture_video_assign->institute_id = $request->institute_id;
        $lecture_video_assign->course_id = $request->course_id;
        $lecture_video_assign->status=$request->status;
        $lecture_video_assign->updated_by=Auth::id();
        $lecture_video_assign->push();

        $this->save_subject_faculty_relation( $lecture_video_assign, $request );

//        $institute = Institutes::where('id',$request->institute_id)->first();
//        if($institute->type == 1)
//        {
//            if (LectureVideoFaculty::where('lecture_video_assign_id', $lecture_video_assign->id)->first()) {
//                LectureVideoFaculty::where('lecture_video_assign_id', $lecture_video_assign->id)->delete();
//            }
//
//            if($request->faculty_id)
//            {
//                foreach ($request->faculty_id as $key => $value) {
//                    if($value=='')continue;
//                    LectureVideoFaculty::insert(['lecture_video_assign_id' => $lecture_video_assign->id,'lecture_video_id' => $lecture_video_assign->lecture_video_id, 'faculty_id' => $value]);
//                }
//            }
//
//        }
//        else
//        {
//
//            if (LectureVideoDiscipline::where('lecture_video_assign_id', $lecture_video_assign->id)->first()) {
//                LectureVideoDiscipline::where('lecture_video_assign_id', $lecture_video_assign->id)->delete();
//            }
//
//            if($request->subject_id)
//            {
//                foreach ($request->subject_id as $key => $value) {
//                    if($value=='')continue;
//                    LectureVideoDiscipline::insert(['lecture_video_assign_id' => $lecture_video_assign->id,'lecture_video_id' => $lecture_video_assign->lecture_video_id, 'subject_id' => $value]);
//                }
//            }
//
//        }

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
        $lecture_video_assign = LectureVideoAssign::find($id);
        $lecture_video_assign->deleted_by = Auth::id();
        $lecture_video_assign->push();

        LectureVideoAssign::destroy($id); // 1 way
        if (LectureVideoFaculty::where('lecture_video_assign_id', $id)->first()) {
            LectureVideoFaculty::where('lecture_video_assign_id', $id)->delete();
        }
        if (LectureVideoDiscipline::where('lecture_video_assign_id', $id)->first()) {
            LectureVideoDiscipline::where('lecture_video_assign_id', $id)->delete();
        }
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureVideoAssignController@index');

        
    }

    public function download_emails($id){

        $lecture_video_assign = LectureVideoAssign::find($id);
        $emails = array();

        foreach($lecture_video_assign->batches as $batch){
            unset($lecture_video_assign_batch_id);
            $lecture_link = LectureVideoAssignLink::where('id',$batch->lecture_video_assign_batch_id)->get()[0];
            unset($doctors_courses);
            $doctors_courses = DoctorsCourses::where(['year'=>$lecture_link->year,'session_id'=>$lecture_link->session_id,'institute_id'=>$lecture_link->institute_id,'course_id'=>$lecture_link->course_id,'batch_id'=>$lecture_link->batch_id])->get();
            
            foreach($doctors_courses as $doctor_course){
                $emails[] = $doctor_course->doctor->email;
            }
        }

        $content = implode(',',$emails);
        $file_name = $lecture_video_assign->name.'.csv';
        $headers = [
                        'Content-type'        => 'text/csv',
                        'Content-Disposition' => 'attachment; filename='.$file_name,
                ];
            
        return Response::make($content, 200, $headers);
        
    }
}  