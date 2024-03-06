<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Sms;
use App\Doctors;
use App\DoctorsCourses;
use App\DoctorSmss;
use App\DoctorSmsView;
use App\SmsBatch;
use Illuminate\Http\Request;
use App\Batches;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Sessions;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\SmsAssign;
use App\SmsBatchSms;
use App\SmsCourse;
use App\SmsCourseSms;
use App\SmsYearSms;
use App\SmsDiscipline;
use App\SmsEvent;
use App\SmsFaculty;
use App\SmsLog;
use App\Doctor;
use App\SendSms;
use Illuminate\Contracts\Session\Session as SessionSession;
use Carbon\Carbon;

class SmsController extends Controller
{
    use SendSms;
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Auth::loginUsingId(1);
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
        $data['smss'] = Sms::get();
        return view('admin.sms.list',$data);
    }

    public function sms_list() {
        $sms_list = DB::table('sms as d1')->leftJoin('sms_event','sms_event.id','d1.sms_event_id');

        $sms_list->select(
            'd1.id as id',
            'd1.created_at as create_time',
            'd1.title as sms_title',
            'd1.type as sms_type',
            'sms_event.name as sms_event_name',
            'd1.sms_send_option',
            'd1.status as status',
        );

        $sms_list = $sms_list->whereNull('d1.deleted_at');

        return DataTables::of($sms_list)
            ->addColumn('sms_event', function ($sms_list) {
                return $sms_list->sms_event_name??"";
            })
            ->addColumn('doctor_course_option', function ($sms_list) {
                if($sms_list->sms_send_option == "1") {
                    return "All";
                }
                else if($sms_list->sms_send_option == "2") {
                    return "Completed";
                }
                else if($sms_list->sms_send_option == "3") {
                    return "No Payment";
                }
                else if($sms_list->sms_send_option == "4") {
                    return "In Progress";
                }
                else {
                    return "";
                }
            })
            ->addColumn('action', function ($sms_list) {
                return view('admin.sms.sms_ajax_list',(['sms_list'=>$sms_list]));
            })

            ->addColumn('status',function($sms_list) {
                return '<span style="color:' .( $sms_list->status == 1 ? 'green;':'red;').' font-size: 14px;  ">'. ($sms_list->status == 1 ? 'Active':'Inactive') . '</span>';
            })

            ->addColumn('sms_type',function($sms_list) {

                if($sms_list->sms_type == "I") {
                    return "Individual";
                }
                else if($sms_list->sms_type == "A") {
                    return "All";
                }
                else if($sms_list->sms_type == "B") {
                    return "Batch";
                }
                else if($sms_list->sms_type == "C") {
                    return "Course";
                }
                else {
                    return "Others";
                }
            })
            ->rawColumns(['action','status', 'attachment', 'sms_type'])

        ->make(true);
    }

    public function sms_type( Request $request )
    {
        return view('admin.sms.sms_type_individual');
    }

    public function sms_search_doctors(Request $request)
    {
        $text =  $_GET['term'];
        $text = $text['term'];
        $data = Doctors::select(DB::raw("CONCAT(name,' - ',bmdc_no) AS name_bmdc"),'id')
            ->where('name', 'like', '%'.$text.'%')
            ->orWhere('bmdc_no', 'like', '%'.$text.'%')
            ->orWhere('mobile_number', 'like', '%'.$text.'%')
            ->get();
        
        echo json_encode( $data);
    }

    public function sms_institute_course(Request $request)
    {
        $institute_id = $request->institute_id;
        $courses = Courses::get()->where('institute_id',$institute_id)->pluck('name', 'id');
        return view('admin.sms.sms_institute_course',['courses'=>$courses]);
    }

    public function sms_course_batch(Request $request)
    {
        $course_id = $request->course_id;
        $batches = Batches::get()->where('course_id',$course_id)->pluck('name', 'id');
        return view('admin.sms.sms_course_batch',['batches'=>$batches]);
    }

    public function course_branch_changed_in_sms_batch( Request $request )
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        if($institute_type)
        {
            $faculties = Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            $smss = Sms::where(['type'=>"B"])->pluck('title', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties])->render(),'smss'=>view('admin.sms.smss',['smss'=>$smss])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            $smss = Sms::where(['type'=>"B"])->pluck('title', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects])->render(),'smss'=>view('admin.sms.smss',['smss'=>$smss])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user=Institutes::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['sms_events'] = SmsEvent::pluck('name','id');
        
        $data['title'] = 'Genesis Admin : Sms Create';
        return view('admin.sms.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $sms = new Sms();
        $sms->title = $request->title;
        $sms->sms = $request->sms;
        $sms->type = $request->type;
        $sms->sms_event_id = $request->sms_event_id;
        $sms->sms_send_option = $request->sms_send_option;
                
        $sms->created_by = Auth::id();
        $sms->status = $request->status;

        $sms->save();

        if ($request->doctor_id) {
            foreach ($request->doctor_id as $k => $value) {
                
                DoctorSmss::insert(['doctor_id' => $value, 'sms_id' => $sms->id]);
            }
        }

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\SmsController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['id'] = $id;
        $sms = Sms::where('id', $id)->first();
        $data['smss'] = Sms::where('id', $id)->first();

        if ($sms->type=='I'){
            $data['doctors'] = DoctorSmss::where('sms_id', $id)->get();
        }

        return view('admin.sms.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=Institutes::find(Auth::id());
 
         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/
        //echo $id;exit;
        $data['sms'] = Sms::find($id);
        $data['sms_events'] = SmsEvent::pluck('name','id');

        $data['doctor_ids'] = DoctorSmss::where(['sms_id' => $id])->pluck('doctor_id');
        $data['doctors'] = DoctorSmss::select('doctors.id as doctor_id',DB::raw("CONCAT(name,' - ',bmdc_no) AS full_name"),'doctor_sms.*')->join('doctors','doctor_sms.doctor_id','doctors.id')->where(['doctor_sms.sms_id' => $data['sms']->id])->orderBy('doctor_sms.id','asc')->pluck('full_name','doctor_id');        

        return view('admin.sms.edit', $data);
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
            'title' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\SmsController@edit')->withInput();
        }

        $sms = Sms::find($id);

        $sms->title = $request->title;
        $sms->sms = $request->sms;

        if($request->hasFile('attachment')){
            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $filename = rand(12,98).'_'.time().'.'.$extension;
            $file->move('upload/sms/',$filename);
            $sms->attachment = 'upload/sms/'.$filename;
        }

        $sms->type = $request->type;
        $sms->sms_event_id = $request->sms_event_id;
        $sms->sms_send_option = $request->sms_send_option;
        
        $sms->updated_by = Auth::id();
        $sms->status = $request->status;

        $sms->push();

        if ($request->doctor_id) {
            
            $doctor_sms = DoctorSmss::where('sms_id', $id)->get();
            foreach ($doctor_sms as $k => $doctors) {
                DoctorSmss::destroy($doctors->id);
            }

            foreach ($request->doctor_id as $k => $value) {
                DoctorSmss::insert(['doctor_id' => $value, 'sms_id' => $id]);
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


        if( $sms = Sms::find( $id ) ) {
            Sms::where( 'id', $id)->update( ['deleted_by' => Auth::id( ) ] );
            $sms->delete( );
        }

        if( SmsAssign::where(['sms_id'=>$id])->exists() ) {
            SmsAssign::where(['sms_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            SmsAssign::where(['sms_id'=>$id])->delete();
        }

        if( SmsDiscipline::where(['sms_id'=>$id])->exists() ) {
            SmsDiscipline::where(['sms_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            SmsDiscipline::where(['sms_id'=>$id])->delete();
        }

        if( SmsFaculty::where(['sms_id'=>$id])->exists() ) {
            SmsFaculty::where(['sms_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            SmsFaculty::where(['sms_id'=>$id])->delete();
        }

        if( SmsBatchSms::where(['sms_id'=>$id])->exists() ) {
            SmsBatchSms::where(['sms_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            SmsBatchSms::where(['sms_id'=>$id])->delete();
        }

        if( DoctorSmss::where(['sms_id'=>$id])->exists() ) {
            DoctorSmss::where(['sms_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            DoctorSmss::where(['sms_id'=>$id])->delete();
        }


        if( DoctorSmsView::where(['sms_id'=>$id])->exists() ) {
            DoctorSmsView::where(['sms_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            DoctorSmsView::where(['sms_id'=>$id])->delete();
        }


        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SmsController@index');
    }

    function sms_send_list($id)
    {
        $sms_send_option = '1';
        $sms = Sms::where(['id'=>$id])->first();
        if(isset($sms))
        {            
            if(empty($sms->sms_send_option) || $sms->sms_send_option=="1")
            {
                $sms_send_option = '1';
            }
            else if($sms->sms_send_option == "2")
            {
                $sms_send_option = 'Completed';
            }
            else if($sms->sms_send_option == "3")
            {
                $sms_send_option = 'No Payment';
            }
            else if($sms->sms_send_option == "4")
            {
                $sms_send_option = 'In Progress';
            }            
        }
        $doctors = array();
        if(isset($sms))
        {
            $doctors = array();
            if($sms->type == "I")
            {
                $doctor_smss = DoctorSmss::where(['sms_id'=>$sms->id])->get(); 
                foreach($doctor_smss as $doctor_sms)
                {
                    $doctors[]=$doctor_sms->doctor;
                }                         
            }
            
        }
        
        if(isset($sms))
        {
            if($sms->type == "B")
            {   
                $sms_batch_smss = SmsBatchSms::where(['sms_id'=>$sms->id])->get();

                if(isset($sms_batch_smss) && count($sms_batch_smss))
                {
                    foreach($sms_batch_smss as $sms_batch_sms)
                    {
                        $batch = $sms_batch_sms->sms_batch->batch;
                        if(isset($batch) && $batch->fee_type == "Batch")
                        { 
                            if(isset($batch->doctors_courses) && count($batch->doctors_courses))
                            {  
                                foreach($batch->doctors_courses as $doctor_course)
                                {  
                                    if($doctor_course->is_trash == '0')
                                    {
                                        if($sms_send_option == 1)
                                        {
                                            
                                            $doctors[] = $doctor_course->doctor;
                                            
                                        }
                                        else if($sms_send_option != 1)
                                        {
                                            if($doctor_course->payment_status == $sms_send_option)
                                            {
                                                $doctors[] = $doctor_course->doctor;
                                            }
                                            
                                        }

                                    }
                                                                
                                }

                            }
                            

                        }
                        else if(isset($batch) && $batch->fee_type == "Discipline_Or_Faculty")
                        {
                            if(isset($batch->doctors_courses) && count($batch->doctors_courses))
                            {
                                foreach($batch->doctors_courses as $doctor_course)
                                {
                                    if($doctor_course->is_trash == '0')
                                    {
                                        if($doctor_course->institute->type == 1)
                                        {
                                                                                
                                            if(isset($doctor_course->faculty->id) && $sms->faculties->contains('faculty_id',$doctor_course->faculty->id))
                                            {
                                                
                                                if($sms_send_option == 1)
                                                {
                                                    
                                                    $doctors[] = $doctor_course->doctor;
                                                    
                                                }
                                                else if($sms_send_option != 1)
                                                {
                                                    if($doctor_course->payment_status == $sms_send_option)
                                                    {
                                                        $doctors[] = $doctor_course->doctor;
                                                    }
                                                    
                                                }
                                            }

                                            if($doctor_course->institute->id == 16)
                                            {
                                                if(isset($doctor_course->subject->id) && $sms->disciplines->contains('subject_id',$doctor_course->subject->id))
                                                {
                                                    if($sms_send_option == 1)
                                                    {
                                                        
                                                        $doctors[] = $doctor_course->doctor;
                                                        
                                                    }
                                                    else if($sms_send_option != 1)
                                                    {
                                                        if($doctor_course->payment_status == $sms_send_option)
                                                        {
                                                            $doctors[] = $doctor_course->doctor;
                                                        }
                                                        
                                                    }
                                                }
                                            }

                                        }
                                        else
                                        {
                                            if(isset($doctor_course->subject->id) && $sms->disciplines->contains('subject_id',$doctor_course->subject->id))
                                            {
                                                if($sms_send_option == 1)
                                                {
                                                    
                                                    $doctors[] = $doctor_course->doctor;
                                                    
                                                }
                                                else if($sms_send_option != 1)
                                                {
                                                    if($doctor_course->payment_status == $sms_send_option)
                                                    {
                                                        $doctors[] = $doctor_course->doctor;
                                                    }
                                                    
                                                }
                                            }                                        
                                            
                                        }
                                        
                                    }                            
                                }

                            }
                        }
                        
                    }

                }
                
            }
        }

        if(isset($sms))
        {
            if($sms->type == "C")
            {
                $sms_course_smss = SmsCourseSms::where(['sms_id'=>$sms->id])->get();

                if(isset($sms_course_smss) && count($sms_course_smss))
                {
                    foreach($sms_course_smss as $sms_course_sms)
                    {
                        if(isset($sms_course_sms->sms_course))
                        {
                            $doctors_courses = DoctorsCourses::where(['year'=>$sms_course_sms->sms_course->year,'session_id'=>$sms_course_sms->sms_course->session_id,'course_id'=>$sms_course_sms->sms_course->course_id,'is_trash'=>'0'])->get();
                            if(isset($doctors_courses) && count($doctors_courses))
                            {
                                foreach($doctors_courses as $doctor_course)
                                {
                                    if($sms_send_option == 1)
                                    {
                                        
                                        $doctors[] = $doctor_course->doctor;
                                        
                                    }
                                    else if($sms_send_option != 1)
                                    {
                                        if($doctor_course->payment_status == $sms_send_option)
                                        {
                                            $doctors[] = $doctor_course->doctor;
                                        }
                                        
                                    }
                                    
                                }                                
                            }
                        }
                  
                    }

                }
                
            }
        }

        if(isset($sms))
        {
            if($sms->type == "A")
            {
                $sms_year_smss = SmsYearSms::where(['sms_id'=>$sms->id])->get();

                if(isset($sms_year_smss) && count($sms_year_smss))
                {
                    foreach($sms_year_smss as $sms_year_sms)
                    {
                        if(isset($sms_year_sms->sms_year))
                        {
                            $doctors_courses = DoctorsCourses::where(['year'=>$sms_year_sms->sms_year->year,'is_trash'=>'0'])->get();
                            if(isset($doctors_courses) && count($doctors_courses))
                            {
                                foreach($doctors_courses as $doctor_course)
                                {
                                    if($sms_send_option == 1)
                                    {
                                        
                                        $doctors[] = $doctor_course->doctor;
                                        
                                    }
                                    else if($sms_send_option != 1)
                                    {
                                        if($doctor_course->payment_status == $sms_send_option)
                                        {
                                            $doctors[] = $doctor_course->doctor;
                                        }
                                        
                                    }
                                }                                
                            }
                        }
                  
                    }

                }
                
            }
        }
        
        $mobile_numbers = array();
        foreach($doctors as $doctor)
        {
            if(isset($doctor->mobile_number))
            {
                //$mobile_numbers[] = $doctor->mobile_number;
               $this->send_sms($doctor,$sms);
            }
           
        }


        
        // $data['sms'] = $sms->sms;
        // $data['count_mobile_numbers'] = count($mobile_numbers);
        // $data['mobile_numbers'] = $mobile_numbers;

        // $returns = array();
        // $returns = json_decode ($this->send_sms($data));

        // foreach($returns as $return)
        // {
        //     $doctor = Doctor::where('mobile_number', 'like', str_replace("+88","",$return['to']).'%')->orWhere('mobile_number', 'like', str_replace("+","",$return['to']).'%')->orWhere('mobile_number', 'like', $return['to'].'%')->get();
        //     if(isset($doctor) && count($doctor) == 1)
        //     {
        //         SmsLog::insert([
        //             'doctor_id'=>$doctor->id,
        //             'job_id'=>$doctor->id,
        //             'mobile_no'=>$return['to'],
        //             'delivery_status'=>$return['status'],
        //             'event'=>$sms->sms_event->name,
        //             'event_type'=>$sms->sms_event_id,
        //             'admin_id'=>Auth::user()->id,                
        //         ]);
        //     }
            
        // }

        // $sms_send_statuses = array();
        // if(is_array($returns))
        // {   
        //     $k = 0;         
        //     foreach($returns as $return)
        //     {
        //         if (preg_match("/(\d+|\-|\+|\(|\)|\ ){0,}(01)(\d+|\ |\-){8,14}/",$return,$matches))
        //         {
        //             $sms_send_statuses[$k]['mobile_number']=$matches[0];
        //             $sms_send_statuses[$k]['status']=$return;
        //             $k++;
        //         }  
                
        //     }
        // }
    
        Session::flash('message', 'SMS send completed successfully  !!!');        
        return redirect(url('admin/sms-log'));

        // Session::flash('message', 'SMS send completed successfully !!!');        
        // return view('admin.sms.sms_send_status',$data);        
    }



    // public function send_sms($doctor,$sms)
    // {
    //     $postvars = array(
    //         'userID'=>"Genesis",
    //         'passwd'=>"genesisAPI@019",
    //         'sender'=>"8801969901099",
    //         'msisdn'=> '88' .$doctor->mobile_number,
    //         'message'=>urlencode($sms->sms),
    //     );
        
    //     $string = "https://vas.banglalink.net/sendSMS/sendSMS/";

    //     $ch = curl_init();
    //     $url = "https://vas.banglalink.net/sendSMS/sendSMS";
    //     curl_setopt($ch,CURLOPT_URL,$url);
    //     curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
    //     curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
    //     curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
    //     curl_setopt($ch,CURLOPT_TIMEOUT, 20);
    //     $response = curl_exec($ch);
    //     print "curl response is:" . //$response;echo "<pre>";print_r($response);exit;
    //     curl_close ($ch);

    //     $admin_id = Auth::user()->id;
    //     $event_type = $sms->sms_event->id ?? 0;
    //     $event = $sms->sms_event->name ?? '';
    //     $delivery_status = "";

    //     $sms_log = SmsLog::insert([
    //         'doctor_id'=>$doctor->id,
    //         'mobile_no'=>$doctor->mobile_number,
    //         'event_type'=>$event_type,
    //         'event'=>$event,
    //         'delivery_status' => $delivery_status,
    //         'admin_id' => $admin_id
    //     ]);
    //     return $sms_log;

    // }


    

    public function sendSms($doctor,$sms){
        $admin_id = Auth::user()->id;
        $response = null;
        $mob = '88' . Doctors::where('id', $doctor->id)->value('mobile_number');               
        $ch = curl_init();
        $msg = urlencode($sms->sms);   
        
        $msg = str_replace(' ', '%20', $msg);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $response = curl_exec($ch);
        curl_close($ch); 
       
        $response = json_decode( $response, true );
        if( is_array( $response ) && isset( $response['JobId'] ) ) {
            $job_id = $response['JobId'];
            $doctor_id = $doctor->id;

           if($mob != null){
            $mobile_number =preg_replace('/^88/','',$mob);
           }

           $event_type = $sms->sms_event->id ?? 0;
           $event = $sms->sms_event->name ?? 0;
           $delivery_status = "";
           $cUrl = curl_init( );
            curl_setopt( $cUrl, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/GetDelivery?user=genesispg&password=123321@12&jobid=$job_id");
            curl_setopt( $cUrl, CURLOPT_RETURNTRANSFER,1);
            curl_setopt( $cUrl,CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ]
            );

            $response = curl_exec( $cUrl );
            curl_close( $cUrl );
            $response = json_decode( $response, true );

            if( isset( $response['DeliveryReports'] ) && is_array($response['DeliveryReports']) ) {
                $data = $response['DeliveryReports'][0] ?? null;
                if( $data ) {
                    $delivery_status = $data["DeliveryStatus"];
                }
            }

            $created_at = Carbon::now();

            $sms_log = SmsLog::insert([
                'doctor_id'=>$doctor->id,
                'job_id'=>$job_id,
                'mobile_no'=>$mobile_number,
                'event_type'=>$event_type,
                'event'=>$event,
                'delivery_status' => $delivery_status,
                'admin_id' => $admin_id
            ]);
            return $sms_log;
        }
        else return false;        

    }

}
