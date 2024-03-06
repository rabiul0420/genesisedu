<?php

namespace App\Http\Controllers\Admin;

use App\Batches;
use App\DoctorsCourses;
use App\Exam;
use App\Http\Controllers\Controller;

use App\Courses;

use App\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Doctors;
use App\MedicalColleges;
use App\Divisions;
use App\Districts;
use App\Upazilas;
use App\PaymentInfo;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;
use Validator;
use Yajra\Datatables\Datatables;
use App\DoctorCoursePayment;
use App\PaymentVerificationNote;
use App\Sessions;
use App\SiteSetup;
use App\User;
use Excel;
use App\Exports\PaymentExport;
class PaymentController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Auth::loginUsingId(1);
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $years = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $years[$year] = $year;
        }

        $user=User::find(Auth::id());

        $courses= Courses::get()->pluck('name', 'id');

        if(!$user->hasRole('Payment List')){
        

        return view('admin.payment.list_a',[
            'title'=> 'Genesis Admin : Payment List',
            'batches' => Batches::get()->pluck('name', 'id'), 
            'sessions' => Sessions::get()->pluck('name', 'id'),
            'years' => $years,
            'courses'=>$courses
        ]);

    }
    else{
            return view('admin.payment.list',[
                'title'=> 'Genesis Admin : Payment List',
                'batches' => Batches::get()->pluck('name', 'id'),
                'sessions' => Sessions::get()->pluck('name', 'id'),
                'years' => $years,
                'courses'=>$courses
            ]);
        }
    }

    public function paymentTotal(Request $request)
    {
        return $this->paymentQuery()->sum('amount');
    }

    protected function paymentQuery()
    {
        $request = request();

        $year = $request->year;
        $session_id = $request->session_id;
        $batch_id = $request->batch_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $course_id = $request->course_id;

        $payment_list = DB::table('doctor_course_payment as d1')
            ->leftjoin('doctors_courses as d2', 'd1.doctor_course_id', '=','d2.id')
            ->leftjoin('doctors as d3', 'd2.doctor_id', '=','d3.id')
            ->leftjoin('batches as d4', 'd2.batch_id', '=','d4.id')
            ->leftjoin('users as d5', 'd1.verified_by', '=','d5.id')
            ->leftjoin('users as d8', 'd2.payment_completed_by_id', '=','d8.id')
            ->leftjoin('faculties as d6', 'd2.faculty_id', '=','d6.id')
            ->leftjoin('subjects as d7', 'd2.subject_id', '=','d7.id')
            ->leftjoin('subjects as bs', 'd2.bcps_subject_id', '=','bs.id')
            ->leftjoin('courses as d10', 'd2.course_id', '=','d10.id')
            ->orderBy('id', 'DESC');

        if($year){
            $payment_list = $payment_list->where('d2.year', '=', $year);
        }

        if($course_id){
            $payment_list = $payment_list->where('d2.course_id', '=', $course_id);
        }

        if($session_id){
            $payment_list = $payment_list->where('d2.session_id', '=', $session_id);
        }

        if($batch_id){
            $payment_list = $payment_list->where('d2.batch_id', '=', $batch_id);
        }

        if($start_date && $end_date){
            $payment_list = $payment_list->whereBetween('d1.created_at', [$start_date, $end_date]);
        }
        
        $payment_list = $payment_list->select(
            'd1.id as id',
            'd6.name as faculty',
            'd7.name as subject',
            'bs.name as bcsp_name',
            'd1.created_at as created_at',
            'd1.trans_id as trans_id',
            'd1.payment_verification as payment_verification',
            'd1.amount as amount',
            'd2.reg_no as reg_no',
            'd3.name as doctor_name',
            'd3.id as doctor_id',
            'd3.mobile_number as mobile_number',
            'd4.name as batch_name',
            'd2.payment_status as payment_status',
            'd8.name as user_name',
            'd5.name as verified_by',
            'd10.name as course_name'
        );

        return $payment_list;
    }


    public function payment_list(Request $request)
    {
        $payment_list = $this->paymentQuery();

        return Datatables::of($payment_list)
            ->addColumn('action', function ($payment_list) {
                return view('admin.payment.ajax_list',(['payment_list'=>$payment_list]));
            })

            ->addColumn('created_at', function ($payment_list) {
                return date('d M Y h:m a',strtotime($payment_list->created_at));
            })
            ->make(true);
           
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user=Doctors::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $medical_colleges = MedicalColleges::get()->pluck('name', 'id');
        $divisions = Divisions::get()->pluck('name', 'id');
        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();


        $title = 'SIF Admin : Doctor Create';
        return view('admin.doctors.create',(['title'=>$title,'medical_colleges'=>$medical_colleges,'divisions'=>$divisions]));
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
            'bmdc_no' => ['required'],
            'mobile_number' => ['required'],
            'email' => ['required'],
            /*'date_of_birth' => ['required'],*/
            //'medical_college_id' => ['required'],
            //'gender' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DoctorsController@create')->withInput();
        }

        if (Doctors::where('bmdc_no',$request->bmdc_no)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This BMDC NO  already exists');
            return redirect()->action('Admin\DoctorsController@create')->withInput();
        }

        else{

            $doctor = new Doctors();
            $doctor->name = $request->name;
            $doctor->bmdc_no = $request->bmdc_no;
            $doctor->mobile_number = $request->mobile_number;
            $doctor->main_password = $pass=rand(123456, 987654);
            $doctor->password = Hash::make($pass);
            $doctor->email = $request->email;
            $doctor->date_of_birth = $request->date_of_birth;
            $doctor->gender = $request->gender;
            $doctor->father_name = $request->father_name;
            $doctor->mother_name = $request->mother_name;
            $doctor->spouse_name = $request->spouse_name;
            $doctor->medical_college_id = $request->medical_college_id;
            $doctor->chamber_address = $request->chamber_address;
            $doctor->blood_group = $request->blood_group;
            $doctor->facebook_id = $request->facebook_id;
            $doctor->job_description = $request->job_description;
            $doctor->nid = $request->nid;
            $doctor->passport = $request->passport;
            $doctor->permanent_division_id = $request->permanent_division_id;
            $doctor->permanent_district_id = $request->permanent_district_id;
            $doctor->permanent_upazila_id = $request->permanent_upazila_id;
            $doctor->permanent_address = $request->permanent_address;
            $doctor->present_division_id = $request->present_division_id;
            $doctor->present_district_id = $request->present_district_id;
            $doctor->present_upazila_id = $request->present_upazila_id;
            $doctor->present_address = $request->present_address;
            $doctor->status = $request->status;
            $doctor->created_by = Auth::id();
            if($request->hasFile('photo')){
                $file = $request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename = $doctor->bmdc_no.'_'.time().'.'.$extension;
                $file->move('upload/photo/',$filename);
                $doctor->photo = 'upload/photo/'.$filename;
            }
            else {
                $doctor->photo = '';
            }
            if($request->hasFile('sign')){
                $file = $request->file('sign');
                $extension = $file->getClientOriginalExtension();
                $filename = $doctor->bmdc_no.'_'.time().'.'.$extension;
                $file->move('upload/photo/',$filename);
                $doctor->sign = 'upload/photo/'.$filename;
            }
            else {
                $doctor->sign = '';
            }

            $doctor->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\DoctorsController@index');
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
        
      $data['doctor'] = Doctors::select('doctors.*')->find($id);
      return view('admin.doctors.show',$data);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=Doctors::find(Auth::id());

         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/

        $doctor=Doctors::find($id);

        $medical_colleges = MedicalColleges::get()->pluck('name', 'id');

        $permanent_divisions = Divisions::get()->pluck('name', 'id');

        $permanent_districts = Districts::get()->where('division_id',$doctor->permanent_division_id)->pluck('name', 'id');
        $permanent_upazilas = Upazilas::get()->where('district_id',$doctor->permanent_district_id)->pluck('name', 'id');

        $present_divisions = Divisions::get()->pluck('name', 'id');

        $present_districts = Districts::get()->where('division_id',$doctor->present_division_id)->pluck('name', 'id');
        $present_upazilas = Upazilas::get()->where('district_id',$doctor->present_district_id)->pluck('name', 'id');

        $title = 'SIF Admin : Doctor Edit';

        $array_data = array(
            'doctor'=>$doctor,
            'title'=>$title,
            'medical_colleges'=>$medical_colleges,
            'permanent_divisions'=>$permanent_divisions,
            'permanent_districts'=>$permanent_districts,
            'permanent_upazilas'=>$permanent_upazilas,
            'present_divisions'=>$present_divisions,
            'present_districts'=>$present_districts,
            'present_upazilas'=>$present_upazilas,

        );

        return view('admin.doctors.edit', $array_data);
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
            'bmdc_no' => ['required'],
            'mobile_number' => ['required'],
            'email' => ['required'],
            /*'date_of_birth' => ['required'],*/
            //'medical_college_id' => ['required'],
            //'gender' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DoctorsController@edit',[$id])->withInput();
        }

        $doctor = Doctors::find($id);

        $doctor->name = $request->name;
        $doctor->bmdc_no = $request->bmdc_no;
        $doctor->main_password = $request->password;
        $doctor->password = Hash::make($request->password);
        $doctor->mobile_number = $request->mobile_number;
        $doctor->email = $request->email;
        $doctor->date_of_birth = $request->date_of_birth;
        $doctor->gender = $request->gender;
        $doctor->father_name = $request->father_name;
        $doctor->mother_name = $request->mother_name;
        $doctor->spouse_name = $request->spouse_name;
        $doctor->medical_college_id = $request->medical_college_id;
        $doctor->chamber_address = $request->chamber_address;
        $doctor->blood_group = $request->blood_group;
        $doctor->facebook_id = $request->facebook_id;
        $doctor->job_description = $request->job_description;
        $doctor->nid = $request->nid;
        $doctor->passport = $request->passport;
        $doctor->permanent_division_id = $request->permanent_division_id;
        $doctor->permanent_district_id = $request->permanent_district_id;
        $doctor->permanent_upazila_id = $request->permanent_upazila_id;
        $doctor->permanent_address = $request->permanent_address;
        $doctor->present_division_id = $request->present_division_id;
        $doctor->present_district_id = $request->present_district_id;
        $doctor->present_upazila_id = $request->present_upazila_id;
        $doctor->present_address = $request->present_address;
        $doctor->status = $request->status;

        if($request->hasFile('photo')){
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = $doctor->bmdc_no.'_'.time().'.'.$extension;
            $file->move('upload/photo/',$filename);
            $doctor->photo = 'upload/photo/'.$filename;
        }
        else {
            $doctor->photo = '';
        }
        if($request->hasFile('sign')){
            $file = $request->file('sign');
            $extension = $file->getClientOriginalExtension();
            $filename = $doctor->bmdc_no.'_'.time().'.'.$extension;
            $file->move('upload/photo/',$filename);
            $doctor->sign = 'upload/photo/'.$filename;
        }
        else {
            $doctor->sign = '';
        }

        $doctor->push();

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
        /*$user=Doctors::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        Doctors::destroy($id); // 1 way
        //Doctors::where('id', $id)->update(['is_trash' => 1]);
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DoctorsController@index');
    }

    public function view_course_result($id)
    {

        $data['course_id'] = $id;

        $data['course_reg_no'] = DoctorsCourses::select('*')->where('id', $id)->first();
        $data['results'] = Result::select('*')->where('doctor_course_id', $id)->get();
        return view('admin.doctors.course_result', $data);

    }

    function payment_varification(Request $request){
        $value = $request->value;
        $id = $request->id;
        $note = $request->note;
        
        $payment = DoctorCoursePayment::find( $id );        

        if( !$payment ) {
            return response( [ 'changed' => false ] );
        }

        $changed = $payment->payment_verification != $value;

        if( $changed ) {

            $payment->payment_verification = $value;
            $payment->verified_by = Auth::id();
            $payment->save();

            $PaymentVerificationNote = new PaymentVerificationNote();
            $PaymentVerificationNote->course_payment_id = $payment->id;
            $PaymentVerificationNote->note = $note;
            $PaymentVerificationNote->verified = $value;
            $PaymentVerificationNote->verified_by = Auth::id();
            $PaymentVerificationNote->save();

        }

        return response([ 'payment' => $payment, 'id' => $id, 'changed' => $changed ]);


    }

    public function print_payment_list($payment_verification_id)
    {
        $data['payment_verification_note'] = PaymentVerificationNote::with('doctor_course_payment.course_info.batch','doctor_course_payment.course_info.doctor','doctor_course_payment.course_info.course','doctor_course_payment.course_info.session')
        ->where(['course_payment_id'=>$payment_verification_id])->get();
        $data['other_info']=$data['payment_verification_note'][0]->doctor_course_payment->course_info ?? null;

        return view('admin.payment.print',$data);
    }


    function payment_excel($paras=null)
    {
        session(['paras'=>$paras]);
        $params_array = explode('_',$paras);
        // return $params_array[0];
        $payment_infos =  DoctorCoursePayment::with([
            'doctor_course'
            ,'doctor_course.doctor'
            ,'doctor_course.batch'
        ])->whereHas('doctor_course', function ($query) use ($params_array) {
            $query->where('year', $params_array[0]);
            $query->where('session_id', $params_array[1]);
            $query->where('batch_id', $params_array[2]);
        })->get();

        $array=[];
            foreach($payment_infos as $index=>$payment_info){
                  
                $array[] = [
                    'id' => $payment_info->id,
                    'doctor_name' => $payment_info->doctor_course->doctor->name ?? ' ',
                    'name' => $payment_info->doctor_course->batch->name ?? ' ',
                    'reg_no' => $payment_info->doctor_course->reg_no ?? ' ',
                    'mobile_number' => $payment_info->doctor_course->doctor->mobile_number ?? '',
                    'trans_id' => $payment_info->trans_id ?? ' ',
                    'amount' => $payment_info->amount ?? ' ',
                    'date' => $payment_info->created_at ?? ' ',
                ]; 
            }
    
            
        return Excel::download(new PaymentExport($array), 'download.xlsx');

    }


}

// $doctors = Doctors::whereYear('created_at', $year)->get();

// $array = [];

// foreach($doctors as $doctor){
//     $array[] = [
//         'name'  => $doctor->name,
//         'bmdc'  => $doctor->bmdc_no,
//         'phone' => $doctor->mobile_number,
//         'email' => $doctor->email,
//     ];
// }