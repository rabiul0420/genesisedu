<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\LectureSheetTopicAssignLink;
use App\LectureSheetTopic;
use App\LectureSheetTopicAssign;
use App\LectureSheetTopicDiscipline;
use App\LectureSheetTopicFaculty;
use App\LectureSheetTopicBatchLectureSheetTopic;
use App\Providers\AppServiceProvider;
use App\Sessions;
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


class LectureSheetTopicAssignController extends Controller
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
        $data['lecture_sheet_topic_assigns'] = LectureSheetTopicAssign::get();
        $data['module_name'] = 'Lecture Sheet Folder Assign';
        $data['title'] = 'Lecture Sheet Folder Assign List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.lecture_sheet_topic_assign.list',$data);
                
        //echo $Institutes;
        //echo $title;
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

        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['lecture_sheet_topics'] = LectureSheetTopic::pluck('name', 'id');

        $data['module_name'] = 'Lecture Sheet Folder Assign';
        $data['title'] = 'Lecture Sheet Folder Assign Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_sheet_topic_assign.create',$data);
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
            'lecture_sheet_topic_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],    
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\LectureSheetTopicAssignController@create')->withInput();
        }        

        if (LectureSheetTopicAssign::where(['lecture_sheet_topic_id'=>$request->lecture_sheet_topic_id,'institute_id'=>$request->institute_id])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This lecture sheet topic assignment already exists');
            return redirect()->action('Admin\LectureSheetTopicAssignController@create')->withInput();
        }
        else{

            $lecture_sheet_topic_assign = new LectureSheetTopicAssign();
            $lecture_sheet_topic_assign->lecture_sheet_topic_id = $request->lecture_sheet_topic_id;
            $lecture_sheet_topic_assign->institute_id = $request->institute_id;
            $lecture_sheet_topic_assign->course_id = $request->course_id;
            $lecture_sheet_topic_assign->status=$request->status;
            $lecture_sheet_topic_assign->created_by=Auth::id();
            $lecture_sheet_topic_assign->save();

            $this->save_subject_faculty_relation( $lecture_sheet_topic_assign, $request );

//            $institute = Institutes::where('id',$request->institute_id)->first();
//            if($institute->type == 1)
//            {
//
//                if($request->faculty_id)
//                {
//                    if (LectureSheetTopicFaculty::where('lecture_sheet_topic_assign_id', $lecture_sheet_topic_assign->id)->first()) {
//                        LectureSheetTopicFaculty::where('lecture_sheet_topic_assign_id', $lecture_sheet_topic_assign->id)->delete();
//                    }
//
//                    foreach ($request->faculty_id as $key => $value) {
//                        if($value=='')continue;
//                        LectureSheetTopicFaculty::insert(['lecture_sheet_topic_assign_id' => $lecture_sheet_topic_assign->id,'lecture_sheet_topic_id' => $lecture_sheet_topic_assign->lecture_sheet_topic_id, 'faculty_id' => $value]);
//                    }
//                }
//
//            }
//            else
//            {
//
//                if($request->subject_id)
//                {
//                    if (LectureSheetTopicDiscipline::where('lecture_sheet_topic_assign_id', $lecture_sheet_topic_assign->id)->first()) {
//                        LectureSheetTopicDiscipline::where('lecture_sheet_topic_assign_id', $lecture_sheet_topic_assign->id)->delete();
//                    }
//
//                    foreach ($request->subject_id as $key => $value) {
//                        if($value=='')continue;
//                        LectureSheetTopicDiscipline::insert(['lecture_sheet_topic_assign_id' => $lecture_sheet_topic_assign->id,'lecture_sheet_topic_id' => $lecture_sheet_topic_assign->lecture_sheet_topic_id, 'subject_id' => $value]);
//                    }
//                }
//
//            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureSheetTopicAssignController@index');
        }
    }

    function save_subject_faculty_relation( LectureSheetTopicAssign $lecture_sheet_topic_assign, Request $request){
        $institute = Institutes::where( 'id', $request->institute_id )->first( );


        if( $institute->type == 1 ) {
            $this->save_relation(
                LectureSheetTopicFaculty::class,
                [ 'lecture_sheet_topic_assign_id' => $lecture_sheet_topic_assign->id ], $request->faculty_id,
                ['lecture_sheet_topic_assign_id' => $lecture_sheet_topic_assign->id,'lecture_sheet_topic_id' => $lecture_sheet_topic_assign->lecture_sheet_topic_id, 'faculty_id' => '@value@']
            );
        }

        if( $institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

            $this->save_relation(LectureSheetTopicDiscipline::class,[ 'lecture_sheet_topic_assign_id' => $lecture_sheet_topic_assign->id ], $request->subject_id,[ 'lecture_sheet_topic_assign_id' => $lecture_sheet_topic_assign->id,'lecture_sheet_topic_id' => $lecture_sheet_topic_assign->lecture_sheet_topic_id, 'subject_id' => '@value@' ]
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
        $lecture_sheet_topic_assign=LectureSheetTopicAssign::select('lecture_sheet_topic_assigns.*')->find($id);
        return view('admin.lecture_sheet_topic_assign.show',['lecture_sheet_topic_assign'=>$lecture_sheet_topic_assign]);
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
        $lecture_sheet_topic_assign = LectureSheetTopicAssign::find($id);
        $data['lecture_sheet_topic_assign'] = LectureSheetTopicAssign::find($id);
        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['lecture_sheet_topics'] = LectureSheetTopic::pluck('name', 'id');
        
        $institute = Institutes::where('id',$lecture_sheet_topic_assign->institute_id)->first();
        if($institute)$institute_type = $institute->type;
        else $institute_type = null;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties':'courses-subjects';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$lecture_sheet_topic_assign->institute_id)->pluck('name', 'id');

        $course = Courses::find( $lecture_sheet_topic_assign->course_id );
        $is_combined = $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID;

        if( $data['institute_type'] == 1 ){
            $data['faculties'] = Faculty::where('course_id',$lecture_sheet_topic_assign->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$lecture_sheet_topic_assign->faculty_id)->pluck('name', 'id');

            if( $is_combined ) {
                $data[ 'faculties' ] = $course->combined_faculties()->pluck('name', 'id');
            }

            $lecture_sheet_topic_assign_faculties = LectureSheetTopicFaculty::where('lecture_sheet_topic_assign_id',$id)->get();
            $selected_faculties = array();
            foreach($lecture_sheet_topic_assign_faculties as $faculty)
            {
                $selected_faculties[] = $faculty->faculty_id;
            }

            $data['selected_faculties'] = collect($selected_faculties);
        }

        if( $data['institute_type'] == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ){
            $data['subjects'] = Subjects::where('course_id',$lecture_sheet_topic_assign->course_id)->pluck('name', 'id');

            if( $is_combined ) {
                $data[ 'subjects' ] = $course->combined_disciplines()->pluck('name', 'id');
            }

            $lecture_sheet_topic_assign_disciplines = LectureSheetTopicDiscipline::where('lecture_sheet_topic_assign_id',$id)->get();
            $selected_subjects = array();
            foreach($lecture_sheet_topic_assign_disciplines as $lecture_sheet_topic_assign_discipline)
            {
                $selected_subjects[] = $lecture_sheet_topic_assign_discipline->subject_id;
            }

            $data['selected_subjects'] = collect($selected_subjects);
        }

        $data['module_name'] = 'Lecture Sheet Folder';
        $data['title'] = 'Lecture Sheet Folder Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.lecture_sheet_topic_assign.edit', $data);
        
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
            'lecture_sheet_topic_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
        
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\LectureSheetTopicAssignController@edit',[$id])->withInput();
        }

        $lecture_sheet_topic_assign = LectureSheetTopicAssign::find($id);

        if($lecture_sheet_topic_assign->lecture_sheet_topic_id != $request->lecture_sheet_topic_id || $lecture_sheet_topic_assign->institute_id != $request->institute_id || $lecture_sheet_topic_assign->course_id != $request->course_id) {

            if (LectureSheetTopicAssign::where(['lecture_sheet_topic_id'=>$request->lecture_sheet_topic_id,'institute_id'=>$request->institute_id])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This lecture sheet topic assignment already exists');
                return redirect()->action('Admin\LectureSheetTopicAssignController@edit',[$id])->withInput();
            }

        }

        $lecture_sheet_topic_assign->lecture_sheet_topic_id = $request->lecture_sheet_topic_id;
        $lecture_sheet_topic_assign->institute_id = $request->institute_id;
        $lecture_sheet_topic_assign->course_id = $request->course_id;
        $lecture_sheet_topic_assign->status=$request->status;
        $lecture_sheet_topic_assign->updated_by=Auth::id();
        $lecture_sheet_topic_assign->push();

        $this->save_subject_faculty_relation( $lecture_sheet_topic_assign, $request );

//        $institute = Institutes::where('id',$request->institute_id)->first();
//        if($institute->type == 1)
//        {
//
//            if($request->faculty_id)
//            {
//                if (LectureSheetTopicFaculty::where('lecture_sheet_topic_assign_id', $lecture_sheet_topic_assign->id)->first()) {
//                    LectureSheetTopicFaculty::where('lecture_sheet_topic_assign_id', $lecture_sheet_topic_assign->id)->delete();
//                }
//
//                foreach ($request->faculty_id as $key => $value) {
//                    if($value=='')continue;
//                    LectureSheetTopicFaculty::insert(['lecture_sheet_topic_assign_id' => $lecture_sheet_topic_assign->id,'lecture_sheet_topic_id' => $lecture_sheet_topic_assign->lecture_sheet_topic_id, 'faculty_id' => $value]);
//                }
//            }
//
//        }
//        else
//        {
//
//            if($request->subject_id)
//            {
//                if (LectureSheetTopicDiscipline::where('lecture_sheet_topic_assign_id', $lecture_sheet_topic_assign->id)->first()) {
//                    LectureSheetTopicDiscipline::where('lecture_sheet_topic_assign_id', $lecture_sheet_topic_assign->id)->delete();
//                }
//
//                foreach ($request->subject_id as $key => $value) {
//                    if($value=='')continue;
//                    LectureSheetTopicDiscipline::insert(['lecture_sheet_topic_assign_id' => $lecture_sheet_topic_assign->id,'lecture_sheet_topic_id' => $lecture_sheet_topic_assign->lecture_sheet_topic_id, 'subject_id' => $value]);
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
        if (LectureSheetTopicFaculty::where('lecture_sheet_topic_assign_id', $id)->first()) {
            $lecturesheet_topic_faculty = LectureSheetTopicFaculty::find($id);
            $lecturesheet_topic_faculty->deleted_by = Auth::id();
            $lecturesheet_topic_faculty->push();
            LectureSheetTopicAssign::destroy($id);
            // LectureSheetTopicFaculty::where('lecture_sheet_topic_assign_id', $id)->delete();
        }
        if ( $lecturesheet_topic_diciplain = LectureSheetTopicDiscipline::where('lecture_sheet_topic_assign_id', $id)->first()) {
            $lecturesheet_topic_diciplain->deleted_by = Auth::id();
            $lecturesheet_topic_diciplain->push();
            LectureSheetTopicAssign::destroy($id);
            // LectureSheetTopicDiscipline::where('lecture_sheet_topic_assign_id', $id)->delete();
        }
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureSheetTopicAssignController@index');
    }

    public function download_emails($id){

        $lecture_sheet_topic_assign = LectureSheetTopicAssign::find($id);
        $emails = array();

        foreach($lecture_sheet_topic_assign->batches as $batch){
            unset($lecture_sheet_topic_assign_batch_id);
            $lecture_link = LectureSheetTopicAssignLink::where('id',$batch->lecture_sheet_topic_assign_batch_id)->get()[0];
            unset($doctors_courses);
            $doctors_courses = DoctorsCourses::where(['year'=>$lecture_link->year,'session_id'=>$lecture_link->session_id,'institute_id'=>$lecture_link->institute_id,'course_id'=>$lecture_link->course_id,'batch_id'=>$lecture_link->batch_id])->get();
            
            foreach($doctors_courses as $doctor_course){
                $emails[] = $doctor_course->doctor->email;
            }
        }

        $content = implode(',',$emails);
        $file_name = $lecture_sheet_topic_assign->name.'.csv';
        $headers = [
                        'Content-type'        => 'text/csv',
                        'Content-Disposition' => 'attachment; filename='.$file_name,
                ];
            
        return Response::make($content, 200, $headers);
        
    }
}  