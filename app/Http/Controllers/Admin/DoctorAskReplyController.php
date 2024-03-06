<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Doctors;
use App\DoctorAsk;
use App\DoctorAsks;
use App\DoctorAskReply;
use App\DoctorAskBatchDoctorAsk;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Branch;
use App\CourseSessions;
use App\DoctorsCourses;
use App\ExamBatch;
use App\LectureVideo;
use App\User;
use Illuminate\Support\Carbon;
use Session;
use Auth;
use Illuminate\Contracts\Session\Session as SessionSession;
use Validator;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class DoctorAskReplyController extends Controller
{

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
        $data['doctor_ask_replies'] = DoctorAskReply::get();
        $data['module_name'] = 'Doctor Ask Reply';
        $data['title'] = 'Doctor Ask Reply List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['courses']= Courses::get()->pluck('name', 'id');
        return view('admin.doctor_ask_reply.list',$data);

    }

    function doctor_question_feedback(Request $request){
        $value = $request->value;
        $id = $request->id;

        $doctorAsk = DoctorAsk::find( $id );

        if( !$doctorAsk ) {
            return response( [ 'changed' => false ] );
        }

        $changed = $doctorAsk->has_feedback != $value;

        if( $changed ) {
            $doctorAsk->has_feedback = $value;
            $doctorAsk->save();
        }

        return response([ 'doctor_ask' => $doctorAsk, 'id' => $id, 'changed' => $changed ]);

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

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');

        $data['institutes'] = Institutes::pluck('name', 'id');
        $data['branches'] = Branch::pluck('name','id');

        $data['doctor_asks'] = DoctorAsk::pluck('name', 'id');

        $data['module_name'] = 'Doctor Ask Reply';
        $data['title'] = 'Doctor Ask Reply Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.doctor_ask_reply.create',$data);
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
            'year' => ['required'],
            'session_id' => ['required'],
            'branch_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'batch_id' => ['required'],
            'lecture_video_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DoctorAskReplyController@create')->withInput();
        }

        if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
        {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\DoctorAskReplyController@create')->withInput();

        }

        if (DoctorAskReply::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\DoctorAskReplyController@create')->withInput();
        }
        else{

            $doctor_ask_reply = new DoctorAskReply();
                        
            $doctor_ask_reply->year = $request->year;
            $doctor_ask_reply->session_id = $request->session_id;
            $doctor_ask_reply->branch_id=$request->branch_id;
            $doctor_ask_reply->institute_id=$request->institute_id;
            $doctor_ask_reply->course_id=$request->course_id;
            $doctor_ask_reply->faculty_id=$request->faculty_id;
            $doctor_ask_reply->subject_id=$request->subject_id;
            $doctor_ask_reply->batch_id=$request->batch_id;
            $doctor_ask_reply->status=$request->status;
            $doctor_ask_reply->created_by=Auth::id();
            $doctor_ask_reply->save();

            
            $lecture_video_ids = $request->lecture_video_id;

            if (is_array($lecture_video_ids)) {
                foreach ($lecture_video_ids as $key => $value) {
                        
                        if($value == '')continue;
                    
                        unset($lecture_video_batch_lecture_video);
                        $lecture_video_batch_lecture_video = new DoctorAskBatchDoctorAsk();
                        $lecture_video_batch_lecture_video->lecture_video_batch_id = $doctor_ask_reply->id;
                        $lecture_video_batch_lecture_video->lecture_video_id = $value;
                        //$lecture_video_batch_lecture_video->status = $request->status;
                        //$lecture_video_batch_lecture_video->created_by = Auth::id();
                        $lecture_video_batch_lecture_video->save();
                        
                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\DoctorAskReplyController@index');
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
        //$doctor_ask_reply=DoctorAskReply::select('doctor_ask_reply.*')->find($id);
        //return view('admin.doctor_ask_reply.show',['doctor_ask_reply'=>$doctor_ask_reply]);
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
        $doctor_ask_reply = DoctorAskReply::find($id);
        $data['doctor_ask_reply'] = DoctorAskReply::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['doctor_asks'] = DoctorAsk::pluck('name', 'id');

        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$doctor_ask_reply->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$doctor_ask_reply->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$doctor_ask_reply->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$doctor_ask_reply->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$doctor_ask_reply->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::where('institute_id',$doctor_ask_reply->institute_id)
            ->where('course_id',$doctor_ask_reply->course_id)
            ->where('branch_id',$doctor_ask_reply->branch_id)
            ->pluck('name', 'id');
        
        
        $data['lecture_videos'] = DoctorAsk::where(['institute_id'=>$doctor_ask_reply->institute_id,'course_id'=>$doctor_ask_reply->course_id])->pluck('name', 'id');
        $selected_videos = array();
        foreach($doctor_ask_reply->lecture_videos as $lecture_video)
        {
            $selected_videos[] = $lecture_video->lecture_video_id;
        }        

        $data['selected_videos'] = collect($selected_videos);
                
        $data['module_name'] = 'Doctor Ask Reply';
        $data['title'] = 'Doctor Ask Reply Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.doctor_ask_reply.edit', $data);
        
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
            'year' => ['required'],
            'session_id' => ['required'],
            'branch_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'batch_id' => ['required'],
            'lecture_video_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $doctor_ask_reply = DoctorAskReply::find($id);

        if($doctor_ask_reply->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\DoctorAskReplyController@edit',[$id])->withInput();

            }

        }

        if($doctor_ask_reply->year != $request->year || $doctor_ask_reply->session_id != $request->session_id || $doctor_ask_reply->branch_id != $request->branch_id || $doctor_ask_reply->institute_id != $request->institute_id || $doctor_ask_reply->course_id != $request->course_id || $doctor_ask_reply->batch_id != $request->batch_id) {

            if (DoctorAskReply::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\DoctorAskReplyController@edit',[$id])->withInput();
            }

        }

        $doctor_ask_reply->year = $request->year;
        $doctor_ask_reply->session_id = $request->session_id;
        $doctor_ask_reply->branch_id=$request->branch_id;
        $doctor_ask_reply->institute_id=$request->institute_id;
        $doctor_ask_reply->course_id=$request->course_id;
        $doctor_ask_reply->faculty_id=$request->faculty_id;
        $doctor_ask_reply->subject_id=$request->subject_id;
        $doctor_ask_reply->batch_id=$request->batch_id;
        $doctor_ask_reply->status=$request->status;
        $doctor_ask_reply->updated_by=Auth::id();
        $doctor_ask_reply->push();


        $lecture_video_ids = $request->lecture_video_id;

        if (is_array($lecture_video_ids)) {

            if(DoctorAskBatchDoctorAsk::where('lecture_video_batch_id',$doctor_ask_reply->id)->first())
            {
                DoctorAskBatchDoctorAsk::where('lecture_video_batch_id',$doctor_ask_reply->id)->delete();       
            }
            foreach ($lecture_video_ids as $key => $value) {
                    
                    if($value == '')continue;
                
                    unset($lecture_video_batch_lecture_video);
                    $lecture_video_batch_lecture_video = new DoctorAskBatchDoctorAsk();
                    $lecture_video_batch_lecture_video->lecture_video_batch_id = $doctor_ask_reply->id;
                    $lecture_video_batch_lecture_video->lecture_video_id = $value;
                    //$lecture_video_batch_lecture_video->status = $request->status;
                    //$lecture_video_batch_lecture_video->created_by = Auth::id();
                    $lecture_video_batch_lecture_video->save();
                    
            }
        }
        Session::flash('message', 'Record has been updated successfully');
        return back();
    }

    public function doctors_questions()
    {
        // $data['doc_info'] = Doctors::where('id', Auth::id())->first();
        // $data['question_info'] = DoctorAsks::where('doctor_id', Auth::id())->get();
        $data['module_name'] = 'Doctors Questions';
        $data['title'] = 'Doctors Questions List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';
        $data['doctors_questions'] = DoctorAsks::with('doctor','doctor_course','doctor_course.course', 'doctor_course.batch','videoname')->get();
//        $data['doctors_questions'] = DoctorAsks::get();
        $data['years'] = Batches::pluck('year' , 'id');
        $data['courses'] = Courses::pluck('name' , 'id');
        $data['batches'] = Batches::pluck( 'name', 'id' );
        $data['sessions'] = Sessions::pluck('name' , 'id');
        $data['lecture_video'] = LectureVideo:: pluck('name' , 'id');


//        dd( $data['batches'] );

        // $years = range(2017 , date("Y"));
        $years = array(''=>'Select year');
        for($year = date("Y");$year>=2017;$year--){
            $years[$year] = $year;
        }
        $data['years'] = $years;
        return view('admin.doctor_ask_reply.doctors_questions', $data);
    }


    public function view_conversation($id)
    {        

    //    if(DoctorAskReply::where('doctor_ask_id',$id)->count()>0){
       if(DoctorAskReply::where('doctor_ask_id',$id)->first()){


            $data['module_name'] = 'Doctor Ask Reply';
            $data['title'] = 'Doctor Ask Reply';
            $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
            $data['submit_value'] = 'Submit';

            $doctor_ask_reply = DoctorAskReply::where(['doctor_ask_id'=>$id])->first();
            $doctor_ask_reply->is_read = 'Yes';
            $doctor_ask_reply->push();        
        
            $data['doctor_ask_reply'] = $doctor_ask_reply;        
            $data['user'] = User::find(Auth::id());
            $data['doctor_ask_replies'] = DoctorAskReply::where('doctor_ask_id',$id)->get();
            return view('admin.doctor_ask_reply.view_conversation', $data);
        }
        else
        {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'The doctor did not ask any question');
            return redirect()->action('Admin\DoctorAskReplyController@doctors_questions');        
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reply_conversation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required'],
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return redirect()->action('Admin\DoctorAskReplyController@view_conversation',[$request->doctor_ask_id])->withInput();
        }

        $doctor_ask_reply = new DoctorAskReply();
        
        $doctor_ask_reply->doctor_id = $request->doctor_id;
        $doctor_ask_reply->user_id = Auth::id();
        $doctor_ask_reply->message_by = 'admin';
        $doctor_ask_reply->message = $request->message;
        $doctor_ask_reply->doctor_ask_id = $request->doctor_ask_id;
        $doctor_ask_reply->is_read = 'No';

        $doctor_ask_reply->save();

        Session::flash('message', 'Record has been added successfully');
        return redirect()->action('Admin\DoctorAskReplyController@view_conversation',[$request->doctor_ask_id]);
    }

    public function doctor_questions_print( Request  $request ){


        $lecture_name = LectureVideo::where( 'id', $request->lecture_video )->value( 'name' );
        $start_date = Carbon::make( $request->start_date )->format('d,F Y');
        $end_date = Carbon::make( $request->end_date )->format('d,F Y');


        $complain_list = DoctorAsk::with('replies')
            ->leftjoin( 'doctors as d', 'doctor_asks.doctor_id', '=', 'd.id' )
            ->leftjoin( 'doctors_courses as dc', 'doctor_asks.doctor_course_id', '=', 'dc.id' )
            ->leftjoin( 'courses as c', 'c.id', '=', 'dc.course_id' )
            ->leftjoin( 'batches as b', 'b.id', '=', 'dc.batch_id' )
            ->leftjoin( 'sessions as s', 's.id', '=', 'dc.session_id' )
            ->leftjoin( 'lecture_video as lv', 'lv.id', '=', 'doctor_asks.lecture_video_id' )
            ->select(
                'doctor_asks.id as id',
                'doctor_asks.id as doctor_ask_id',
                'd.name as doctor_name',
                'c.name as course_name',
                'b.name as batch_name',
                's.name as session_name',
                'lv.name as lecture_video',
                'dc.year as year'
            );

        $complain_list = $this->complain_list_filter( $complain_list, $request, 'doctor_asks' )->get();


        return view('admin.doctor_ask_reply.print', compact('complain_list', 'lecture_name', 'start_date', 'end_date') );
    }

    public function complain_list_filter(&$doctor_ask_reply, Request $request, $doctor_ask_as = 'da' ){

        if( $request->year ){
            $doctor_ask_reply->where( 'dc.year', '=', $request->year);
        }

        if( $request->course_id ){
            $doctor_ask_reply->where('dc.course_id', '=', $request->course_id );
        }

        if( $request->batch_id ){
            $doctor_ask_reply->where('dc.batch_id', '=', $request->batch_id );
        }

        if( $request->session_id ){
            $doctor_ask_reply->where('s.id', '=',  $request->session_id);
        }

        if($faculty_id = $request->faculty_id) {
            $doctor_ask_reply->where('dc.faculty_id', $faculty_id);
        }

        if($subject_id = $request->subject_id) {
            $doctor_ask_reply->where('dc.subject_id', $subject_id);
        }

        if(  $request->lecture_video ){
            $doctor_ask_reply->where('lv.id', '=', $request->lecture_video);
        }

        if( $request->start_date && $request->end_date ){
            $doctor_ask_reply
                ->havingRaw('( SELECT COUNT(*) FROM  doctor_ask_reply WHERE DATE_FORMAT(created_at, "%Y-%m-%d") BETWEEN "' . $request->start_date . '" AND  "' . $request->end_date . '" AND doctor_ask_id = '.$doctor_ask_as.'.id ) > 0');
//            $doctor_ask_reply->whereBetween( $doctor_ask_as.'.created_at', [ $request->start_date, $request->end_date ]);
        }

        return $doctor_ask_reply;

    }

    public function doctors_complain_list( Request $request )
    {

        $doctor_ask_reply = DB::table( 'doctor_asks as da' )
            ->leftjoin( 'doctors as d', 'da.doctor_id', '=', 'd.id' )
            ->leftjoin( 'doctors_courses as dc', 'da.doctor_course_id', '=', 'dc.id' )
            ->leftjoin( 'courses as c', 'c.id', '=', 'dc.course_id' )
            ->leftjoin( 'batches as b', 'b.id', '=', 'dc.batch_id' )
            ->leftjoin( 'sessions as s', 's.id', '=', 'dc.session_id' )
            ->leftjoin( 'lecture_video as lv', 'lv.id', '=', 'da.lecture_video_id' )
            ->leftjoin( 'teachers as t', 'lv.teacher_id', '=', 't.id' )
           ->rightJoin( 'doctor_ask_reply as dar','dar.doctor_ask_id','=','da.id');

        $doctor_ask_reply->select(
            'da.id as doctor_ask_id',
            'd.name as doctor_name',
            'c.name as course_name',
            'b.name as batch_name',
            's.name as session_name',
            'lv.name as lecture_video',
            't.tec_name as teacher_name',
            'da.created_at as date_and_time',
            'da.has_feedback as has_feedback',
            'dc.year as year'
        );

        $doctor_ask_reply = $this->complain_list_filter( $doctor_ask_reply, $request );

        return Datatables::of( $doctor_ask_reply )
            ->addColumn('action', function ( $doctor_ask_reply ) {
                return' <a href="view-conversation/' . $doctor_ask_reply->doctor_ask_id . '" class="btn btn-xs btn-primary">View Conversation</a> ';
            })
            ->rawColumns([ 'action' ])
            ->make(true);
    }

    public function html()
    {
        return $this->builder()->columns([
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ])->parameters([
                'dom' => 'Bfrtip',
                'buttons' => [ 'csv', 'excel', 'pdf', 'print', 'reset', 'reload' ],
            ]);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DoctorAskReply::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DoctorAskReplyController@index');

    }

     function complain($paras=null)
     {

         // session(['paras'=>$paras]);

         // $params_array = explode('_',$paras);

         // $batch = Batches::where('id',$params_array[2])->value('name');

         // $file_name = str_replace(' ', '_', $batch).'_'.$params_array[0];

         // return (new UsersExport(1))->download($file_name.'.xlsx');
         //return Excel::download(new UsersExport, 'users.xlsx');
     }
}