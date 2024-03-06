<?php

namespace App\Http\Controllers;

use App\Batches;
use App\BatchesSchedules;
use App\BatchesSchedulesBatches;
use App\BatchesSchedulesFaculties;
use App\BatchesSchedulesLecturesExams;
use App\DoctorCourseScheduleDetails;
use App\BatchesSchedulesMeta;
use App\BatchesSchedulesSubjects;
use App\BatchSchedules;
use App\DoctorClassRating;
use App\Exam;
use App\Exam_question;

use App\OnlineLectureAddress;
use App\OnlineLectureLink;
use App\OnlineExamCommonCode;
use App\Page;
use App\Providers\AppServiceProvider;
use App\QuestionTypes;
use App\Result;
use App\Role;
use App\ScheduleDetail;
use App\ScheduleTimeSlot;
use App\Sessions;
use App\Subjects;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Doctors;
use App\Courses;
use App\Topics;
use App\DoctorsCourses;
use App\Faculty;
use App\Http\Resources\BatchesSchedulesResource;
use App\OnlineExam;
use App\OnlineExamLink;
use App\OnlineExamTopics;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;
use App\Notices;
use Validator;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use function App\Http\Helpers\view_healpers\highlight_filter_text;


class BatchScheduleController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:doctor', ['except' => ['view_batch_schedule','full_schedule'] ]);
    }

    /**
     *
     */
    public function master_schedules( ){
        $data['combined_schedule'] = false;
        $doctor_id = Auth::guard('doctor')->id( );
        $schedules = $this->master_schedules_query( );//echo "<pre>";print_r($schedules);exit;
        // return ([ 'schedules' => $schedules ] );
        foreach($schedules as $schedule)
        {
            
            $doctor_course = DoctorsCourses::where(['doctor_id'=>$doctor_id,'year'=>$schedule->year,'session_id'=>$schedule->session_id,'course_id'=>$schedule->course_id,'batch_id'=>$schedule->batch_id,'is_trash'=>'0','status'=>'1'])->first();
            //echo "<pre>";print_r($doctor_course);exit;
            if(isset($doctor_course))
            {
                $schedule->doctor_course_id = $doctor_course->id;
                $schedule->doctor_course_system_driven = $doctor_course->system_driven;
            }

            if($schedule->institute->id == 16)
            {
                $data['combined_schedule'] = true;
            }

        }

        $data['schedules'] = $schedules;
        
        return view('batch_schedule/master_schedule_batches',$data );
    }

    protected function master_schedules_query( ){

        $doctor_id = Auth::guard('doctor')->id( );

        $courses_where = [
            'doctors_courses.doctor_id' => $doctor_id,
            'doctors_courses.status' => '1',
            'doctors_courses.is_trash' => '0',
            'doctors_courses.payment_status' => 'Completed',
        ];

        $batch_ids = array();
        $doctor = Doctors::where('id',$doctor_id)->first();
        $doctor_courses = $doctor->doctorcourses;
        $custom_doctor_courses = array();
        if(isset($doctor_courses) && count($doctor_courses))
        {
            foreach($doctor_courses as $doctor_course)
            {
                
                if($doctor_course->status == '1' && $doctor_course->is_trash == '0' )
                {
                    if(isset($doctor_course->batch->fee_type))
                    {
                        $doctor_course->set_payment_status();
                    }
                    
                    if(($doctor_course->payment_status == "Completed" || $doctor_course->payment_status == "In Progress") && $doctor_course->eligibility())
                    {
                        $batch_ids[] = $doctor_course->batch_id;
                        $custom_doctor_courses[] = $doctor_course;
                    }
                }
                
            }

        }

        $schedule_ids = array();
        $schedule_ids = BatchesSchedules::whereIn('batch_id',$batch_ids)->whereNull('deleted_at')->pluck('id');
        if(isset($schedule_ids) && count($schedule_ids))
        {
            $batches_schedules = BatchesSchedules::with('batch','course')
            ->whereHas('batch', function($q){
                $q->where('status', '!=' , 0);
            })
            ->whereIn('batches_schedules.id',$schedule_ids->all())->whereNull('batches_schedules.deleted_at')->get();
        }

        $schedule_ids = array();
        if(isset($custom_doctor_courses) && count($custom_doctor_courses))
        {
            foreach($custom_doctor_courses as $doctor_course)
            {

                if(isset($doctor_course->batch) && $doctor_course->batch->fee_type == "Batch")
                {
                    if($doctor_course->institute->id == "16")
                    {

                        foreach($batches_schedules as $batch_schedule)
                        {
                            if(( $doctor_course->year == $batch_schedule->year && $doctor_course->course_id == $batch_schedule->course_id && $doctor_course->session_id == $batch_schedule->session_id && $doctor_course->batch_id == $batch_schedule->batch_id ) && ($doctor_course->faculty_id == $batch_schedule->faculty_id) && ($doctor_course->bcps_subject_id == $batch_schedule->bcps_subject_id))
                            {
                                $schedule_ids[] = $batch_schedule->id;

                            }
                        }

                    }
                    else
                    {
                        foreach($batches_schedules as $batch_schedule)
                        {
                            if(( $doctor_course->year == $batch_schedule->year && $doctor_course->course_id == $batch_schedule->course_id && $doctor_course->session_id == $batch_schedule->session_id && $doctor_course->batch_id == $batch_schedule->batch_id ) && $doctor_course->batch_id == $batch_schedule->batch_id)
                            {
                                $schedule_ids[] = $batch_schedule->id;

                            }
                        }
                    }

                }
                else if(isset($doctor_course->batch) && $doctor_course->batch->fee_type == "Discipline_Or_Faculty")
                {
                    if($doctor_course->institute->type == "1")
                    {

                        if($doctor_course->institute->id == "16")
                        {
                            foreach($batches_schedules as $batch_schedule)
                            {
                                if(( $doctor_course->year == $batch_schedule->year && $doctor_course->course_id == $batch_schedule->course_id && $doctor_course->session_id == $batch_schedule->session_id && $doctor_course->batch_id == $batch_schedule->batch_id ) && ($doctor_course->faculty_id == $batch_schedule->faculty_id) && ($doctor_course->bcps_subject_id == $batch_schedule->bcps_subject_id))
                                {
                                    $schedule_ids[] = $batch_schedule->id;

                                }
                            }

                        }
                        else
                        {
                            foreach($batches_schedules as $batch_schedule)
                            {
                                if(( $doctor_course->year == $batch_schedule->year && $doctor_course->course_id == $batch_schedule->course_id && $doctor_course->session_id == $batch_schedule->session_id && $doctor_course->batch_id == $batch_schedule->batch_id ) &&  $doctor_course->faculty_id == $batch_schedule->faculty_id)
                                {
                                    $schedule_ids[] = $batch_schedule->id;

                                }
                            }
                        }

                    }
                    else if($doctor_course->institute->type == "0")
                    {
                        foreach($batches_schedules as $batch_schedule)
                        {
                                if(( $doctor_course->year == $batch_schedule->year && $doctor_course->course_id == $batch_schedule->course_id && $doctor_course->session_id == $batch_schedule->session_id && $doctor_course->batch_id == $batch_schedule->batch_id ) &&  $doctor_course->subject_id == $batch_schedule->subject_id)
                            {
                                $schedule_ids[] = $batch_schedule->id;

                            }
                        }
                    }
                }
            }
        }

        if(isset($schedule_ids) && count($schedule_ids))
        {
            // return
            $batches_schedules = BatchesSchedules::with('batch','course')
            ->whereIn('batches_schedules.id',$schedule_ids)
            ->whereHas('batch', function($q){
                $q->where('status', true);
            })
            ->whereNull('batches_schedules.deleted_at')->get();
        }

        if($batches_schedules ?? false) {
            $inactive_batches = $batches_schedules->where('batch.expired_at', '!=', Null)->where('batch.expired_at','<',date("Y-m-d"))->pluck(['batch_id']) ?? [];
            DB::table('batches')
            ->whereIn('id', $inactive_batches)
            ->update(array('status' => false));
        }

        return 
            ($batches_schedules ?? null)
            ? $batches_schedules->whereNotIn('batch_id',$inactive_batches) ?? []
            : [];

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function batch_schedules( )
    {

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where(['doctor_id' => $doc_info->id,'status'=>'1', 'is_trash' => '0'])->get();

        $data['doctor_courses'] = $doctor_courses;

        $doctor_courses_of_batch_schedules = array();

        foreach( $doctor_courses as $key => $doctor_course ){

            $batchSchedulesQuery = $this->batchSchedulesQuery( $doctor_course );

            if( $batchSchedulesQuery->exists( ) ){
                $doctor_courses_of_batch_schedules[] = $doctor_course;
            }

        }

        $data['doctor_courses'] = $doctor_courses_of_batch_schedules;

        $data[ 'updated_schedules' ] = $this->master_schedules_query( )->get( );;

        return view('batch_schedule/lecture_topics',$data );



    }

    public function master_schedule_list(){
        $doctor = Doctors::with('active_doctor_courses.batch', 'active_doctor_courses.institute')
            ->where('id', Auth::guard('doctor')->id() )->first();

        return
        $doctor_courses = $doctor->active_doctor_courses;

        $data['doc_info'] = $doctor;
        $data['doctor_courses'] = $doctor_courses;

        $doctor_course_ids = Collection::make([]);

        foreach ( $doctor_courses as $doctor_course ) {

            $batch = $doctor_course->batch;

            if( $batch->fee_type == "Batch" ) {
                $doctor_course_ids->push( $batch->id );
            }
            else if( $batch->fee_type == "Discipline_Or_Faculty"){

                if( $doctor_course->institute->type == 1 ) {

                } else {

                }
            }

        }

        foreach( $doctor_courses as $key => $doctor_course ){
            $batchSchedulesQuery = $this->batchSchedulesQuery( $doctor_course );
            if( $batchSchedulesQuery->exists( ) ){
                $schedule_data = $batchSchedulesQuery->pluck(  'id' );
                $batch_schedule_ids = empty( $batch_schedule_ids ) ? $schedule_data : $batch_schedule_ids->merge( $schedule_data );
            }
        }

    }


    private function batchSchedulesQuery(DoctorsCourses $doctor_course){
        $query = BatchesSchedules::where(
            [
                'year'=> $doctor_course->year,
                'session_id'=>$doctor_course->session_id,
                'institute_id'=>$doctor_course->institute_id,
                'course_id'=>$doctor_course->course_id,
            ]
        )->whereIn( 'id', BatchesSchedulesBatches::where([ 'batch_id' => $doctor_course->batch_id ])
            ->whereNull('deleted_at')->select( 'batch_schedule_id' ) );

        return $query;
    }




    public function doctor_course_batch_schedule($doctor_course_id)
    {


        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where(['id'=>$doctor_course_id,'is_trash'=>'0'])->get();

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $data['doctor_course'] = $doctor_courses[0];
        $batch_schedule_ids = array();

        foreach( $doctor_courses as $key => $doctor_course ){
            $batchSchedulesQuery = $this->batchSchedulesQuery( $doctor_course );
            if( $batchSchedulesQuery->exists( ) ){
                $schedule_data = $batchSchedulesQuery->pluck(  'id' );
                $batch_schedule_ids = empty( $batch_schedule_ids ) ? $schedule_data : $batch_schedule_ids->merge( $schedule_data );
            }
        }

        if( $doctor_courses[0]->batch->fee_type == "Batch" )
        {
            $batch_schedule_batch = BatchesSchedules::whereIn( 'batches_schedules.id', $batch_schedule_ids )
                ->orderBy('batches_schedules.id','desc');

            if( $doctor_courses[0]->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

                $batch_schedule_batch = $batch_schedule_batch
                    ->select( 'batches_schedules.*' )
                    ->where( function ( $query ) use( $doctor_courses ){
                        $query->whereExists( function ( $q ) use ( $doctor_courses ){
                            $q->select('*')->from( 'batches_schedules_subjects' )->whereRaw('schedule_id = batches_schedules.id AND `subject_id` = '. ( $doctor_courses[0]->bcps_subject_id ?? 0 ).' AND deleted_at IS NULL');
                        })
                        ->orWhereExists(function ($q) use ( $doctor_courses ){
                            $q->select('*')->from( 'batches_schedules_faculties' )->whereRaw('schedule_id = batches_schedules.id AND `faculty_id` = '.( $doctor_courses[0]->faculty_id ?? 0 ).'  AND deleted_at IS NULL');
                        });
                    })
                    ->orderBy( 'batches_schedules.id', 'desc' )
                    ->groupBy( 'batches_schedules.id' )
                    ->paginate( 10 );

            } else {
                $batch_schedule_batch = $batch_schedule_batch->paginate( 10 );
            }

        }
        else if($doctor_courses[0]->batch->fee_type == "Discipline_Or_Faculty")
        {

            if($doctor_courses[0]->institute->type == 1)
            {
                $batch_schedule_batch = BatchesSchedules::select('batches_schedules.*')
                    ->join('batches_schedules_faculties','batches_schedules_faculties.schedule_id','batches_schedules.id')
                    ->join('faculties','faculties.id','batches_schedules_faculties.faculty_id')
                    ->whereIn('batches_schedules.id',$batch_schedule_ids)
                    ->where('batches_schedules_faculties.faculty_id',$doctor_courses[0]->faculty_id)
                    ->whereNull( 'batches_schedules_faculties.deleted_at')
                    ->orderBy('batches_schedules.id','desc')
                    ->paginate(10);

            }
            else
            {
                $batch_schedule_batch = BatchesSchedules::select('batches_schedules.*')
                    ->join('batches_schedules_subjects','batches_schedules_subjects.schedule_id','batches_schedules.id')
                    ->join('subjects','subjects.id','batches_schedules_subjects.subject_id')
                    ->whereIn('batches_schedules.id',$batch_schedule_ids)
                    ->where('batches_schedules_subjects.subject_id',$doctor_courses[0]->subject_id)
                    ->whereNull( 'batches_schedules_subjects.deleted_at')
                    ->orderBy('batches_schedules.id','desc')
                    ->paginate(10);

            }
        }

        $data['batch_schedule_batch'] = $batch_schedule_batch;
        $doctor_courses_of_batch_schedules = [];

        foreach($doctor_courses as $key=>$doctor_course){
            $batchSchedulesQuery = $this->batchSchedulesQuery( $doctor_course );
            if( $batchSchedulesQuery->exists( ) ){
                $doctor_courses_of_batch_schedules[] = $doctor_course;
            }
        }


        $data['doctor_courses'] = $doctor_courses_of_batch_schedules;

        return view('batch_schedule/lecture_topics',$data);

    }



    protected function _schedule_time_slot( $doctor_course_id ){
        ScheduleDetail::$_doctor_course_id = $doctor_course_id;

        $time_slots = ScheduleTimeSlot::with([
            'schedule_details' => function( $detail ){
                return $detail->where( 'parent_id', 0 )->orderBy( 'priority' );
            },
            'schedule_details.time_slot',
            'schedule_details.doctor_class_view',
            'schedule_details.lectures.doctor_class_view',
            'schedule_details.lectures.video',
            'schedule_details.lectures.exam',
            'schedule_details.lectures.mentor',
            'schedule_details.lectures.parentClass',
            'schedule_details.video',
            'schedule_details.exam',
            'schedule_details.mentor',
            'schedule_details.doctor_exam',
        ]);


        $search_text = $_GET['text'] ?? '';

        if( $search_text ) {

            $time_slots->whereExists(function ( $query ){

                $query->from( 'schedule_details' )->whereRaw( 'slot_id = schedule_time_slots.id' );

                $query->where( function ($query){

                    //class name filtering
                    $query->where( function ( $query ){
                        $query->where( 'type', 'Class' );
                        $query->whereExists( function ( $query ){
                            $search_text = $_GET['text'] ?? '';
                            $query->from( "lecture_video" )
                                ->whereRaw( "id = schedule_details.class_or_exam_id" )
                                ->where( "name", "LIKE", "%{$search_text}%" );
                        });
                    });


                    //exam name filtering
                    $query->orWhere( function ( $query ){
                        $query->where( 'type', 'Exam' );
                        $query->whereExists( function ( $query ){
                            $search_text = $_GET['text'] ?? '';
                            $query->from( "exam" )
                                ->whereRaw( "id = schedule_details.class_or_exam_id" )
                                ->where( "name", "LIKE", "%{$search_text}%" );
                        });
                    });

                    //mentor name filtering
                    $query->orWhereExists( function ( $query ){
                        $search_text = $_GET['text'] ?? '';
                        $query->from( "teacher" )
                            ->whereRaw( "id = schedule_details.mentor_id" )
                            ->where( "name", "LIKE", "%{$search_text}%" );

                    });

                });

            });
        }


        return $time_slots;
    }


    public function new_schedule_single(  $slot_id, $doctor_course_id ){
        $slot = $this->_schedule_time_slot( $doctor_course_id );
        $time_slot = $slot->where('id', $slot_id )->first();
        $schedule_id = $time_slot->schedule_id;
        $schedule = BatchesSchedules::find( $schedule_id );
        $course_id = $schedule->course_id;
        $batch_id = $schedule->course_id;

        return view('batch_schedule.time-slot',compact('time_slot','doctor_course_id','schedule_id','course_id','batch_id' ) );
    }

    public function view_batch_schedule( Request $request, $batch_id, $faculty_or_dicpline_id = null, $bcps_subject_id = null ){



        $batch = Batches::where('status' , 1)->find( $batch_id );



        if($batch == null){
            return "This facility temporarily closed. Please contact with admin.";
        }

        $where = [];


        $institute = $batch->institute;

        $institute_type = $institute->type ?? null;
        $isCombined = $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID;

        if( !$faculty_or_dicpline_id ) {

            if( $batch->fee_type == 'Discipline_Or_Faculty' || $isCombined ){

                $label = ( $institute_type == 1 ) ? 'faculty' : 'discipline';

                $faculties =  null;
                $disciplines =  null;

                if( $isCombined ) {

                    $course = new Courses();
                    $faculties = $course->combined_faculties()->active()->pluck('name','id');
                    $disciplines = $course->combined_disciplines()->active()->pluck('name','id');

                } else {
                    if ( $batch->fee_type == 'Discipline_Or_Faculty' ){

                        if ($label == 'faculty') {

                            $faculties = Faculty::where([
                                'status' => 1,
                                'institute_id' => $batch->institute_id,
                                'course_id' => $batch->course_id,
                            ])->pluck('name', 'id');

                        } else if ($label == 'discipline') {
                            $disciplines = Subjects::where([
                                'status' => 1,
                                'institute_id' => $batch->institute_id,
                                'course_id' => $batch->course_id,
                            ])->pluck('name', 'id');
                        }
                    }
                }


                return view(
                    'batch_schedule.faculty-discipline-selector',
                    compact(
                        'batch_id',
                        'institute_type', 'faculties', 'disciplines', 'label', 'batch_id', 'isCombined' )
                );
            }

        }

        if( $institute_type == 1 ) {
            $where['faculty_id'] = $faculty_or_dicpline_id;
            if( $isCombined ) {
                $where['bcps_subject_id'] = $bcps_subject_id;
            }
        } else {
            $where['subject_id'] = $faculty_or_dicpline_id;
        }

        $schedule = BatchesSchedules::where( 'batch_id', $batch->id )
            ->when( count($where ), function( $schedule, $count_where ) use ( $where ){
                $schedule->where( $where );
            })
            ->whereExists( function ($query){
                $query->select()->from('schedule_time_slots')->whereRaw( "schedule_id = batches_schedules.id" );
            })
            ->first( );

        if( !$schedule  ) {
            return  redirect()->back( )->with([
                'message' => 'No Schedule found' ,
                'class' => 'alert-warning',
                'section' => '#available-batches'
            ])->withInput([
                'faculty_or_discipline_id' => $faculty_or_dicpline_id ,
                'bcps_subject_id' => $bcps_subject_id ,
            ]);
        }

        return $this->full_schedule( $request, $schedule->id ?? ' ');
    }

    public function full_schedule( Request $request, $schedule_id ){

        //return $request->user();

        $schedule = BatchesSchedules::where( 'id', $schedule_id )->first( [ 'course_id', 'batch_id' ] );


        $course_id = $schedule->course_id ?? null;
        $batch_id = $schedule->batch_id ?? null;

        $doctor_course_id = DoctorsCourses::where( [ 'course_id' => $course_id, 'batch_id' => $batch_id ] )
            ->where( 'doctor_id', Auth::guard('doctor')->id() )->value('id');

        $search_date = $_GET['date'] ?? '';

        $scheduleTimeSlots = $this->_schedule_time_slot( $doctor_course_id );

        $scheduleTimeSlots->where('schedule_id', $schedule_id );

        if( $search_date ) {
            $scheduleTimeSlots->whereRaw("DATE(`datetime`) = '" . $search_date . "'" );
        }

        $scheduleTimeSlots = $scheduleTimeSlots->orderBy('datetime' )->paginate( 3 )->appends( $request->query() );

        $systemDriven = true;
        $batch =  Batches::where('id',$batch_id)->first();
        return view('full-schedule', compact('scheduleTimeSlots', 'doctor_course_id', 'course_id', 'systemDriven', 'schedule_id','batch' ) );
    }

    protected function schedule_and_doctor_course( $schedule_id, &$schedule, $doctor_course_with = [ ] ){
        $schedule = BatchesSchedules::where( 'id', $schedule_id )->first( [ 'course_id', 'batch_id' ] );

        $course_id = $schedule->course_id ?? null;
        $batch_id = $schedule->batch_id ?? null;

        $query = is_array($doctor_course_with ) && count($doctor_course_with)
                ? DoctorsCourses::with( $doctor_course_with ):DoctorsCourses::query();

        return $query->where( [
                'course_id' => $course_id,
                'batch_id' => $batch_id,
                'doctor_id' => Auth::guard('doctor')->id() ]
            )->first();
    }


    public function schedule_print( Request $request, $schedule_id ){

        $doctor_course = $this->schedule_and_doctor_course($schedule_id, $schedule,
            ['course','session', 'faculty', 'subject', 'bcps_subject', 'batch', 'branch' ] );

        $course_id = $doctor_course->course_id;
        $scheduleTimeSlots = $this->_schedule_time_slot( $doctor_course->id ?? '' );
        $scheduleTimeSlots->where( 'schedule_id', $schedule_id  );

        $search_date = $_GET['date'] ?? '';

        if( $search_date ) {
            $scheduleTimeSlots->whereRaw("DATE(`datetime`) = '" . $search_date . "'" );
        }

        $scheduleTimeSlots = $scheduleTimeSlots->orderBy('datetime' )->get( );

        $systemDriven = true;
        // return  compact('scheduleTimeSlots', 'doctor_course', 'course_id', 'systemDriven', 'schedule_id'  );
        return view('batch_schedule.print.print', compact('scheduleTimeSlots', 'doctor_course', 'course_id', 'systemDriven', 'schedule_id' ) );
    }
    private function schedule_data($id, $action = 'edit')
    {

        $relations = [

            'time_slots' => function ($slot) use ($action) {
                return $slot->whereNull('deleted_at')->orderBy('datetime');
            },

            'time_slots.schedule_details' => function ($details)  use ($action) {

                if ($action == 'duplicate') {

                    $details->select(
                        'type',
                        'class_or_exam_id',
                        'slot_id',
                        'mentor_id',
                        'priority',
                        'id',
                        'parent_id',
                        DB::raw('concat( "duplicate-", id ) as dup_id'),
                        DB::raw('concat( "duplicate-", parent_id ) as dup_parent_id')

                    );
                }

                return $details->orderBy('priority');
            },

            'meta' => function ($meta) {
                return $meta->where('key', 'fb_links');
            }
        ];

        if ($action == 'view') {

            $relations['time_slots.schedule_details'] = function ($detail) {
                return $detail->where('parent_id', 0)->orderBy('priority');
            };

            $relations[] =  'time_slots.schedule_details.exam.question_type';
            $relations[] =  'time_slots.schedule_details.lectures';
            $relations[] =  'time_slots.schedule_details.video';
            $relations[] =  'time_slots.schedule_details.mentor';
            $relations[] =  'time_slots.schedule_details.lectures.video';
            $relations[] =  'time_slots.schedule_details.lectures.mentor';
            $relations[] =  'room';
        }

        $data = BatchesSchedules::with($relations)->find($id);

        return $data;
    }

    public function schedule_print_table($id)
    {
        $data = [];
        $data['action'] = 'edit';
        $data['id'] = $id;
        // return 
        $batch_schedule = $this->schedule_data($id, 'view');
        $data['batch_schedule'] = new BatchesSchedulesResource($batch_schedule);
        // return $data;
        return view('batch_schedule.print.print_second', $data);
    }

    public function checkFistExam($scheduleTimeSlots)
    {
        $scheduleTimeSlots = $scheduleTimeSlots->orderBy('datetime')->get();

        foreach($scheduleTimeSlots as $scheduleTimeSlot) {
            foreach($scheduleTimeSlot->schedule_details as $schedule_detail) {
                if($schedule_detail->type == 'Exam') {
                    $exam = $schedule_detail->examLink();
                    if($exam && !$exam['completed']) {
                        return $exam["url"];
                    };
                }
            }
        }
    }

    public function new_schedule( Request $request, $schedule_id,$doctor_course_id ){
        //echo "<pre>";print_r($doctor_course_id);exit;
        $schedule = BatchesSchedules::with('meta')->where( 'id', $schedule_id )->first( ['id', 'course_id', 'batch_id', 'faculty_id', 'subject_id', 'bcps_subject_id'  ] );

        $schedule_links = null;
        if($schedule->meta){
            $schedule_links = $schedule->meta;
            $schedule_links=$schedule_links->where('key','fb_links')->first();
            $schedule_links = json_decode($schedule_links->value ?? null);
        }

        $request->session( )->put( '__schedule_back_link', $request->fullUrl( ) );


        $course_id = $schedule->course_id ?? null;
        $batch_id = $schedule->batch_id ?? null;

        // $doctor_course = DoctorsCourses::with('batch','institute', 'subject', 'faculty')
        //     ->where( [ 'course_id' => $course_id, 'batch_id' => $batch_id ] )
        //     ->where( 'doctor_id', Auth::guard('doctor')->id() )->first( );
        $doctor_course = DoctorsCourses::with('batch','institute', 'subject', 'faculty')->where(['doctors_courses.id'=>$request->doctor_course_id])->first( );
        
        $doctor_course_id = $doctor_course->id ?? '';

        $notAdmitted = false;

        if( $doctor_course ) {
            if( ( $doctor_course->batch->fee_type ?? '' )  == 'Discipline_Or_Faculty' ){
                if( $doctor_course->institute->type == 1 ){
                    if( $schedule->faculty_id != ($doctor_course->faculty_id ?? '') ) {
                        $notAdmitted = true;
                    }
                }else if( $schedule->subject_id != ($doctor_course->subject_id ?? '') ) {
                    $notAdmitted = true;
                }
            }
        }
        // } else {
        //     $notAdmitted = true;
        // }

        // if( $notAdmitted === true ) {
        //     Session::flash( 'message', 'To gain access please complete admission in the batch or if already admitted please check your faculty/discipline.' );
        //     return redirect( route('doctor_admission') );
        // }


        $search_date = $_GET['date'] ?? '';

        $scheduleTimeSlots = $this->_schedule_time_slot( $doctor_course_id );

        $scheduleTimeSlots->where('schedule_id', $schedule_id );

        if( $search_date ) {
            $scheduleTimeSlots->whereRaw("DATE(`datetime`) = '" . $search_date . "'" );
        }

        // previous exam mendatory
        $previous_exam_mendatory = Batches::where('id', $batch_id)->value('previous_exam_mendatory') ?? false;
        $only_available_exam = $this->checkFistExam($scheduleTimeSlots);

        $scheduleTimeSlots = $scheduleTimeSlots->orderBy('datetime' )->paginate( 3 )->appends( $request->query() );

        $systemDriven = true;

        if($schedule->batch->system_driven == "Mandatory" || ( $schedule->batch->system_driven == "Optional" && $doctor_course->system_driven == "Yes"))
        {
            if(isset($scheduleTimeSlots) && count($scheduleTimeSlots)>0)
            {
                $doctor_course_schedule_details = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$doctor_course_id,'schedule_id'=>$schedule_id])->orderBy('id','desc')->first();
                if(isset($doctor_course_schedule_details))
                {
                    if($doctor_course_schedule_details->feedback == "not_completed" || $doctor_course_schedule_details->feedback == "")
                    {
                        foreach($scheduleTimeSlots as $scheduleTimeSlot)
                        {

                            foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                            {

                                if($doctor_course_schedule_details->schedule_details_id == $schedule_details->id)
                                {
                                    $schedule_details->active_status = "active";
                                }
                                else
                                {
                                    $schedule_details->active_status = "inactive";
                                }

                            }

                        }

                    }
                    else if($doctor_course_schedule_details->feedback == "completed")
                    {
                        $today = Date('Ymd',time());$count=$completed=0;
                        foreach($scheduleTimeSlots as $scheduleTimeSlot)
                        {
                            foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                            {

                                $date = $schedule_details->time_slot->datetime->format('Ymd');
                                if($date==$today)
                                {
                                    $count++;
                                    $available_schedule = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$doctor_course_id,'schedule_id'=>$schedule_id,'schedule_details_id'=>$schedule_details->id])->first();
                                    if(isset($available_schedule))
                                    {
                                        if($available_schedule->feedback == "not_completed" || $available_schedule->feedback == "" )
                                        {
                                            $schedule_details->active_status = "active";
                                        }
                                        else if($available_schedule->feedback == "completed")
                                        {
                                            $completed++;
                                        }
                                    }
                                    else
                                    {
                                        $schedule_details->active_status = "active";
                                    }

                                }
                                else
                                {
                                    $schedule_details->active_status = "inactive";
                                }

                            }
                            if($count == $completed)
                            {
                                $today = Date('Ymd',time());
                                foreach($scheduleTimeSlots as $scheduleTimeSlot)
                                {
                                    foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                                    {
                                        $date = $schedule_details->time_slot->datetime->format('Ymd');
                                        if($date<=$today)
                                        {
                                            $available_schedule = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$doctor_course_id,'schedule_id'=>$schedule_id,'schedule_details_id'=>$schedule_details->id])->first();
                                            if(isset($available_schedule))
                                            {
                                                if($available_schedule->feedback == "not_completed" || $available_schedule->feedback == "" )
                                                {
                                                    $schedule_details->active_status = "active";
                                                }
                                            }
                                            else
                                            {
                                                $schedule_details->active_status = "active";
                                            }

                                        }
                                        else
                                        {
                                            $schedule_details->active_status = "inactive";
                                        }
                                    }
                                }

                            }

                        }

                    }

                }
                else
                {

                    $today = Date('Ymd',time());
                    foreach($scheduleTimeSlots as $scheduleTimeSlot)
                    {
                        foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                        {
                            $date = $schedule_details->time_slot->datetime->format('Ymd');
                            if($date<=$today)
                            {
                                $schedule_details->active_status = "active";
                            }
                            else
                            {
                                $schedule_details->active_status = "inactive";
                            }
                        }
                    }

                }

                // foreach($scheduleTimeSlots as $scheduleTimeSlot)
                // {
                //     foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                //     {

                //         $doctor_course_schedule_details = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$doctor_course_id,'schedule_details_id'=>$schedule_details->id])->first();
                //         if(isset($doctor_course_schedule_details))
                //         {
                //             if($doctor_course_schedule_details->feedback == "not_completed" || $doctor_course_schedule_details->feedback == "")
                //             {
                //                 $schedule_details->active_status = "active";
                //             }
                //             else if($doctor_course_schedule_details->feedback == "completed")
                //             {
                //                 $schedule_details->active_status = "inactive";
                //             }

                //         }
                //         else
                //         {
                //             $today = Date('Ymd',time());
                //             $date = $schedule_details->time_slot->datetime->format('Ymd');
                //             if($date<=$today)
                //             {
                //                 $schedule_details->active_status = "active";
                //             }
                //             else
                //             {
                //                 $schedule_details->active_status = "inactive";
                //             }
                //         }

                //     }

                // }
            }

        }

        return view('new_schedule', compact('scheduleTimeSlots', 'doctor_course_id', 'course_id', 'batch_id', 'systemDriven', 'schedule_id' , 'schedule_links', 'previous_exam_mendatory', 'only_available_exam' ) );
    }

    public function schedule( Request $request )
    {

        $schedule = BatchesSchedules::with('meta')->where( 'id', $request->schedule_id )->first();

        $schedule_links = null;
        if($schedule->meta){
            $schedule_links = $schedule->meta;
            $schedule_links=$schedule_links->where('key','fb_links')->first();
            $schedule_links = json_decode($schedule_links->value ?? null);
        }

        $request->session( )->put( '__schedule_back_link', $request->fullUrl( ) );


        $course_id = $schedule->course_id ?? null;
        $batch_id = $schedule->batch_id ?? null;

        $doctor_course = DoctorsCourses::with('batch','institute', 'subject', 'faculty')
            ->where( [ 'course_id' => $course_id, 'batch_id' => $batch_id ] )
            ->where( 'doctor_id', Auth::guard('doctor')->id() )->first( );

        $doctor_course_id = $doctor_course->id ?? '';

        $notAdmitted = false;

        if( $doctor_course ) {
            if( ( $doctor_course->batch->fee_type ?? '' )  == 'Discipline_Or_Faculty' ){
                if( $doctor_course->institute->type == 1 ){
                    if( $schedule->faculty_id != ($doctor_course->faculty_id ?? '') ) {
                        $notAdmitted = true;
                    }
                }else if( $schedule->subject_id != ($doctor_course->subject_id ?? '') ) {
                    $notAdmitted = true;
                }
            }
        }

        if( $notAdmitted === true ) {
            Session::flash( 'message', 'To gain access if already admitted please check your faculty/discipline.' );
            return redirect( route('doctor_admission') );
        }


        $search_date = $_GET['date'] ?? '';

        $scheduleTimeSlots = $this->_schedule_time_slot( $doctor_course_id );

        $scheduleTimeSlots->where('schedule_id', $schedule->id );

        if( $search_date ) {
            $scheduleTimeSlots->whereRaw("DATE(`datetime`) = '" . $search_date . "'" );
        }

        $scheduleTimeSlots = $scheduleTimeSlots->orderBy('datetime' )->paginate( 3 )->appends( $request->query() );

        $systemDriven = true;

        if($schedule->batch->system_driven == "Mandatory" || ( $schedule->batch->system_driven == "Optional" && $doctor_course->system_driven == "Yes"))
        {
            if(isset($scheduleTimeSlots) && count($scheduleTimeSlots)>0)
            {
                $doctor_course_schedule_details = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$doctor_course_id,'schedule_id'=>$schedule_id])->orderBy('id','desc')->first();
                if(isset($doctor_course_schedule_details))
                {
                    if($doctor_course_schedule_details->feedback == "not_completed" || $doctor_course_schedule_details->feedback == "")
                    {
                        foreach($scheduleTimeSlots as $scheduleTimeSlot)
                        {

                            foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                            {

                                if($doctor_course_schedule_details->schedule_details_id == $schedule_details->id)
                                {
                                    $schedule_details->active_status = "active";
                                }
                                else
                                {
                                    $schedule_details->active_status = "inactive";
                                }

                            }

                        }

                    }
                    else if($doctor_course_schedule_details->feedback == "completed")
                    {
                        $today = Date('Ymd',time());$count=$completed=0;
                        foreach($scheduleTimeSlots as $scheduleTimeSlot)
                        {
                            foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                            {

                                $date = $schedule_details->time_slot->datetime->format('Ymd');
                                if($date==$today)
                                {
                                    $count++;
                                    $available_schedule = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$doctor_course_id,'schedule_id'=>$schedule_id,'schedule_details_id'=>$schedule_details->id])->first();
                                    if(isset($available_schedule))
                                    {
                                        if($available_schedule->feedback == "not_completed" || $available_schedule->feedback == "" )
                                        {
                                            $schedule_details->active_status = "active";
                                        }
                                        else if($available_schedule->feedback == "completed")
                                        {
                                            $completed++;
                                        }
                                    }
                                    else
                                    {
                                        $schedule_details->active_status = "active";
                                    }

                                }
                                else
                                {
                                    $schedule_details->active_status = "inactive";
                                }

                            }
                            if($count == $completed)
                            {
                                $today = Date('Ymd',time());
                                foreach($scheduleTimeSlots as $scheduleTimeSlot)
                                {
                                    foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                                    {
                                        $date = $schedule_details->time_slot->datetime->format('Ymd');
                                        if($date<=$today)
                                        {
                                            $available_schedule = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$doctor_course_id,'schedule_id'=>$schedule_id,'schedule_details_id'=>$schedule_details->id])->first();
                                            if(isset($available_schedule))
                                            {
                                                if($available_schedule->feedback == "not_completed" || $available_schedule->feedback == "" )
                                                {
                                                    $schedule_details->active_status = "active";
                                                }
                                            }
                                            else
                                            {
                                                $schedule_details->active_status = "active";
                                            }

                                        }
                                        else
                                        {
                                            $schedule_details->active_status = "inactive";
                                        }
                                    }
                                }

                            }

                        }

                    }

                }
                else
                {

                    $today = Date('Ymd',time());
                    foreach($scheduleTimeSlots as $scheduleTimeSlot)
                    {
                        foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                        {
                            $date = $schedule_details->time_slot->datetime->format('Ymd');
                            if($date<=$today)
                            {
                                $schedule_details->active_status = "active";
                            }
                            else
                            {
                                $schedule_details->active_status = "inactive";
                            }
                        }
                    }

                }

                // foreach($scheduleTimeSlots as $scheduleTimeSlot)
                // {
                //     foreach($scheduleTimeSlot->schedule_details as $schedule_details)
                //     {

                //         $doctor_course_schedule_details = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$doctor_course_id,'schedule_details_id'=>$schedule_details->id])->first();
                //         if(isset($doctor_course_schedule_details))
                //         {
                //             if($doctor_course_schedule_details->feedback == "not_completed" || $doctor_course_schedule_details->feedback == "")
                //             {
                //                 $schedule_details->active_status = "active";
                //             }
                //             else if($doctor_course_schedule_details->feedback == "completed")
                //             {
                //                 $schedule_details->active_status = "inactive";
                //             }

                //         }
                //         else
                //         {
                //             $today = Date('Ymd',time());
                //             $date = $schedule_details->time_slot->datetime->format('Ymd');
                //             if($date<=$today)
                //             {
                //                 $schedule_details->active_status = "active";
                //             }
                //             else
                //             {
                //                 $schedule_details->active_status = "inactive";
                //             }
                //         }

                //     }

                // }
            }

        }

        $data['scheduleTimeSlots'] = $scheduleTimeSlots;
        $data['doctor_course_id'] = $doctor_course_id;
        $data['course_id'] = $course_id;
        $data['batch_id'] = $batch_id;
        $data['systemDriven'] = $systemDriven;
        $data['schedule_id'] = $schedule->id;
        $data['schedule_links'] = $schedule_links;


        return view('new_schedule', $data);
    }



    public function batch_schedule($course_id,$batch_id)
    {
        $doc_info = Doctors::where( 'id', Auth::guard('doctor')->id() )->first();

        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doc_info->id,'course_id'=>$course_id,'batch_id'=>$batch_id,'is_trash'=>'0'])
            ->where('payment_status', '!=' , 'No Payment')->get();

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $exam_link_ids = array( );

        foreach($doctor_courses as $key=>$doctor_course){
            if(OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'batch_schedule_batch.institute_id'=>$doctor_course->institute_id,'batch_schedule_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){
                $exam_link_ids[] = OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'batch_schedule_batch.institute_id'=>$doctor_course->institute_id,'batch_schedule_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->value('id');
            }
        }

        if($doctor_courses[0]->batch->fee_type == "Batch")
        {
            $batch_schedule_batch = OnlineExamLink::whereIn('batch_schedule_batch.id',$exam_link_ids)
                ->join('batch_schedule_batch_batch_schedule','batch_schedule_batch_batch_schedule.batch_schedule_batch_id','batch_schedule_batch.id')
                ->join('batch_schedule','batch_schedule.id','batch_schedule_batch_batch_schedule.batch_schedule_id')
                ->paginate(10);

        }
        else if($doctor_courses[0]->batch->fee_type == "Discipline")
        {
            $batch_schedule_batch = OnlineExamLink::whereIn('batch_schedule_batch.id',$exam_link_ids)->where('batch_schedule_discipline.subject_id',$doctor_courses[0]->subject_id)
                ->join('batch_schedule_batch_batch_schedule','batch_schedule_batch_batch_schedule.batch_schedule_batch_id','batch_schedule_batch.id')
                ->join('batch_schedule','batch_schedule.id','batch_schedule_batch_batch_schedule.batch_schedule_id')
                ->join('batch_schedule_discipline','batch_schedule_discipline.batch_schedule_id','batch_schedule.id')
                ->paginate(10);
        }

        $data['batch_schedule_batch'] = $batch_schedule_batch;

        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doc_info->id,'is_trash'=>'0'])->where('payment_status', '!=' , 'No Payment')->get();
        $doctor_courses_with_exam = array();
        foreach($doctor_courses as $key=>$doctor_course){

            if(OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'batch_schedule_batch.institute_id'=>$doctor_course->institute_id,'batch_schedule_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){
                $doctor_courses_with_exam[] = $doctor_course;
            }

        }

        $data['doctor_courses'] = $doctor_courses_with_exam;
        return view('batch_schedule/lecture_topics',$data);

    }

    public function lecture_topics($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_course_info = DoctorsCourses::where('id', $id)->first();
        $data['doctor_course_info'] = $doctor_course_info;
        $batch_schedule_batch_id = OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'batch_schedule_batch.institute_id'=>$doctor_course->institute_id,'batch_schedule_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->value('id');
        $batch_schedule_batch = OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'batch_schedule_batch.institute_id'=>$doctor_course->institute_id,'batch_schedule_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])
            ->join('batch_schedule_topics','batch_schedule_topics.batch_schedule_batch_id','batch_schedule_batch.id')
            ->join('topics','topics.id','batch_schedule_topics.topic_id')
            ->join('batch_schedule_post','batch_schedule_post.topic_id','topics.id')
            ->paginate(10);
        $data['batch_schedule_batch'] = $batch_schedule_batch;
        $data['batch_schedule_batch_id'] = $batch_schedule_batch_id;
        $data['batch_schedule_topics'] = OnlineExamTopics::where('batch_schedule_batch_id',$batch_schedule_batch_id)->join('topics','topics.id','batch_schedule_topics.topic_id')->pluck('topics.name', 'topics.id');
        return view('lecture_topics',$data);
    }

    public function lecture_details($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_course_info'] = DoctorsCourses::where('id', $id)->first();
        $data['batch_schedule'] = OnlineExam::where('id',$id)->first();
        $data['batch_schedules'] = OnlineExam::where('topic_id',$data['batch_schedule']->topic_id)->get();
        return view('lecture_details',$data);
    }

    public function batch_schedule_details($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_course_info'] = DoctorsCourses::where('id', $data['doc_info']->id)->first();
        $data['link'] = OnlineExam::where('id',$id)->first();
        $agent =  new Agent();
        $data['browser'] = $agent->browser();
        return view('batch_schedule/lecture_details',$data);
    }

    public function topic_batch_schedules(Request $request)
    {
        $batch_schedule_batch_id = $request->batch_schedule_batch_id;
        $topic_id = $request->topic_id;

        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();

        $batch_schedule_batch = OnlineExam::where(['topic_id'=>$topic_id])
            ->paginate(10);
        $data['batch_schedule_batch'] = $batch_schedule_batch;
        $data['batch_schedule_batch_id'] = $batch_schedule_batch_id;
        $data['topic'] = Topics::where('id',$topic_id)->first();
        $data['batch_schedule_topics'] = OnlineExamTopics::where('batch_schedule_batch_id',$batch_schedule_batch_id)->join('topics','topics.id','batch_schedule_topics.topic_id')->pluck('topics.name', 'topics.id');
        //echo '<pre>';print_r($data['topic']);exit;
        return view('topic_batch_schedules',$data);
    }



    public function batch_scheduleoo()
    {

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where('doctor_id',$doc_info->id)->get();

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $online_lecture_links = array();
        foreach($doctor_courses as $key=>$doctor_course){
            $exam_comm_code_ids = OnlineLectureLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'institute_id'=>$doctor_course->institute_id,'course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->pluck('lecture_address_id');

            foreach ($exam_comm_code_ids as $id){
                $online_lecture_links[$doctor_course->reg_no][] =  OnlineLectureAddress::select('*')->where('id',$id)->get();
            }
        }
        $data['online_lecture_links'] = $online_lecture_links;
        $data['rc'] = '';
        $data['exam_link'] = OnlineLectureAddress::select('*')->where('status', 1)->get();
        return view('lecture/batch_schedule', $data);

    }

    public function system_driven( $id )
    {
        $data['doctor_course'] = DoctorsCourses::find($id);
        return view('admin.doctors_courses.system_driven', $data);
    }

    public function system_driven_save( Request $request )
    {
        $doctor_course = DB::table('doctors_courses')->where(['id'=>$request->doctor_course_id])->update(['system_driven'=>$request->system_driven,'system_driven_count'=>$request->system_driven_count,'updated_by'=>Auth::id()]);
        Session::flash('message', 'Doctor System Driven Changed successfully');
        $data['doctor_course'] = DoctorsCourses::find($request->doctor_course_id);
        return view('admin.doctors_courses.system_driven', $data);
    }

}

