<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\Package;
use App\PackageExam;
use App\PackageBatchPackage;
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


class PackageController extends Controller
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
        $data['packages'] = Package::get();
        $data['module_name'] = 'Package';
        $data['title'] = 'Package List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.package.list',$data);
                
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

        $data['exams'] = Exam::where(["sif_only"=>"No"])->pluck('name','id');
        $data['institutes'] = Institutes::pluck('name','id');

        $data['module_name'] = 'Package';
        $data['title'] = 'Package Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.package.create',$data);
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
            'name' => ['required'],
            'amount_bdt' => ['required'],
            'amount_usd' => ['required'],
            'description' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'status' => ['required'],
    
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\PackageController@create')->withInput();
        }        

        if (Package::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Package name already exists');
            return redirect()->action('Admin\PackageController@create')->withInput();
        }
        else{

            $package = new Package();
            $package->name = $request->name;
            $package->description = $request->description;
            $package->amount_bdt = $request->amount_bdt;
            $package->amount_usd = $request->amount_usd;
            $package->start_date = $request->start_date;
            $package->end_date = $request->end_date;
            $package->institute_id = $request->institute_id;
            $package->course_id = $request->course_id;
            $package->faculty_id = $request->faculty_id;
            $package->subject_id = $request->subject_id;
            $package->status=$request->status;
            $package->created_by=Auth::id();
            $package->save();

            if (PackageExam::where('package_id', $package->id)->first()) {
                PackageExam::where('package_id', $package->id)->delete();
            }
    
            if($request->exam_id)
            {
                foreach ($request->exam_id as $key => $value) {
                    if($value=='')continue;
                    PackageExam::insert(['package_id' => $package->id, 'exam_id' => $value]);
                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\PackageController@index');
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
        $package=Package::select('packages.*')->find($id);
        return view('admin.package.show',['package'=>$package]);
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
        $package = Package::find($id);
        $data['package'] = Package::find($id);

        $data['exams'] = Exam::where(["sif_only"=>"No"])->pluck('name','id');

        $selected_exams = array();
        if(count($package->exams)>0){
            foreach($package->exams as $package_exam)
            {
                $selected_exams[] = $package_exam->exam_id;
            }
        }
        
        $data['selected_exams'] = collect($selected_exams);

        $data['institutes'] = Institutes::pluck('name','id');

        $data['courses'] = Courses::where('institute_id',$package->institute_id)->pluck('name', 'id');

        if($package->institute->type==1){
            $data['faculties'] = Faculty::where('course_id',$package->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$package->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$package->course_id)->pluck('name', 'id');
        }

        $data['module_name'] = 'Package';
        $data['title'] = 'Package Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.package.edit', $data);
        
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
        //echo '<pre>';print_r($request->exam_id);exit;
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'amount_bdt' => ['required'],
            'amount_usd' => ['required'],
            'description' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'status' => ['required'],
        
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\PackageController@edit',[$id])->withInput();
        }

        $package = Package::find($id);

        if($package->name != $request->name) {

            if (Package::where(['name'=>$request->name])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Package ame already exists');
                return redirect()->action('Admin\PackageController@edit',[$id])->withInput();
            }

        }

        $package->name = $request->name;
        $package->description = $request->description;
        $package->amount_bdt = $request->amount_bdt;
        $package->amount_usd = $request->amount_usd;
        $package->start_date = $request->start_date;
        $package->end_date = $request->end_date;
        $package->institute_id = $request->institute_id;
        $package->course_id = $request->course_id;
        $package->faculty_id = $request->faculty_id;
        $package->subject_id = $request->subject_id;
        $package->status=$request->status;
        $package->updated_by=Auth::id();
        $package->push();

        if (PackageExam::where('package_id', $package->id)->first()) {
            PackageExam::where('package_id', $package->id)->delete();
        }

        if($request->exam_id)
        {
            foreach ($request->exam_id as $key => $value) {
                if($value=='')continue;
                PackageExam::insert(['package_id' => $package->id, 'exam_id' => $value]);
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
        /*$user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        Package::destroy($id); // 1 way
        if (PackageExam::where('package_id', $id)->first()) {
            PackageExam::where('package_id', $id)->delete();
        }

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\PackageController@index');
    }

    public function download_emails($id){

        $package = Package::find($id);
        $emails = array();

        foreach($package->batches as $batch){
            unset($package_batch_id);
            $lecture_link = PackageLink::where('id',$batch->package_batch_id)->get()[0];
            unset($doctors_courses);
            $doctors_courses = DoctorsCourses::where(['year'=>$lecture_link->year,'session_id'=>$lecture_link->session_id,'institute_id'=>$lecture_link->institute_id,'course_id'=>$lecture_link->course_id,'batch_id'=>$lecture_link->batch_id])->get();
            
            foreach($doctors_courses as $doctor_course){
                $emails[] = $doctor_course->doctor->email;
            }
        }

        $content = implode(',',$emails);
        $file_name = $package->name.'.csv';
        $headers = [
                        'Content-type'        => 'text/csv',
                        'Content-Disposition' => 'attachment; filename='.$file_name,
                ];
            
        return Response::make($content, 200, $headers);
        
    }
}  