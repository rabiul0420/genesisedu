<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Faculties;
use App\Batches;
use App\Branch;
use App\Sessions;
use App\DoctorsCourses;
use App\BatchFacultyFee;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BatchFacultyFeeController extends Controller
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
        $data['batch_faculty_fees'] = BatchFacultyFee::get();
        $data['title'] = 'Batch Faculty Fee List';
        return view('admin.batch_faculty_fee.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    public function batch_faculty_fees_list() {
        $batch_faculty_fees_list = DB::table('batch_faculty_fees as d1' )
        ->leftjoin('batches as d2', 'd1.batch_id', '=','d2.id')
        ->leftjoin('faculties as d3', 'd1.faculty_id', '=','d3.id');

        $batch_faculty_fees_list->select(
            'd1.id as id',
            'd2.name as batch_name',
            'd3.name as faculty_name',
            'd1.admission_fee as admission_fee',
            'd1.lecture_sheet_fee as lecture_sheet_fee',
            'd1.discount_from_regular as discount_from_regular',
            'd1.discount_from_exam as discount_from_exam',
        );
        return DataTables::of($batch_faculty_fees_list)
            ->addColumn('action', function ($batch_faculty_fees_list) {
                return view('admin.batch_faculty_fee.ajax_list',(['batch_faculty_fees_list'=>$batch_faculty_fees_list]));
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
       // $user=Faculties::find(Auth::id());
        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['batches'] = Batches::where('fee_type','Discipline_Or_Faculty')->pluck('name', 'id');

        //$data['faculties'] = Faculties::where('institute_id','4')->pluck('name', 'id');

        //echo '<pre>';print_r($data['faculties']);exit;

        $data['title'] = 'Batch Faculty Fee Create';

        return view('admin.batch_faculty_fee.create',$data);
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

            'batch_id' => ['required'],
            'faculty_id' => ['required'],
            'admission_fee' => ['required'],

        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter valid data!!!');
            return redirect()->action('Admin\BatchFacultyFeeController@create')->withInput();
        }

        if (BatchFacultyFee::where(['batch_id'=>$request->batch_id, 'faculty_id'=>$request->faculty_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Batch & Faculty already exists');
            return redirect()->action('Admin\BatchFacultyFeeController@create')->withInput();
        }
        else{

            $batch_faculty_fee = new BatchFacultyFee();
            $batch_faculty_fee->batch_id = $request->batch_id;
            $batch_faculty_fee->faculty_id = $request->faculty_id;
            $batch_faculty_fee->admission_fee = $request->admission_fee;
            $batch_faculty_fee->lecture_sheet_fee = $request->lecture_sheet_fee;
            $batch_faculty_fee->discount_from_regular = $request->discount_from_regular;
            $batch_faculty_fee->discount_from_exam = $request->discount_from_exam;
            
            $batch_faculty_fee->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\BatchFacultyFeeController@index');
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
        $batch_faculty_fee = BatchFacultyFee::select('users.*')->find($id);
        return view('admin.batch_faculty_fee.show',['batch_faculty_fee'=>$batch_faculty_fee]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=Faculties::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data = $this->editing_data($id);
        return view('admin.batch_faculty_fee.edit', $data);

        // $batch_faculty_fee = BatchFacultyFee::find($id);
        // $data['batch_faculty_fee'] = BatchFacultyFee::find($id);
        // $data['batches'] = Batches::where(['course_id' => $batch_faculty_fee->batch->course->id, 'fee_type'=>'Discipline_Or_Faculty'])->pluck('name', 'id');
        // $data['faculties'] = Faculty::where('course_id', $batch_faculty_fee->batch->course->id)->pluck('name', 'id');
        // $data['title'] = 'Batch Faculty Fee Edit';
        // return view('admin.batch_faculty_fee.edit', $data);
        
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

            'batch_id' => ['required'],
            'faculty_id' => ['required'],
            'admission_fee' => ['required'],

        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter valid data!!!');
            return redirect()->action('Admin\BatchFacultyFeeController@edit',[$id])->withInput();
        }
                
        $batch_faculty_fee = BatchFacultyFee::find($id);

        if($batch_faculty_fee->batch_id != $request->batch_id || $batch_faculty_fee->faculty_id != $request->faculty_id) {

            if (BatchFacultyFee::where(['batch_id'=>$request->batch_id, 'faculty_id'=>$request->faculty_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch & Faculty already exists');
                return redirect()->action('Admin\BatchFacultyFeeController@edit',[$id])->withInput();
            }

        }

        $batch_faculty_fee->batch_id = $request->batch_id;
        $batch_faculty_fee->faculty_id = $request->faculty_id;
        $batch_faculty_fee->admission_fee = $request->admission_fee;
        $batch_faculty_fee->lecture_sheet_fee = $request->lecture_sheet_fee;
        $batch_faculty_fee->discount_from_regular = $request->discount_from_regular;
        $batch_faculty_fee->discount_from_exam = $request->discount_from_exam;
        
        $batch_faculty_fee->push();        

        Session::flash('message', 'Record has been updated successfully');
        return back();
    }

    
    public function print_batch_doctor_address()
    {  
        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');

        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['branches'] = Branch::pluck('name','id');

        $data['module_name'] = 'Batch Doctors Address';
        $data['title'] = 'Batch Faculty Fee Doctors Address Print';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.batch_faculty_fee.batch_doctor_address',$data);
        
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
        return view('admin.batch_faculty_fee.batch_doctors_addresses_print',$data);
        
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$user=Faculties::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        BatchFacultyFee::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\BatchFacultyFeeController@index');
    }

    public function duplicate($id){
       $data = $this->editing_data($id);
       $data[ 'action'] = 'duplicate';
       return view('admin.batch_faculty_fee.edit', $data);
    }

    protected function editing_data($id){

        $batch_faculty_fee = BatchFacultyFee::with('batch')->find($id);
        $data['batch_faculty_fee'] = $batch_faculty_fee;
        $data['batches'] = Batches::where(['course_id' => $batch_faculty_fee->batch->course->id, 'fee_type'=>'Discipline_Or_Faculty'])->pluck('name', 'id');
        $data['faculties'] = Faculty::where('course_id', $batch_faculty_fee->batch->course->id)->pluck('name', 'id');
        $data['title'] = 'Batch Faculty Fee Duplicate';
        return $data;

    }
}