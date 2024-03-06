<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\DoctorComplain;
use App\DoctorQuestion;
use App\DoctorQuestionReply;
use App\Doctors;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;
use Validator;
use Yajra\Datatables\Datatables;


class DoctorQuestionController extends Controller
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

        /*  if(!$user->hasRole('Admin')){
              return abort(404);
          }*/
        $data['title'] = 'Doctor Question List';
        $data['question_info'] = DoctorQuestion::where('status', '!=', 0)->orderBy('id', 'DESC')->get();
        return view('admin.doctor_question.list', $data );
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reply' => ['required'],
            /*'description' => ['required'],*/
            'question_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\DoctorQuestionController@edit')->withInput();
        }

            $reply = new DoctorQuestionReply();
            $reply->question_id = $request->question_id;
            $reply->reply_by = $request->reply_by;
            $reply->reply = $request->reply;
            $reply->status = 1;            
            $reply->save();

            $question = DoctorQuestion::find($request->question_id);
            $question->status = 2;
            $question->push();
        
            Session::flash('message', 'Reply has been added successfully');
            //return redirect()->action('Admin\ComplainController@index');
            return back();
        
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

        $question_id=DoctorQuestion::find($id);
        
        $doctor_name = Doctors::select('name')->where('id', $question_id->doctor_id)->first();

        $question_replied = DoctorQuestionReply::where('question_id', $question_id->id)->orderBy('id', 'ASC')->get();

        $title = 'Question Reply';

        $array_data = array(
            'question_id'=>$question_id,
            'question_replied'=>$question_replied,
            'title'=>$title,
            'doctor_name'=>$doctor_name,
        );
        //echo $array_data['complain_id'];
        return view('admin.doctor_question.edit', $array_data);
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
            'medical_college_id' => ['required'],
            'gender' => ['required'],
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



}