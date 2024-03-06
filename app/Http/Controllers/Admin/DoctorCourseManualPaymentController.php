<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\DoctorCourseManualPaymentAssign;
use App\DoctorCourseManualPaymentLink;
use App\DoctorCourseManualPayment;
use App\DoctorCourseManualPaymentDiscipline;
use App\DoctorCourseManualPaymentFaculty;
use App\DoctorCourseManualPaymentBatchDoctorCourseManualPayment;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\DoctorCourseManualPaymentContent;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Branch;
use App\Branches;
use App\CourseYear;
use App\CourseYearSession;
use App\DoctorCoursePayment;
use App\LectureSheet;
use App\LectureVideo;
use App\Location;
use App\Locations;
use App\ScheduleMediaType;
use App\ScheduleDoctorCourseManualPaymentType;
use App\ScheduleProgramType;
use App\Teacher;
use App\Topic;
use App\TopicContent;
use Session;
use Auth;
use Illuminate\Support\Collection;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;


class DoctorCourseManualPaymentController extends Controller
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

        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Collection::make([]);
        $data['years'] = Collection::make([]);
        $data['sessions'] = Collection::make([]);

        $data['total_manual_payment'] = DoctorCourseManualPayment::sum('amount');
        
        $data['module_name'] = 'DoctorCourseManualPayment';
        $data['title'] = 'DoctorCourseManualPayment List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.doctor_course_manual_payment.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function doctor_course_manual_payment_validate($payment_id)
    {
      /*  if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['payment'] = DoctorCourseManualPayment::where(['id'=>$payment_id])->update(['payment_validated'=>'1','payment_validated_by'=>Auth::id()]);

        return redirect('admin/doctor-course-manual-payment');
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function doctor_course_manual_payment_list(Request $request)
    {        
        // $doctor_course_manual_payment_list = DB::table('doctor_course_manual_payment as d1')->whereNull('d1.deleted_at')->join('doctors_courses as d2','d2.id','d1.doctor_course_id')->where('d2.status','1')->where('is_trash','0');
        // $doctor_course_manual_payment_list = $doctor_course_manual_payment_list->select('d1.*','d2.reg_no','d2.institute_id','d2.course_id','d2.year','d2.session_id','d2.payment_status','d2.is_trash');
        
        // if($request->institute_id)
        // {
        //     $doctor_course_manual_payment_list->where('d2.institute_id',$request->institute_id);
        // }

        // if($request->course_id)
        // {
        //     $doctor_course_manual_payment_list->where('d2.course_id',$request->course_id);
        // }

        // if($request->year)
        // {
        //     $doctor_course_manual_payment_list->where('d2.year',$request->year);
        // }

        // if($request->session_id)
        // {
        //     $doctor_course_manual_payment_list->where('d2.session_id',$request->session_id);
        // }

        // $doctor_course_manual_payment_list->orderBy('d1.id','desc');

        $doctor_course_manual_payment_list = DoctorCourseManualPayment::with(['doctor_course','doctor_course.institute','doctor_course.course','doctor_course.session'])->whereHas('doctor_course',function($doctor_course) use($request){
                $doctor_course->where(['is_trash'=>'0','status'=>'1']);
                if($request->year)
                {
                    $doctor_course->where('year' , $request->year);
                }
        })->whereHas('doctor_course.institute',function($institute) use($request){
            if($request->institute_id)
            {
                $institute->where('id' , $request->institute_id);
            }            
        })->whereHas('doctor_course.course',function($course) use($request){
            if($request->course_id)
            {
                $course->where('id' , $request->course_id);
            }            
        })->whereHas('doctor_course.session',function($session) use($request){
            if($request->session_id)
            {
                $session->where('id' , $request->session_id);
            }            
        })
        ->orderBy('doctor_course_manual_payment.id','desc');//->get();

        if($request->payment_validated)
        {
            $doctor_course_manual_payment_list = $doctor_course_manual_payment_list->get();
            $doctor_course_manual_payment_list = $doctor_course_manual_payment_list->filter(function ($item) use($request) {

                if($request->payment_validated == "Yes")
                {
                    return $item->check_payment_validated();
                }
                else if($request->payment_validated == "No")
                {
                    return $item->check_payment_validated() !== true;
                }
                else
                {
                    return true;
                }
            });
        }

        return Datatables::of($doctor_course_manual_payment_list)
            ->addColumn('payment_validated', function ($doctor_course_manual_payment_list) {
                if($doctor_course_manual_payment_list->check_payment_validated() )
                {
                    return 'Payment Validated';
                }
                else 
                {
                    return '';
                }
            })
            ->addColumn('action', function ($doctor_course_manual_payment_list) {
                $data['doctor_course_manual_payment_list'] = $doctor_course_manual_payment_list;
                if($doctor_course_manual_payment_list->check_payment_validated())
                {
                    $data['payment_validated'] = true;
                }
                else 
                {
                    $data['payment_validated'] = false;
                }               
                return view('admin.doctor_course_manual_payment.doctor_course_manual_payment_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }
    
    public function institute_change_in_manual_payment(Request $request)
    {
        $institute = Institutes::where(['id'=>$request->institute_id,'status'=>'1'])->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id,'status'=>'1'])->pluck('name','id');

        $view_name = $request->view_name;
        
        return  json_encode(array('course'=>view('admin.doctor_course_manual_payment.ajax.'.$view_name,$data)->render()), JSON_FORCE_OBJECT);

    }

    public function course_change_in_manual_payment(Request $request)
    {
        $years = CourseYear::where('course_id',$request->course_id)->distinct()->orderBy('year','desc')->pluck('year');

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
        
        return  json_encode(array('year'=>view('admin.doctor_course_manual_payment.ajax.'.$view_name,$data)->render()), JSON_FORCE_OBJECT);

    }

    public function year_change_in_manual_payment(Request $request)
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
        
        return  json_encode(array('session'=>view('admin.doctor_course_manual_payment.ajax.'.$view_name,$data)->render()), JSON_FORCE_OBJECT);

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
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');
        $data['module_name'] = 'DoctorCourseManualPayment';
        $data['title'] = 'DoctorCourseManualPayment Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.doctor_course_manual_payment.create',$data);
        //echo "DoctorCourseManualPayment create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
            'branch_id' => ['required'],
            'location_id' => ['required'],
            'floor' => ['required'],
            'capacity' => ['required'],
        ]);
    }

    public function check_request($request)
    {
        $room = DoctorCourseManualPayment::where(['name'=>$request->name,'branch_id'=>$request->branch_id,'location_id'=>$request->location_id,'floor'=>$request->floor])->first();
        if(isset($room))
        {
            return true;
        }
        else
        {
            return false;
        }
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
            return redirect()->action('Admin\DoctorCourseManualPaymentController@create')->withInput();
        }        

        if ($this->check_request($request)){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This record already exists');
            return redirect()->action('Admin\DoctorCourseManualPaymentController@create')->withInput();
        }
        else
        {

            $room = new DoctorCourseManualPayment();
            $room->name = $request->name;
            $room->branch_id = $request->branch_id;
            $room->location_id = $request->location_id;
            $room->floor = $request->floor;
            $room->capacity = $request->capacity;
            $room->status = $request->status;
            $room->created_by = Auth::id();
            $room->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\DoctorCourseManualPaymentController@index');
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
        $data['room']=DoctorCourseManualPayment::find($id);
        return view('admin.doctor_course_manual_payment.show',$data);
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
        $data['room'] = DoctorCourseManualPayment::find($id);
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        $data['locations'] = Location::where(['status'=>'1'])->pluck('name','id');

        $data['module_name'] = 'DoctorCourseManualPayment';
        $data['title'] = 'DoctorCourseManualPayment Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.doctor_course_manual_payment.edit', $data);
        
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

        $room = DoctorCourseManualPayment::find($id);

        if( $room->name != $request->name || $room->branch_id != $request->branch_id || $room->location_id != $request->location_id || $room->floor != $request->floor ) {

            if ($this->check_request($request)){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This record already exists');
                return redirect()->action('Admin\DoctorCourseManualPaymentController@edit',[$id])->withInput();
            }

        }
        
        $room->name = $request->name;
        $room->branch_id = $request->branch_id;
        $room->location_id = $request->location_id;
        $room->floor = $request->floor;
        $room->capacity = $request->capacity;
        $room->status = $request->status;
        $room->status = $request->status;
        $room->updated_by=Auth::id();
        $room->push();

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
        DoctorCourseManualPayment::destroy($id); // 1 way

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DoctorCourseManualPaymentController@index');
    }
}  