<?php

namespace App\Http\Controllers\Admin;
use App\DoctorsCourses;
use App\Exam;
use App\Http\Controllers\Controller;

use App\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Doctors;
use App\MedicalColleges;
use App\Divisions;
use App\Districts;
use App\Exports\DoctorsExport;
use App\Profile_Edit_History;
use App\SendSms;
use App\Sessions;
use App\Upazilas;
use App\User;
use App\SmsLog;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Yajra\Datatables\Datatables;


class DoctorsController extends Controller
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
        //$this->middleware('auth');
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
        $title = 'Genesis Admin : Doctors List';
        
        $medical_colleges = MedicalColleges::pluck('name', 'id');
        $years = array(''=>'Select year');
        for($year = date("Y");$year>=2017;$year--){
            $years[$year] = $year;
        }

        return view('admin.doctors.list', [
            'title' => $title,
            'medical_colleges' => $medical_colleges,
            'years' => $years,
            'vip' => request()->vip ?? false,
            'verified' => request()->verified ?? false,
        ]);
    }


    public function doctors_list(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $doctors_list = DB::table('doctors as d1' )
            ->leftjoin('medical_colleges as d3', 'd3.id', '=','d1.medical_college_id' );
        // $doctors_list = Doctors::with('doctorcourses', 'medicalcolleges')->select('*');

        // $mecial_college_id = $request->medical_college_id;

        if($start_date && $end_date){
            $doctors_list = $doctors_list->whereBetween('d1.created_at', [$start_date, $end_date]);
        }
        // if($mecial_college_id)
        // {
        //     $doctors_list = $doctors_list->where('medical_college_id' , $mecial_college_id);
        // }

        if($request->vip) {
            $doctors_list->whereNotNull('d1.vip');
        }

        if($request->verified == 'yes') {
            $doctors_list->where('d1.is_verified', 'yes');
        }

        if($request->verified == 'no') {
            $doctors_list
                ->where(function ($query) {
                    $query->where('d1.is_verified', '!=', 'yes')
                        ->orWhereNull('d1.is_verified');
                });
        }

        //$doctors_list->groupBy('d1.id');
        $doctors_list->select(
            'd1.id as id' , 
            'd1.name as name' ,
            'd1.is_verified as is_verified' ,
            'd1.vip as vip' ,
            'd1.email as email' ,
            'd1.bmdc_no as bmdc_no' ,
            'd3.name as medical_college' ,
            'd1.mobile_number as mobile_number' ,
            'd1.status as status' ,
            'd1.main_password as main_password' ,
            'd1.created_at as created_at' ,
            'd1.updated_at as updated_at' ,
            DB::raw('(select count(*) from doctors_courses where doctor_id = d1.id and doctors_courses.is_trash = 0 ) as total_courses' ),
        );
        // dd($doctors_list->get());
        return Datatables::of($doctors_list)
            ->addColumn('action', function ($doctors_list) use ($request) {
                return view('admin.doctors.ajax_list', [
                    'doctors_list'  => $doctors_list,
                    'vip'           => $request->vip ?? false,
                ]);
            })
            
            ->addColumn( 'name', function ($doctors_list) {
                $icon = $doctors_list->is_verified == 'yes' ? '<img src="/img/check.png" alt="" style="width:16px;height:16px; margin-right: 4px;">' : '';

                if(!Auth::user()->can('Go To Doctor Profile')) {
                    return "
                        <div style='display:block; text-align:left; min-width: max-content;'>
                            {$icon}{$doctors_list->name}
                        </div>
                    ";
                }

                $url = route('go-to-doctor-profile', $doctors_list->id);
                $doctor_name = strlen($doctors_list->name) ? $doctors_list->name : 'N/A';

                return "
                    <a title='{$doctor_name}' href='{$url}' target='_blank' style='display:block; text-align:left; min-width: max-content;'>
                        {$icon}{$doctor_name}
                    </a>
                ";
            })

            ->addColumn('status', function ($doctors_list) {
                return ($doctors_list->status==1)?'Active':'InActive' ;
                
            })
            
            ->addColumn('updated_at', function ($doctors_list) {
                return  date('d M Y',strtotime($doctors_list->created_at));
                
            })

            ->rawColumns(['action', 'name'])

            ->make(true);
    }


    public function create()
    {
        // $user=Doctors::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $medical_colleges = MedicalColleges::get()->pluck('name', 'id');
        $divisions = Divisions::get()->pluck('name', 'id');
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

            'is_verified' =>['required'],
            'status' => ['required'],
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:11|min:11|unique:doctors', 
            'email' => 'required|string|email|max:255|unique:doctors',
            'bmdc_no' => 'required|string|min:5|max:7|unique:doctors',
            //  'password' => 'required|string|min:6|confirmed',
        ]);


       
        // if ($validator->mobile_number == 7){
        //     Session::flash('class', 'alert-danger');
        //     session()->flash('message','Please enter proper input values!!!');
        //     return redirect()->action('Admin\DoctorsController@create')->withInput();
        // }

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
            $doctor->bmdc_no ='A' .$request->bmdc_no;
            $doctor->is_verified= $request->is_verified;

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

            if($doctor){
                $this->sendMessage($doctor);
            }

            if($doctor->is_verified =="yes"){
                $this->sendSms($doctor);
            }

            // $smsLog->set_response( $response, $doctor)->set_event('Discount')->save( );
            
            Session::flash('message', 'Record has been added successfully');

            //return back();
            // exit;
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
         $user=User::find(Auth::id());

        //  if(!$user->hasRole('Admin')){
        //      return abort(404);
        //  }

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

        if($user->hasRole('Call Center')){
            return view('admin.doctors.call_center_edit', $array_data);
        }

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
 
        $doctor=Doctors::find($id);
        $changed = [
            'is_verified' =>['required'],
            'status' => ['required'],
            'name' => 'required|string|max:255',
        ];
        if( $doctor->bmdc_no != $request->bmdc_no){
            $changed['bmdc_no'] = 'required|string|min:5|max:8|unique:doctors';
        }
        if( $doctor->email != $request->email){
            $changed['email'] = 'required|string|email|max:255|unique:doctors';
        }
        if( $doctor->mobile_number != $request->mobile_number){
            $changed['mobile_number'] = 'required|string|max:11|min:11|unique:doctors';
        }
        
        $validator = Validator::make($request->all(), $changed,
    
    );

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DoctorsController@edit',[$id])->withInput();
        }

        $doctor = Doctors::find($id);
  
        $is_verified=$doctor->is_verified;

        $doctor->name = $request->name;
        $doctor->bmdc_no =$request->bmdc_no;
        $doctor->main_password = $request->password;
        $doctor->is_verified= $request->is_verified;
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
       
        if($request->hasFile('sign')){
            $file = $request->file('sign');
            $extension = $file->getClientOriginalExtension();
            $filename = $doctor->bmdc_no.'_'.time().'.'.$extension;
            $file->move('upload/photo/',$filename);
            $doctor->sign = 'upload/photo/'.$filename;
        }
       
        $doctor->push();



        if($doctor->is_verified != $is_verified && $doctor->is_verified =="yes"){
            
       
            $this->sendSms($doctor);
           
        }

        Session::flash('message', 'Record has been updated successfully');

        return back();

    }
    public function update_by_call_center(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bmdc_no' => ['required'],
            'mobile_number' => ['required'],
            'email' => ['required'],
            // 'password' => ['password'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DoctorsController@index',[$request->id])->withInput();
        }
        
        
        $doctor = Doctors::find($request->id);


        $previous_bmdc_no = $doctor->bmdc_no;
        $previous_mobile_number = $doctor->mobile_number;
        $previous_email = $doctor->email;
        $previous_main_password = $doctor->main_password;

        $doctor->bmdc_no = $request->bmdc_no;
        $doctor->password = Hash::make($request->password);
        $doctor->main_password = $request->password;
        $doctor->mobile_number = $request->mobile_number;
        $doctor->email = $request->email;
        $doctor->push();

        $edit_profile = new Profile_Edit_History();
        $edit_profile->doctor_id = $request->id;
        
        $edit_profile->bmdc_no = $previous_bmdc_no != $request->bmdc_no ? 
        $previous_bmdc_no . ' - ' . $request->bmdc_no : '';

        $edit_profile->mobile_number = $previous_mobile_number != $request->mobile_number ? 
        $previous_mobile_number . ' - ' . $request->mobile_number : '';

        $edit_profile->email = $previous_email != $request->email ? 
        $previous_email . ' - ' . $request->email : '';

        $edit_profile->password = $previous_main_password != $request->password ? 
        $previous_main_password . ' - ' . $request->password : '';
        
        if( !$doctor->bmdc_no && !$doctor->email && !$doctor->mobile_number && !$doctor->main_password ) {
                Session::flash('class', 'alert-danger');
                Session::flash('message', 'Nothing changed');
            return redirect()->action('Admin\DoctorsController@index');
        }

        $edit_profile->updated_at =Carbon::now();
        $edit_profile->updated_by =Auth::id();
        $edit_profile->save();
        
        Session::flash('message', 'Record has been added successfully');
        return redirect()->action('Admin\DoctorsController@index');
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

        //Doctors::destroy($id); // 1 way
        //Doctors::where('id', $id)->update(['is_trash' => 1]);
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DoctorsController@index');
    }

    public function view_course_result($id)
    {
        $doctor_course = DoctorsCourses::findOrFail($id);

        $data['doctor_course'] = $doctor_course->load([
            'doctor:id,name,mobile_number,bmdc_no',
            'institute:id,name'
        ]);

        $data['results'] = Result::query()
            ->with([
                'doctor_course',
                'subject:id,name',
                'batch:id,name',
                'exam:id,name,year,created_at',
            ])
            ->where('doctor_course_id', $doctor_course->id)
            ->get();

        return view('admin.doctors_courses.all_result', $data);
    }

    public function view_course_result_print($id)
    {
        $data['doctor_course'] = DoctorsCourses::findOrFail($id);
        $data['course_reg_no'] = DoctorsCourses::with('doctor')->select('*')->where('id', $data['doctor_course']->id)->first();
        $data['results'] = Result::with('doctor_course')->select('*')->where('doctor_course_id', $data['doctor_course']->id)->get();
        return view('admin.doctors.course_result_print', $data);
    }

    public function doctorsExcelExport($year)
    {

        $doctors = Doctors::whereYear('created_at', $year)->get();

        $array = [];
        
        foreach($doctors as $doctor){
            $array[] = [
                'name'      => $doctor->name ?? '',
                'bmdc'      => $doctor->bmdc_no ?? '',
                'phone'     => $doctor->mobile_number ?? '',
                'email'     => $doctor->email ?? '',
                'medical'   => $doctor->medical_college->name ?? '',
            ];
        }

        return Excel::download(new DoctorsExport($array), 'download.xlsx');
    }

    public function doctorsExcel($paras)
    {
        $date_array = explode('_',$paras);

        $doctors = Doctors::query()
            ->whereBetween('created_at', [$date_array[0], $date_array[1]])
            ->when(request()->verified == 'yes', function ($query) {
                $query->where('is_verified', request()->verified);
            })
            ->when(request()->verified == 'no', function ($query) {
                $query->where(function ($query) {
                    $query->where('is_verified', '!=', 'yes')
                        ->orWhereNull('is_verified');
                });
            })
            ->get();

        foreach($doctors as $doctor){
            $array[] = [
                'name'      => $doctor->name,
                'bmdc'      => $doctor->bmdc_no,
                'phone'     => $doctor->mobile_number,
                'email'     => $doctor->email,
                'medical'   => $doctor->medical_college->name ?? '',
            ];
        }
        return Excel::download(new DoctorsExport($array), 'download.xlsx');
    }


    
    public function sendSms($doctor){
        $admin_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;
        $mob = '88' . Doctors::where('id', $doctor->id)->value('mobile_number');               
        $ch = curl_init();
        $msg = 'Dear Doctor, Your Profile is Verified . You will find " Verification Mark " beside your name . Website : Genesisedu.info . Thank you.';   
     
        //$msg = str_replace(' ', '%20', $msg);
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctor->id,$mob,$admin_id)->set_event('BMDC Verified')->save();
        $this->send_custom_sms($doctor,$msg,'BMDC Verified',$isAdmin = true); 

    }

    protected function sendMessage( $doctor ) {
        $admin_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;
        
        $websitename = 'https://www.genesisedu.info/';
        $mob = '88' . $doctor->mobile_number;

        $msg = 'Dear Doctor, Welcome to ' .$websitename. '. Your user ID '.$doctor->bmdc_no.' and Password ' .$doctor->main_password. ' Thank you. Stay safe. '. $websitename;

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctor->id,$mob,$admin_id)->set_event('Registration (Office)')->save();
        $this->send_custom_sms($doctor,$msg,'Registration (Office)',$isAdmin = true);
    }

}
