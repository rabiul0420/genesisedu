<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\ReferenceCourse;
use App\ReferenceFaculty;
use App\ReferenceInstitute;
use App\ReferenceSession;
use App\ReferenceSubject;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_type;
use App\Institutes;
use App\Question;
use App\QuestionReferenceExam;

use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class QuestionReferenceExamController extends Controller
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
        $data['question_reference_exams'] = QuestionReferenceExam::with('institute','course','faculty','subject','session')->get();
        $data['module_name'] = 'Question Source';
        $data['title'] = 'Question Source List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.question_reference_exam.list',$data);
                
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
        
        $data['reference_institutes'] = ReferenceInstitute::pluck('name', 'id');
        $data['exam_types'] = Exam_type::pluck('name', 'id');

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2000;$year--){
            $data['years'][$year] = $year;
        }
        
        $data['module_name'] = 'Question Source';
        $data['title'] = 'Question Source Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.question_reference_exam.create',$data);
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
            'institute_id' => ['required'],
            'course_id' => ['required'],
            //'exam_type_id' => ['required'],
            'year' => ['required'],
    
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\QuestionReferenceExamController@create')->withInput();
        }
        
        // $course = ReferenceCourse::where(['id'=>$request->course_id])->first();
        // if(isset($course->type) && $course->type == 1 && (QuestionReferenceExam::where(['institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'faculty_id'=>$request->faculty_id,'year'=>$request->year])->first()))
        // {
        //     Session::flash('class', 'alert-danger');
        //     session()->flash('message','This Question Source already exists');
        //     return redirect()->action('Admin\QuestionReferenceExamController@create')->withInput();

        // }else if ((QuestionReferenceExam::where(['institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'subject_id'=>$request->subject_id,'session_id'=>$request->session_id,'year'=>$request->year])->first()) ){
        //     Session::flash('class', 'alert-danger');
        //     session()->flash('message','This Question Source already exists');
        //     return redirect()->action('Admin\QuestionReferenceExamController@create')->withInput();
        // }
        //else{

            $reference_code = '';

            $institute_code = ReferenceInstitute::where('id',$request->institute_id)->value('reference_code');
            $course_code = ReferenceCourse::where('id',$request->course_id)->value('reference_code');
            $faculty_code = ReferenceFaculty::where('id',$request->faculty_id)->value('reference_code');
            $subject_code = ReferenceSubject::where('id',$request->subject_id)->value('reference_code');
            $session_code = ReferenceSession::where('id',$request->session_id)->value('reference_code');
            $year = substr($request->year, -2);

            $reference_code = $institute_code.'-'.$course_code;
            if(isset($faculty_code)){
                $reference_code.='-'.$faculty_code;
            }
            if(isset($subject_code)){
                $reference_code.='-'.$subject_code;
            }
            if(isset($session_code)){
                $reference_code.='-'.$year.$session_code;
            }
            else
            {
                $reference_code.='-'.$year;
            }

            if (QuestionReferenceExam::where(['reference_code'=>$reference_code])->first()){
                    Session::flash('class', 'alert-danger');
                    session()->flash('message','This Question Source already exists');
                    return redirect()->action('Admin\QuestionReferenceExamController@create')->withInput();
            }

            $question_reference_exam = new QuestionReferenceExam();
            $question_reference_exam->institute_id = $request->institute_id;
            $question_reference_exam->course_id = $request->course_id;
            $question_reference_exam->faculty_id = $request->faculty_id;
            $question_reference_exam->subject_id = $request->subject_id;
            $question_reference_exam->session_id = $request->session_id;
            $question_reference_exam->year = $request->year;
            $question_reference_exam->exam_type_id = $request->exam_type_id;
            $question_reference_exam->reference_code = $reference_code;
            $question_reference_exam->status=$request->status;
            $question_reference_exam->created_by=Auth::id();
            $question_reference_exam->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\QuestionReferenceExamController@index');
        //}
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question_reference_exam=QuestionReferenceExam::select('ques_references.*')->find($id);
        return view('admin.question_reference_exam.show',['question_reference_exam'=>$question_reference_exam]);
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
        $question_reference_exam = QuestionReferenceExam::find($id);
        $data['question_reference_exam'] = QuestionReferenceExam::find($id);

        $data['reference_institutes'] = ReferenceInstitute::pluck('name', 'id');
        $data['reference_courses'] = ReferenceCourse::where('institute_id',$question_reference_exam->institute_id)->pluck('name', 'id');
        if(isset($question_reference_exam) && $question_reference_exam->course->type == 1) {
            $data['reference_faculties'] = ReferenceFaculty::where('course_id',$question_reference_exam->course_id)->orderBy('name','asc')->pluck('name', 'id');
        }
        else
        {
            $data['reference_subjects'] = ReferenceSubject::where('course_id',$question_reference_exam->course_id)->orderBy('name','asc')->pluck('name', 'id');
            $data['reference_sessions'] = ReferenceSession::where('course_id',$question_reference_exam->course_id)->pluck('name', 'id');
        }

        $data['exam_types'] = Exam_type::pluck('name', 'id');

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2000;$year--){
            $data['years'][$year] = $year;
        }

        $data['module_name'] = 'Question Source';
        $data['title'] = 'Question Source Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.question_reference_exam.edit', $data);
        
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
            'institute_id' => ['required'],
            'course_id' => ['required'],
            //'exam_type_id' => ['required'],
            'year' => ['required'],
        
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $question_reference_exam = QuestionReferenceExam::find($id);

        $reference_code = '';

        $institute_code = ReferenceInstitute::where('id',$request->institute_id)->value('reference_code');
        $course_code = ReferenceCourse::where('id',$request->course_id)->value('reference_code');
        $faculty_code = ReferenceFaculty::where('id',$request->faculty_id)->value('reference_code');
        $subject_code = ReferenceSubject::where('id',$request->subject_id)->value('reference_code');
        $session_code = ReferenceSession::where('id',$request->session_id)->value('reference_code');
        $year = substr($request->year, -2);

        $reference_code = $institute_code.'-'.$course_code;
        if(isset($faculty_code)){
            $reference_code.='-'.$faculty_code;
        }
        if(isset($subject_code)){
            $reference_code.='-'.$subject_code;
        }
        if(isset($session_code)){
            $reference_code.='-'.$year.$session_code;
        }
        else
        {
            $reference_code.='-'.$year;
        }

        if($question_reference_exam->institute_id != $request->institute_id || $question_reference_exam->course_id != $request->course_id || $question_reference_exam->faculty_id != $request->faculty_id || $question_reference_exam->subject_id != $request->subject_id || $question_reference_exam->session_id != $request->session_id || $question_reference_exam->year != $request->year) {

            if (QuestionReferenceExam::where(['reference_code'=>$reference_code])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Question Source already exists');
                return redirect()->action('Admin\QuestionReferenceExamController@edit',[$id])->withInput();
            }

        }

        $question_reference_exam->institute_id = $request->institute_id;
        $question_reference_exam->course_id = $request->course_id;
        $question_reference_exam->faculty_id = $request->faculty_id;
        $question_reference_exam->subject_id = $request->subject_id;
        $question_reference_exam->session_id = $request->session_id;
        $question_reference_exam->year = $request->year;
        $question_reference_exam->reference_code = $reference_code;
        $question_reference_exam->status=$request->status;
        $question_reference_exam->updated_by=Auth::id();
        $question_reference_exam->push();

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
        $question_reference_exam = QuestionReferenceExam::find($id);
        $question_reference_exam->deleted_by=Auth::id();
        $question_reference_exam->push();

        QuestionReferenceExam::destroy($id); // 1 way

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\QuestionReferenceExamController@index');
    }

    public function download_emails($id){

        $question_reference_exam = QuestionReferenceExam::find($id);
        $emails = array();

        foreach($question_reference_exam->batches as $batch){
            unset($question_reference_exam_batch_id);
            $lecture_link = QuestionReferenceExamLink::where('id',$batch->question_reference_exam_batch_id)->get()[0];
            unset($doctors_courses);
            $doctors_courses = DoctorsCourses::where(['year'=>$lecture_link->year,'session_id'=>$lecture_link->session_id,'institute_id'=>$lecture_link->institute_id,'course_id'=>$lecture_link->course_id,'batch_id'=>$lecture_link->batch_id])->get();
            
            foreach($doctors_courses as $doctor_course){
                $emails[] = $doctor_course->doctor->email;
            }
        }

        $content = implode(',',$emails);
        $file_name = $question_reference_exam->name.'.csv';
        $headers = [
                        'Content-type'        => 'text/csv',
                        'Content-Disposition' => 'attachment; filename='.$file_name,
                ];
            
        return Response::make($content, 200, $headers);
        
    }
}  