<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\OnlineLectureAddress;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\DoctorsCourses;
use App\OnlineLectureLink;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class OnlineLectureAddressController extends Controller
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
        $data['online_lecture_addresses'] = OnlineLectureAddress::get();
        $data['module_name'] = 'Online Lecture Address';
        $data['title'] = 'Online Lecture Address List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.online_lecture_address.list',$data);
                
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

        $data['module_name'] = 'Online Lecture Address';
        $data['title'] = 'Online Lecture Address Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.online_lecture_address.create',$data);
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
            'lecture_address' => ['required'],
            'status' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper values!!!');
            return redirect()->action('Admin\OnlineLectureAddressController@create')->withInput();
        }

        if (OnlineLectureAddress::where('name',$request->name)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Name already exists');
            return redirect()->action('Admin\OnlineLectureAddressController@create')->withInput();
        }

        if (OnlineLectureAddress::where('lecture_address',$request->lecture_address)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Lecture Address already exists');
            return redirect()->action('Admin\OnlineLectureAddressController@create')->withInput();
        }
        else{

            $online_lecture_address = new OnlineLectureAddress();
            $online_lecture_address->name = $request->name;
            $online_lecture_address->lecture_address = $request->lecture_address;
            $online_lecture_address->password = $request->password;
            $online_lecture_address->status=$request->status;
            $online_lecture_address->created_by=Auth::id();
            
            if($request->hasFile('pdf')){
                $file = $request->file('pdf');
                $extension = $file->getClientOriginalExtension();
                $filename = date("ymd").'_'.time().'.'.$extension;
                $file->move('pdf/',$filename);
                $online_lecture_address->pdf_file = $filename;
            }
            else {
                $online_lecture_address->pdf_file = '';
            }

            $online_lecture_address->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\OnlineLectureAddressController@index');
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
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['online_lecture_address'] = OnlineLectureAddress::find($id);
        $data['module_name'] = 'Online Lecture Address';
        $data['title'] = 'Online Lecture Address Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.online_lecture_address.edit', $data);
        
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
            'lecture_address' => ['required'],
            'status' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $online_lecture_address = OnlineLectureAddress::find($id);

        if($online_lecture_address->name != $request->name) {

            if (OnlineLectureAddress::where('name', $request->name)->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This name already exists');
                return redirect()->action('Admin\OnlineLectureAddressController@edit', [$id])->withInput();
            }

        }

        if($online_lecture_address->lecture_address != $request->lecture_address) {

            if (OnlineLectureAddress::where('lecture_address', $request->lecture_address)->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This Lecture Address already exists');
                return redirect()->action('Admin\OnlineLectureAddressController@edit', [$id])->withInput();
            }

        }

        $online_lecture_address->name = $request->name;        
        $online_lecture_address->lecture_address = $request->lecture_address;
        $online_lecture_address->password = $request->password;
        $online_lecture_address->status = $request->status;
        $online_lecture_address->updated_by = Auth::id();

        if($request->hasFile('pdf')){
            $file = $request->file('pdf');
            $extension = $file->getClientOriginalExtension();
            $filename = date("ymd").'_'.time().'.'.$extension;
            $file->move('pdf/',$filename);
            $online_lecture_address->pdf_file = $filename;
        }
        

        $online_lecture_address->push();
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
        OnlineLectureAddress::destroy($id); // 1 way
        OnlineLectureLink::where('lecture_address_id',$id)->delete();
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\OnlineLectureAddressController@index');
    }

    public function download_emails($id){

        $online_lecture_address = OnlineLectureAddress::find($id);
        $emails = array();

        foreach($online_lecture_address->lecture_links as $lecture_link){ 
            unset($doctors_courses);
            $doctors_courses = DoctorsCourses::where(['year'=>$lecture_link->year,'session_id'=>$lecture_link->session_id,'institute_id'=>$lecture_link->institute_id,'course_id'=>$lecture_link->course_id,'batch_id'=>$lecture_link->batch_id])->get();
            
            foreach($doctors_courses as $doctor_course){
                $emails[] = $doctor_course->doctor->email;
            }
        }

        $content = implode(',',$emails);
        $file_name = $online_lecture_address->name.'.csv';
        $headers = [
                        'Content-type'        => 'text/csv',
                        'Content-Disposition' => 'attachment; filename='.$file_name,
                ];
            
        return Response::make($content, 200, $headers);
        
    }
}
