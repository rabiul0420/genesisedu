<?php
namespace App\Http\Controllers\Admin;

use App\AddonService;
use App\BatchAddonService;
use App\BatchesFaculties;
use App\BatchesSubjects;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Branch;
use App\CourierChargePackage;
use App\Sessions;
use App\DoctorsCourses;
use App\DisciplineFee;
use Session;
use Auth;
use Validator;
use App\DoctorCourseScheduleDetails;

use DB;
use Yajra\DataTables\Facades\DataTables;

/*Batch faculties and subjects*/
class BatchController extends Controller
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
        $title = 'Batch List';

        $batches = Batches::where('status','1')->pluck('name', 'id');
        $courses= Courses::where('status','1')->pluck('name', 'id');
        $sessions = Sessions::get()->pluck('name', 'id');
        
        return view('admin.batch.list',['title'=>$title,'sessions'=>$sessions,'batches'=>$batches,'courses'=>$courses]);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function batch_list( Request $request ){

        $year = $request->year;
        $session_id = $request->session_id;
        $course_id = $request->course_id;
        $batch_id = $request->batch_id;

        $batch_list = DB::table('batches as b')
        ->leftjoin('institutes as i', 'b.institute_id', '=','i.id' )
        ->leftjoin('courses as c', 'b.course_id', '=','c.id' )
        ->leftjoin('branches as br', 'b.branch_id', '=','br.id')
        ->leftjoin('courier_charge_package as ccp', 'b.package_id', '=','ccp.id')
        ->leftjoin('sessions as se', 'b.session_id', '=','se.id');

        if($year){
            $batch_list = $batch_list->where('b.year', '=', $year);
        }
        if($session_id){
            $batch_list = $batch_list->where('b.session_id', '=', $session_id);
        }
        if($course_id){
            $batch_list = $batch_list->where('b.course_id', '=', $course_id);
        }
        if($batch_id){
            $batch_list = $batch_list->where('b.id', '=', $batch_id);
        }

       $batch_list = $batch_list->select('b.id as id'
        ,'b.year as year'
        ,'b.name as batch_name'
        ,'se.name as session_name'
        ,'b.capacity as capacity'
        ,'i.name as institute_name'
        ,'c.name as course_name'
        ,'br.name as branch_name'
        ,'b.batch_type as batch_type'
        ,'b.fee_type as fee_type'
        ,'b.admission_fee as admission_fee'
        ,'b.lecture_sheet_fee as lecture_sheet_fee'
        ,'b.discount_from_regular as discount_from_regular'
        ,'b.discount_from_exam as discount_from_exam'
        ,'b.payment_times as payment_times'
        ,'b.minimum_payment as minimum_payment'
        ,'b.discount_fee as discount_fee'
        ,'ccp.name as package_name'
        ,'b.is_emi as is_emi'
        ,'b.system_driven as system_driven'
        ,'b.status as status');

        return DataTables::of($batch_list)
        ->addColumn('action', function ($batch_list) {
             $data['batch'] = $batch_list;
            return view('admin.batch.ajax_list',$data);
        })

         ->make(true);
    }

    public function create()
    {

        $data['institute'] = Institutes::get()->pluck('name', 'id');
        $data['branches'] = Branch::pluck('name', 'id');
        $data['package_name']= DB::table('courier_charge_package')->pluck('name','id');
        // $data['is_emi_name']= DB::table('batches')->pluck('emi_types','id');
        // $data['is_emi_types']= DB::table('is_emi_types')->pluck('name','id');
        $data['title'] = 'Batch Create';

        return view('admin.batch.create',$data);
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
            'capacity' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'branch_id' => ['required'],
            'fee_type' => ['required'],
            'status'=> ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter valid data!!!');
            return redirect()->action('Admin\BatchController@create')->withInput();
        }

        if (Batches::where(['year' => $request->year, 'name' => $request->name, 'session_id' => $request->session_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Batch Name already exists');
            return redirect()->action('Admin\BatchController@create')->withInput();
        }

        // if (Batches::where(['year' => $request->year, 'batch_code'=>$request->batch_code, 'course_id'=>$request->course_id])->exists()){
        //     Session::flash('class', 'alert-danger');
        //     session()->flash('message','This Batch Code already exists');
        //     return redirect()->action('Admin\BatchController@create')->withInput();
        // }
        else{

            $batch = new Batches();
            $batch->name = $request->name;
            $batch->year = $request->year;
            // $batch->batch_code = $request->batch_code;
            $batch->capacity = $request->capacity;
            // $batch->end_index = $request->end_index;
            $batch->institute_id = $request->institute_id;
            $batch->course_id = $request->course_id;
            $batch->session_id=$request->session_id;
            $batch->branch_id = $request->branch_id;
            $batch->service_point_discount = $request->service_point_discount;
            $batch->lecture_sheet_mobile_no = $request->lecture_sheet_mobile_no;

            $batch->fee_type = $request->fee_type;
            $batch->batch_type = $request->batch_type;


            $batch->admission_fee=$request->admission_fee?$request->admission_fee:0;
            $batch->lecture_sheet_fee=$request->lecture_sheet_fee?$request->lecture_sheet_fee:0;
            $batch->discount_from_regular = $request->discount_from_regular?$request->discount_from_regular:0;
            $batch->discount_from_exam = $request->discount_from_exam?$request->discount_from_exam:0;


            $batch->payment_times=$request->payment_times;
            $batch->full_payment_waiver=$request->full_payment_waiver;
            $batch->minimum_payment=$request->minimum_payment;

            $batch->apply_new_discount_rule = $request->apply_new_discount_rule;

            $batch->details=$request->details;

            $batch->is_show_admission = $request->is_show_admission;
            $batch->is_special = $request->is_special ?? 'No';

            // previous exam mendatory
            $batch->previous_exam_mendatory = $request->previous_exam_mendatory;

            $batch->is_show_lecture_sheet_fee=$request->is_show_lecture_sheet_fee ?? 'No';
            $batch->package_id=$request->package_id;
            // $batch->is_emi=$request->is_emi;
            $batch->shipment=$request->shipment;

            $batch->discount_fee=$request->discount_fee;

            $batch->status=$request->status;
            $batch->expired_at=$request->expired_at;
            $batch->expired_message=$request->expired_message;

            $batch->created_by=Auth::id();

            $batch->system_driven=$request->system_driven;

            $subjects = $request->subject_id;
            $faculties = $request->faculty_id;

            $batch->subject_id = is_array($subjects) && isset($subjects[0]) ? $subjects[0] : null;

            if( $batch->save( ) ) {
                $this->insert_batches_faculties( $batch->id, $faculties );
                $this->insert_batches_subjects( $batch->id, $subjects );
            }

            Session::flash('message', 'Record has been added successfully');
            return redirect()->action('Admin\BatchController@index');
        }
    }

    private function insert_batches_faculties( $batch_id, $faculty_ids, $willDeletePrevious = false ){
        if( is_array( $faculty_ids ) ) {

            if( $willDeletePrevious ) {
                $batchFaculty = BatchesFaculties::where( [ 'batch_id' => $batch_id ] );
                if( $batchFaculty->exists( ) ) {
                    $batchFaculty->update( ['deleted_by' => Auth::id() ]);
                    $batchFaculty->delete( );
                }
            }

            foreach ( $faculty_ids as $faculty_id ) {
                BatchesFaculties::insert( [ 'faculty_id' => $faculty_id, 'batch_id' => $batch_id ] );
            }
        }
    }

    private function insert_batches_subjects( $batch_id, $subject_ids, $willDeletePrevious = false ){
        if( is_array( $subject_ids ) ) {
            if( $willDeletePrevious ) {
                $batchSubject = BatchesSubjects::where( [ 'batch_id' => $batch_id ] );
                if( $batchSubject->exists( ) ) {
                    $batchSubject->update( ['deleted_by' => Auth::id() ]);
                    $batchSubject->delete( );
                }
            }

            foreach ( $subject_ids as $subject_id ) {
                BatchesSubjects::insert( [ 'subject_id' => $subject_id, 'batch_id' => $batch_id ] );
            }
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
        $user=Subjects::select('users.*')->find($id);
        return view('admin.subjects.show',['user'=>$user]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $batch = Batches::find($id);
        $data['batch'] = $batch->load('addon_services');

        $data['addon_services'] = AddonService::get();

        $data['branches'] = Branch::pluck('name', 'id');
        $data['institute'] = Institutes::pluck('name', 'id');
        $data['course'] = Courses::where('institute_id', $batch->institute_id)->pluck('name', 'id');
        $data['faculty'] = Faculty::where('id', $batch->faculty_id)->pluck('name', 'id');

        $data[ 'faculties' ] = Faculty::where( 'course_id', $batch->course_id )->pluck( 'name', 'id' );

        $data[ 'selected_faculties' ] = BatchesFaculties::where( 'batch_id', $batch->id )->pluck( 'faculty_id' );
        $data[ 'selected_subjects' ] = BatchesSubjects::where( 'batch_id', $batch->id )->pluck( 'subject_id' );


        $data[ 'subjects' ] =  Subjects::where('course_id', $batch->course_id )->pluck('name', 'id');
        // $data['package'] =     CourierChargePackage::pluck('name','id');
        $data['package_name'] = CourierChargePackage::pluck('name', 'id');
        // $data['batch'] = Batches::pluck('is_emi','emi_types','id');

        $data['title'] = 'Batch Edit';

        $data['sessions'] = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
                ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
                ->where('course_year.year',$batch->year)
                ->where('course_year.deleted_at',NULL)
                ->where('course_year_session.deleted_at',NULL)
                ->where('course_id',$batch->course_id)
                ->where('show_admission_form','yes')
                ->pluck('name',  'sessions.id');
        return view('admin.batch.edit', $data);

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
        // return $request;
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'branch_id' => ['required'],
            'fee_type' => ['required'],
            'status' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\BatchController@edit', [$id])->withInput();
        }

        $batch = Batches::find($id);

        if($batch->name != $request->name) {

            if (Batches::where(['year' => $request->year, 'name' => $request->name , 'session_id' => $request->session_id])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This Batch Name already exists');
                return redirect()->action('Admin\BatchController@edit', [$id])->withInput();
            }

        }

        // if($batch->batch_code != $request->batch_code) {

        //     if (Batches::where(['year' => $request->year, 'batch_code'=>$request->batch_code,'course_id'=>$request->course_id])->exists()){
        //         Session::flash('class', 'alert-danger');
        //         session()->flash('message','This Batch Code already exists');
        //         return redirect()->action('Admin\BatchController@edit',[$id])->withInput();
        //     }

        // }

        $batch->name=$request->name;
        //$batch->batch_code=$request->batch_code;
        $batch->year = $request->year;
        // $batch->start_index=$request->start_index;
        // $batch->end_index=$request->end_index;
        $batch->capacity = $request->capacity;
        $batch->institute_id=$request->institute_id;
        $batch->course_id=$request->course_id;
        $batch->session_id=$request->session_id;
        $batch->branch_id=$request->branch_id;
        $batch->service_point_discount = $request->service_point_discount;
        $batch->lecture_sheet_mobile_no=$request->lecture_sheet_mobile_no;


        $batch->fee_type=$request->fee_type;
        $batch->batch_type=$request->batch_type;


        $batch->admission_fee=$request->admission_fee?$request->admission_fee:0;
        $batch->lecture_sheet_fee=$request->lecture_sheet_fee?$request->lecture_sheet_fee:0;
        $batch->discount_from_regular = $request->discount_from_regular?$request->discount_from_regular:0;
        $batch->discount_from_exam = $request->discount_from_exam?$request->discount_from_exam:0;

        $batch->payment_times=$request->payment_times;
        $batch->full_payment_waiver=$request->full_payment_waiver;
        $batch->minimum_payment=$request->minimum_payment;

        $batch->apply_new_discount_rule = $request->apply_new_discount_rule;

        $batch->details=$request->details;

        $batch->is_show_admission = $request->is_show_admission;
        $batch->is_special = $request->is_special ?? $batch->is_special;            
        
        // previous exam mendatory
        $batch->previous_exam_mendatory = $request->previous_exam_mendatory;

        $batch->is_show_lecture_sheet_fee=$request->is_show_lecture_sheet_fee;
        $batch->package_id=$request->package_id;
        // $batch->is_emi=$request->is_emi ?? $batch->is_emi;
        $batch->shipment=$request->shipment;
        $batch->discount_fee=$request->discount_fee;

        $batch->status=$request->status;
        $batch->expired_at=$request->expired_at;
        $batch->expired_message=$request->expired_message;
        $batch->updated_by = Auth::id( );
        $message = "";
        if($batch->system_driven != $request->system_driven)
        {
            $batch->system_driven=$request->system_driven;
            $batch->system_driven_text="";
            $batch->system_driven_change_count_max="";

            $doctors_courses_ids = DoctorsCourses::where(['batch_id'=>$batch->id,'year'=>$batch->year,'course_id'=>$batch->course_id,'session_id'=>$batch->session_id])->pluck('id');
            if(isset($doctors_courses_ids) && count($doctors_courses_ids))
            DoctorsCourses::whereIn('id',$doctors_courses_ids->all())->update(['system_driven'=>"",'system_driven_count'=>"",'updated_by'=>Auth::id()]);

            if(isset($batch->batch_schedule) && count($batch->batch_schedule))
            {
                foreach($batch->batch_schedule as $schedule)
                {
                    DoctorCourseScheduleDetails::where(['schedule_id'=>$schedule->id])->delete();
                }
            }

            if($request->system_driven != "No")$message = "<br>Batch System Driven Option has been changed. Please click <a href=".url("admin/batch-system-driven/".$batch->id)."><span style=\"font-weight:700;font-size:19px\";>BATCH SYSTEM DRIVEN</span></a> to set necessary values of Batch System Driven Option...";
        }



        $subjects = $request->subject_id;
        $faculties = $request->faculty_id;


        $batch->subject_id = is_array( $subjects ) && isset( $subjects[0]) ? $subjects[0] : null;

        $batch->push();

        $this->storeAddonService($batch->id, $request, true);

        $this->insert_batches_faculties( $batch->id, $faculties, true );
        $this->insert_batches_subjects( $batch->id, $subjects, true );


        Session::flash('message', 'Record has been updated successfully'.$message);
        return back();
    }

    protected function storeAddonService($batch_id, $request, $willDeletePrevious = false)
    {
        if($willDeletePrevious) {
            BatchAddonService::where('batch_id', $batch_id)->delete();
        }

        if(is_array($request->addon_services)) {
            foreach($request->addon_services as $addon_service) {
                BatchAddonService::onlyTrashed()->updateOrCreate(
                    [
                        'batch_id' => $batch_id
                    ],
                    [
                        'addon_service_id' => $addon_service,
                        'deleted_at' => null,
                    ]
                );
            }
        }
    }


    public function print_batch_doctor_address( )
    {

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['branches'] = Branch::pluck('name','id');
        $data['module_name'] = 'Batch Doctors Address';
        $data['title'] = 'Batch Doctors Address Print';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.batch.batch_doctor_address', $data );

    }

    public function print_batch_doctors_addresses(Request $request)
    {

        $doctors_courses_unformated = DoctorsCourses::where(['year'=>$request->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->get();
        $doctors_courses = array();
        $j=1;$i=1;
        foreach($doctors_courses_unformated as $key=>$doctor_course)
        {
            if(isset($doctor_course->doctor->present_address) && $doctor_course->doctor->present_address != null )
            {
                //echo '<pre>';print_r($doctor_course);
                if($j==3)
                {
                    $i++;
                    $j=1;
                }
                $doctors_courses[$i][$j++] = $doctor_course;

            }

        }
        $data['doctors_courses'] = $doctors_courses;
        //echo '<pre>';print_r($doctors_courses);exit;
        return view('admin.batch.batch_doctors_addresses_print',$data);

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
        Batches::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\BatchController@index');
    }

    public function system_driven( $id )
    {
        $data['batch'] = Batches::find($id);
        return view('admin.batch.system_driven', $data);
    }

    public function system_driven_save( Request $request )
    {
        $batch = DB::table('batches')->where(['id'=>$request->batch_id])->update(['system_driven_text'=>$request->system_driven_text,'system_driven_change_count_max'=>$request->system_driven_change_count_max??"0",'updated_by'=>Auth::id()]);
        Session::flash('message', 'Batch System Driven saved successfully');
        $data['batch'] = Batches::find($request->batch_id);
        return view('admin.batch.system_driven', $data);
    }

    public function payment_option( $id )
    {
        $data['batch'] = Batches::where(['id'=>$id])->first();
        $data['module_name'] = 'Batch Payment Options';
        $data['title'] = 'Batch Payment Options';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.batch.payment_option', $data );
        
    }

    public function payment_option_save(Request $request)
    {
        \App\BatchPaymentOptions::where(['batch_id'=>$request->batch_id])->update(['deleted_by'=>Auth::id()]);
        \App\BatchPaymentOptions::where(['batch_id'=>$request->batch_id])->delete();

        for($i = 1; $i <= count($request->payment_date);$i++)
        {
            \App\BatchPaymentOptions::insert(['batch_id'=>$request->batch_id,'payment_date'=>$request->payment_date[$i],'amount'=>$request->amount[$i],'created_by'=>Auth::id()]);
        }

        Session::flash('message', 'Batch payment options saved successfully');
        return redirect()->action('Admin\BatchController@payment_option',$request->batch_id);
        
    }
}
