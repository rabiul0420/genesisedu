<?php
namespace App\Http\Controllers\Admin;

use App\BcpsFaculty;
use App\DoctorAnswers;
use App\DoctorExam;
use App\Doctors;
use App\DoctorsCourses;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Sms;
use App\Http\Traits\ContentSelector;
use App\Question_ans;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Sessions;
use App\Topics;
use App\Exam_topic;
use App\QuestionTypes;
use App\Batches;
use App\Question;
use App\Result;
use App\Exam_type;
use App\ExamEdit;
use App\Exports\ResultExport;
use App\Teacher;
use Illuminate\Support\Facades\Schema;
use Session;
use Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Excel;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class ExamController extends Controller
{
    use ContentSelector;

    const EXAM_FILE_ROOT = 'exam_files';


    //
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        ini_set('max_execution_time', 3000);
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        ini_set('memory_limit', '-1');
                
        $EXAM = Exam::with('topic', 'institute', 'course', 'faculty','question_type', 'sessions', 'results');


        if( Auth::user()->isMentor( ) ) {
            $EXAM = $EXAM->where( 'created_by', Auth::id( ) );
        }
        
        $exams = $EXAM->get( );

        foreach($exams as $exam){
            $exam->is_lock = (boolean) count($exam->results);
        }

        $title = 'Exam List';

        return view('admin.exam.list', compact('exams','title'));
    }

    public function exam_list() {
        $exam_list = ExamEdit::with( 'doctor_exams' )->leftjoin('institutes as d2', 'd1.institute_id', '=','d2.id')
        ->leftjoin('courses as d3', 'd1.course_id', '=','d3.id')
        ->leftjoin('topics as d4', 'd1.class_id', '=','d4.id')
        ->leftjoin('sessions as d5', 'd1.session_id', '=','d5.id')
        ->leftjoin('question_types as d6', 'd1.question_type_id', '=','d6.id');

        $exam_list->select(
            'd1.id as id',
            'd1.name as exam_name',
            'd2.name as institutes_name',
            'd3.name as course_name',
            'd4.name as class_name',
            'd5.name as session',
            'd1.sif_only as sif_only',
            'd1.status as status',
            'd6.mcq_number as mcq_number',
            'd6.sba_number as sba_number',
            'd6.sba_number as sba_number',
            'd6.full_mark as full_mark',
            'd6.duration as duration',
        );

        $exam_list = $exam_list->whereNull('d1.deleted_at');

        return DataTables::of($exam_list)
            ->addColumn('action', function ($exam_list) {
                return view('admin.exam.exam_ajax_list',(['exam_list'=>$exam_list,'user' => Auth::user()]));
            })

            ->addColumn('mcq_number',function($exam_list){
                return $exam_list->mcq_number . ' MCQ  / ' . $exam_list->sba_number .' SBA';
            })

            ->addColumn('full_mark',function($exam_list){
                return (isset($exam_list->full_mark)?$exam_list->full_mark:'') . '/' . (isset($exam_list->duration)?$exam_list->duration / 60 :'');
            })

            ->addColumn('status',function($exam_list){
                return '<span style="color:' .( $exam_list->status == 1 ? 'green;':'red;' ).' font-size: 14px;">'
                        . ($exam_list->status == 1 ? 'Active':'Inactive') . '</span>';
            })
            ->rawColumns(['action','mcq_number', 'full_mark', 'status'])

        ->make(true);
    }

    protected function selection_config( )
    {
        return [
            'institutes' => [
                'label_column_count' => 2,
                'column_count' => 10,
                'label' => 'Class Institute',
            ],
            'courses' => [
                'label_column_count' => 2,
                'column_count' => 10,
                'label' => 'Class Course',
            ],
            'sessions' => [
                'label_column_count' => 2,
                'column_count' => 10,
                'label' => 'Class Session',
            ]
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $years = array('' => 'Select Year');
        for ($year = date("Y") + 1; $year >= 2017; $year--) {
            $years[$year] = $year;
        }

        $sessions = Sessions::get()->pluck('name', 'id');
        $papers = array('' => 'Select Paper', '1' => 'Paper-I', '2' => 'Paper-II', '3' => 'Paper-III');
        $question_type = QuestionTypes::get()->pluck('title', 'id');
        $exam_type = Exam_type::get()->pluck('name', 'id');
        $teacher = Teacher::get()->pluck('name', 'id');
        $institute = Institutes::get( )->pluck('name', 'id');
        $batches = Batches::get( )->pluck( 'name', 'id' ) ;
        $hasClassIdCokumn = Schema::hasColumn( 'exam', 'class_id' );



        $title = 'Exam Create';


        return view('admin.exam.create', ([
            'institute' => $institute,
            'institutes_view' => $this->institutes( request( ) )->render( ),
            'courses_view' => $this->courses( request( ) )->render( ),
            'sessions_view' => $this->sessions( request( ) )->render( ),
            'batches' => $batches,
            'years' => $years,
            'sessions' => $sessions,
            'papers' => $papers,
            'exam_type' => $exam_type,
            'teacher' => $teacher,
            'question_type' => $question_type,
            'title' => $title,
            'has_class_id_column' => $hasClassIdCokumn
        ]));

    }

    static $exam_question = null;

    public static function exam_file_item( $question = null, $question_id = null, $exam_question_id = null, &$data = [] ) {


        if( isset($question->question_title) && isset( $question->question_type ) && isset( $question->exam_question_id ) && isset( $question->question_id ) ) {
            $question_id = $question->question_id;
            $exam_question_id = $question->exam_question_id;
            $answers = Question_ans::where( [ 'question_id' => $question->id ] )->get( );

        } else {
            $question = Question::with('question_answers' )
                ->where('question_id', $question_id )
                ->where('exam_question.id', $exam_question_id )
                ->join('exam_question', 'exam_question.question_id', '=','questions.id' )
                ->select(['*', 'exam_question.id as exam_question_id'])->first( );
            $answers = Question_ans::where( [ 'question_id' => $question_id ] )->get( );
        }

        $question_type = $question->question_type;

        $data[ $question_id  ] = [
            'question_id'      =>   (int) $question_id,
            'exam_question_id'  =>  (int) $exam_question_id,
            'question_type'     =>  $question_type,
            'correct_ans_sba'   =>  ( $question_type == 2 || $question_type == 4 ) ? ( $answers[0]->correct_ans ?? '' ) : '',
            'question_title'    =>  $question->question_title,
        ];

        $sls = [ 'A','B','C','D','E','F','G' ];

        foreach( $answers as $i => $row ){
            $data[ $question_id ][ 'question_option' ][] = [
                'option_serial' =>  $row->sl_no,
                'option_title'  =>  $row->answer,
                'correct_ans'   =>  ( $question_type == 1 || $question_type == 3 ) ? $row->correct_ans : '',
            ];
        }

        return $data[ $question_id ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'exam_date' => ['required'],
            'year' => ['required'],
            'session_id' => ['required'],
            'exam_type_id' => ['required'],
            'question_type_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'is_free' => ['required'],
            'sif_only' => ['required'],
            'status' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\ExamController@create')->withInput();
        }

        if (Exam::where( ['name' => $request->name, 'class_id' => $request->class] )->exists()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'This Exam  already exists');
            return redirect()->action('Admin\ExamController@create')->withInput();
        } else {
            $exam = new Exam;
            $exam->name = $request->name;
            $exam->description = $request->description;
            $exam->institute_id = $request->institute_id;
            $exam->course_id = $request->course_id;
            $exam->faculty_id = $request->faculty_id;
            $exam->subject_id = $request->subject_id;
            $exam->batch_id = $request->batch_id;
            $exam->is_free = $request->is_free;
            $exam->exam_details = $request->exam_details;
            $exam->exam_date = $request->exam_date;
            $exam->year = $request->year;
            $exam->session_id = $request->session_id;
            $exam->paper = $request->paper;
            $exam->teacher_id = $request->teacher_id;
            $exam->exam_type_id = $request->exam_type_id;
            $exam->question_type_id = $request->question_type_id;
            $exam->sif_only = $request->sif_only;
            $exam->status=$request->status;

            $question_type = QuestionTypes::find($request->question_type_id);

            if($question_type){
                if(( $question_type->mcq_number > 0 && $request->mcq_question_id == null) || ($question_type->sba_number > 0 && $request->sba_question_id == null) || ( $question_type->mcq2_number > 0 && $request->mcq2_question_id == null )){
                    $exam->status = '2';
                }
            }
            
            $exam->exam_file_link = self::EXAM_FILE_ROOT;
            $exam->created_by = Auth::id();
          
            if( Schema::hasColumn( 'exam', 'class_id' ) ) {
                $exam->class_id = $request->class ?? NULL;
            }

            $exam->save( );

            $data = array();

            if ( $request->mcq_question_id ) {
                foreach ($request->mcq_question_id as $k => $value) {
                    Exam_question::insert(['question_id' => $value, 'exam_id' => $exam->id, 'question_type' => 1]);
                    $exam_question_id = DB::getPdo()->lastInsertId( );

                    self::exam_file_item(NULL, $value, $exam_question_id, $data);

                }
            }

            if ($request->mcq2_question_id && is_array( $request->mcq2_question_id )) {

                foreach ( $request->mcq2_question_id as $k => $value ) {
                    Exam_question::insert([ 'question_id' => $value,  'exam_id' => $exam->id,  'question_type' => 3 ]);
                    $exam_question_id = DB::getPdo()->lastInsertId( );

                    self::exam_file_item(NULL, $value, $exam_question_id, $data);
                }

            }

            if ( $request->sba_question_id ) {
                foreach ($request->sba_question_id as $k => $value) {
                    Exam_question::insert(['question_id' => $value, 'exam_id' => $exam->id, 'question_type' => 2]);
                    $exam_question_id = DB::getPdo()->lastInsertId();

                    self::exam_file_item(NULL, $value, $exam_question_id, $data);

                }
            }


            if ( $request->topic_id ) {
                foreach ($request->topic_id as $k => $value) {
                    Exam_topic::insert(['topic_id' => $value, 'exam_id' => $exam->id]);
                }
            }

            self::save_exam_file( $exam, $data );

//            $file_path = public_path( 'exam_files' );
//
//            if( !is_dir( $file_path ) ) {
//                mkdir( $file_path, 777, true );
//            }
//
//            $my_file = fopen($file_path. '/' . $exam->id . ".json", "w") or die("Unable to open file!");
//            $txt = json_encode($data);
//            fwrite($my_file, $txt);
//            fclose($my_file);

            Session::flash('message', 'Record has been added successfully');
            return redirect()->action('Admin\ExamController@index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Subjects::select('users.*')->find($id);
        return view('admin.subjects.show', ['user' => $user]);
    }



    public function duplicate( $id ){
        $data = $this->edit_data( $id );
        $data['duplicate'] = true;
        return view( 'admin.exam.edit', $data );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $id ){
        $data = $this->edit_data( $id );
        $data['duplicate'] = false;
        return view( 'admin.exam.edit', $data );
    }


    public function edit_data( $id )
    {
        $exam = Exam::with('topic')->find( $id );

        if( old('class' ) ) {
            $exam->topic = Topics::find( old('class' ) );
        }

        $data['exam'] = $exam;
        $years = array('' => 'Select Year');
        for ($year = date("Y") + 1; $year >= 2017; $year--) {
            $years[$year] = $year;
        }
        $data['years'] = $years;
        $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['papers'] = array('' => 'Select Paper', '1' => 'Paper-I', '2' => 'Paper-II', '3' => 'Paper-III');
        $data['question_types'] = QuestionTypes::get()->pluck('title', 'id');
        $data['question_type'] = QuestionTypes::where('id', $data['exam']->question_type_id)->first();


        $data['mcqs_ids'] = Exam_question::where(['exam_id' => $id, 'question_type' => 1])->pluck('question_id');
        $data['mcqs'] = Exam_question::select('questions.id as question_id',DB::raw('CONCAT(question_title, " (", question_id,")") AS question_title'),'exam_question.*')->join('questions','exam_question.question_id','questions.id')->where(['exam_question.exam_id' => $data['exam']->id, 'exam_question.question_type' => 1])->orderBy('exam_question.id','asc')->pluck('question_title','question_id');
        
        $data['sbas_ids'] = Exam_question::where(['exam_id' => $id, 'question_type' => 2])->pluck('question_id');
        $data['sbas'] = Exam_question::select('questions.id as question_id',DB::raw('CONCAT(question_title, " (", question_id,")") AS question_title'),'exam_question.*')->join('questions','exam_question.question_id','questions.id')->where(['exam_question.exam_id' => $data['exam']->id, 'exam_question.question_type' => 2])->orderBy('exam_question.id','asc')->pluck('question_title','question_id');
        
        $data['mcq2s_ids'] = Exam_question::where(['exam_id' => $id, 'question_type' => 3])->pluck('question_id');
        $data['mcq2s'] = Exam_question::select('questions.id as question_id',DB::raw('CONCAT(question_title, " (", question_id,")") AS question_title'),'exam_question.*')->join('questions','exam_question.question_id','questions.id')->where(['exam_question.exam_id' => $data['exam']->id, 'exam_question.question_type' => 3])->orderBy('exam_question.id','asc')->pluck('question_title','question_id');

        $data['exam_type'] = Exam_type::get()->pluck('name', 'id');
        $data['teacher'] = Teacher::get()->pluck('name', 'id');

        $data['topic'] = Topics::where(['course_id' => $data['exam']->course_id, 'status' => 1])->pluck('name', 'id');
        $data['topic_ids'] = Exam_topic::where('exam_id', $id)->pluck('topic_id');


        $data['institute'] = Institutes::get()->pluck('name', 'id');
        $data['institute_type'] = Institutes::where('id', $data['exam']->institute_id)->value('type');
        $data['course'] = Courses::where('institute_id', $data['exam']->institute_id)->pluck('name', 'id');
        if ($data['institute_type'] == 1) {
            $data['faculty'] = Faculty::where('course_id', $data['exam']->course_id)->pluck('name', 'id');
            $data['subject'] = Subjects::where('faculty_id', $data['exam']->faculty_id)->pluck('name', 'id');
        } else {
            $data['subject'] = Subjects::where('course_id', $data['exam']->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::get()->pluck('name', 'id');
        $data['title'] = 'Exam Edit';
        $data['has_class_id_column']  = Schema::hasColumn( 'exam', 'class_id' );


        $institute_id = old('institute_id', $exam->topic->institute_id ?? null);
        $course_id = old('course_id', $exam->topic->course_id ?? null);
        $session_id = old('session_id', $exam->topic->session_id ?? null);
        $year = old('year', $exam->topic->year ?? null);

        $data[ 'institutes_view' ] = $this->institutes( request( ), $institute_id )->render( );
        $data[ 'courses_view' ] = $this->courses( request( ),$course_id,$institute_id )->render( );
        $data[ 'sessions_view' ] = $this->sessions( request( ),$session_id ,$course_id, $year )->render( );

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make( $request->all(), [
            'name' => ['required'],
            'exam_date' => ['required'],
            'year' => ['required'],
            'session_id' => ['required'],
            'exam_type_id' => ['required'],
            'question_type_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'is_free' => ['required'],
            'sif_only' => ['required'],
            'status' => ['required'],
        ] );

        if ( $validator->fails( ) ){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\ExamController@edit',[$id])->withInput();
        }


        $exam = Exam::find($id);

        if ( !( $request->name == $exam->name && $request->class == $exam->class_id ) ) {

            if( Exam::where( [ 'name' => $request->name, 'class_id' => $request->class ] )->exists( ) ) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This Exam already exists');
                return redirect()->back()->withInput();
            }
        }

        $exam->name = $request->name;
        $exam->description = $request->description;
        $exam->institute_id = $request->institute_id;
        $exam->course_id = $request->course_id;
        $exam->faculty_id = $request->faculty_id;
        $exam->subject_id = $request->subject_id;
        $exam->batch_id = $request->batch_id;
        $exam->is_free = $request->is_free;
        $exam->exam_details = $request->exam_details;
        $exam->exam_date = $request->exam_date;
        $exam->year = $request->year;
        $exam->session_id = $request->session_id;
        $exam->paper = $request->paper;
        $exam->teacher_id = $request->teacher_id;
        $exam->exam_type_id = $request->exam_type_id;
        $exam->question_type_id = $request->question_type_id;
        $exam->sif_only = $request->sif_only;
        $exam->status=$request->status;

        $question_type = QuestionTypes::find($request->question_type_id);

        if($question_type){
            if(( $question_type->mcq_number > 0 && $request->mcq_question_id == null) || ($question_type->sba_number > 0 && $request->sba_question_id == null) || ( $question_type->mcq2_number > 0 && $request->mcq2_question_id == null )){
                $exam->status = '2';
            }
        }
        
        $exam->exam_file_link = self::EXAM_FILE_ROOT;

        if( Schema::hasColumn( 'exam', 'class_id' ) ) {
            $exam->class_id = $request->class ?? NULL;
        }

        $data = array();


        $exam->save();

        Exam_topic::where('exam_id', $id)->delete();
        if ($request->topic_id) {
            foreach ($request->topic_id as $k => $value) {
                Exam_topic::insert(['topic_id' => $value, 'exam_id' => $exam->id]);
            }
        }

        Exam_question::where(['exam_id' => $id, 'question_type' => 1])->delete( );
        if ($request->mcq_question_id) {
            foreach ($request->mcq_question_id as $k => $value) {
                Exam_question::insert(['question_id' => $value, 'exam_id' => $id, 'question_type' => 1]);
                $exam_question_id = DB::getPdo()->lastInsertId();

                self::exam_file_item(NULL, $value, $exam_question_id, $data);
            }
        }

        Exam_question::where(['exam_id' => $id, 'question_type' => 3])->delete();
        if ($request->mcq2_question_id && is_array( $request->mcq2_question_id )) {
            foreach ($request->mcq2_question_id as $k => $value) {
                Exam_question::insert(['question_id' => $value, 'exam_id' => $id, 'question_type' => 3]);
                $exam_question_id = DB::getPdo()->lastInsertId();

                self::exam_file_item(NULL, $value, $exam_question_id, $data);
            }
        }

        Exam_question::where(['exam_id' => $id, 'question_type' => 2])->delete();
        if ($request->sba_question_id) {
            foreach ( $request->sba_question_id as $k => $value ) {
                Exam_question::insert(['question_id' => $value, 'exam_id' => $id, 'question_type' => 2]);
                $exam_question_id = DB::getPdo()->lastInsertId();

                self::exam_file_item(NULL, $value, $exam_question_id, $data);
            }
        }


        Exam_question::where(['exam_id' => $id, 'question_type' => 4])->delete( );

        if ( $request->sba2_question_id && is_array( $request->sba2_question_id ) ) {
            foreach ( $request->sba2_question_id as $k => $value ) {
                Exam_question::insert(['question_id' => $value, 'exam_id' => $id, 'question_type' => 4]);
                $exam_question_id = DB::getPdo()->lastInsertId();

                self::exam_file_item(NULL, $value, $exam_question_id, $data);
            }
        }

        self::save_exam_file( $exam, $data );

        Session::flash('message', 'Record has been updated successfully');
        return back();
    }

    public static function  save_exam_file( Exam $exam, $data ){
        if( empty( $exam->exam_file_link ) ) {
            $exam->exam_file_link = self::EXAM_FILE_ROOT;
            $exam->save( );
        }

        $file_path = $exam->exam_file_link;

        if( !preg_match( "/[^\/]+\.json(?=\/$|$)/i", $file_path ) ) {
            $file_path = rtrim( $file_path, '/' ). '/' . $exam->id . '.json';
        }

        $dir_link = rtrim( preg_replace( "/[^\/]+(?=\/$|$)/", '', $file_path ) , '/' );

        //dd( $dir_link );

        if( !is_dir( $dir_link ) ) {
            mkdir( $dir_link, 0777, true );
        }

        //dd( $file_path );

        $my_file = fopen( $file_path, "w" ) or die("Unable to open file!");
        $txt = json_encode( $data );

        //dd( $txt );
        fwrite($my_file, $txt);
        fclose($my_file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       /* $user = Subjects::find(Auth::id());
        if (!$user->hasRole('Admin')) {
            return abort(404);
        }*/
        $exam = Exam::find($id);
        $exam->deleted_by = Auth::id();
        $exam->push();
        Exam::destroy($exam->id);
        // Exam_question::where('exam_id',$id)->delete();
        // Result::where('exam_id',$id)->delete();

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ExamController@index');
    }

    // public function print($id)
    // {
    //     return view('admin.exam.print', ['exam' => Exam::with('exam_questions.question.question_answers', 'question_type', 'exam_topic.topic')->find($id)]);
    // }

    public function print($id)
    {
        $data['exam_id'] = Exam::find($id);
        $data['question_id'] = Exam_question::where('exam_id', $id)->get();
        foreach ($data['question_id'] as $result) {
            $data['questions'][$result->question_title][] = $result;
        }
        $data['exam_id'] = Exam::find($id);
        $data['topic_id'] = Exam_topic::where('exam_id', $id)->get();
        foreach ($data['topic_id'] as $result) {
            $data['topics'][$result->name][] = $result;
        }
        Exam::where('id', $id)
            ->update(['is_print' => 'Yes']);
        $exam_info = Exam::where('id', $id)->first();
        $data['exam_info'] = Exam::where('id', $id)->first();
        $question_type_id = Exam::where('question_type_id', $exam_info->question_type_id)->first();
        $data['question_type_info'] = QuestionTypes::where('id', $question_type_id->question_type_id)->first();
        return view('admin.exam.print', $data);
    }


    public function print_ans($id)
    {

        $data['exam_id'] = Exam::find($id);

        $data['question_id'] = Exam_question::where('exam_id', $id)->get();
        foreach ($data['question_id'] as $result) {
            $data['questions'][$result->question_title][] = $result;
        }

        $exam_info = Exam::where('id', $id)->first();
        $data['exam_info'] = Exam::where('id', $id)->first();
        /*$topic_info = Exam_topic::where('exam_id', $exam_info->id)->first();
        $data['topic_name'] = Topics::where('id', $topic_info->topic_id)->first();*/
        $question_type_id = Exam::where('question_type_id', $exam_info->question_type_id)->first();
        $data['question_type_info'] = QuestionTypes::where('id', $question_type_id->question_type_id)->first();
        return view('admin.exam.print_ans', $data);

    }

    public function print_onlyans($id)
    {

        $data['exam_id'] = Exam::find($id);

        $data['question_id'] = Exam_question::where('exam_id', $id)->get();
        foreach ($data['question_id'] as $result) {
            $data['questions'][$result->question_title][] = $result;
        }

        $exam_info = Exam::where('id', $id)->first();
        $data['exam_info'] = Exam::where('id', $id)->first();
        /*$topic_info = Exam_topic::where('exam_id', $exam_info->id)->first();
        $data['topic_name'] = Topics::where('id', $topic_info->topic_id)->first();*/
        $question_type_id = Exam::where('question_type_id', $exam_info->question_type_id)->first();
        $data['question_type_info'] = QuestionTypes::where('id', $question_type_id->question_type_id)->first();
        return view('admin.exam.print_onlyans', $data);

    }

    public function print_onlyans_for_file($id)
    {

        $data['exam_id'] = Exam::find($id);

//        return
        $questions = Exam_question::with('question.question_answers')->where('exam_id', $id)->get();

        $text = [];

        foreach ( $questions as $exam_question) {
            $answers = $exam_question->question->question_answers ?? [];
            $tfs = '';

            foreach ( $answers as $ans ) {
                $tfs .= $ans->correct_ans;
            }

            $text[] = $tfs;
        }

        return implode('',$text);
//        return strlen('FFFTTTTTTTTTTTTFTTFTTTTTTTTTTTFTTTFTTTFFFTTTFFFFFFTTFFFTTTTFTFFTTTTTTTTTTTTFTTFFTTFFFTFTFFFTFFTTTFFFTTTTTFTTTFTTFFTTFTTTTTTTTTTTTTFFTFTFFTTTFTFTTFTTTFFFFTTTTFFFTTFFTTTTTFFFFFFTTTTTTFTFTTFFTFTTTTFFFFFTTTTTTTTTTTTFFFFTTTTTTTTTTTTTTTTTTTTTTT,TTTTFFFFFTTTFTTTFFFTTFTTTFFFFFTTTTTTFTFTFFFTFFTTFTFFTTFFFFFFFTTTTTFTTTTFTTTTTFTFFTTTTFFFFFTFTTTTTFFTFFTTTTTF>TTFTTTTTFFTFTTTTTTFTTTFTTTTT');

    }


    public function upload_result($id)
    {
       $data['exam_info'] = $exam_id = Exam::find($id);
        $data['title'] = 'Upload Result';
        $institute_type = $exam_id->institute->type;
        if($institute_type==1){
            return view('admin.exam.upload_result_faculty', $data);
        }
        return view('admin.exam.upload_result', $data);

    }


    public function view_result($id)
    {

        $data['batches'] = Batches::get()->pluck('name', 'id');
        // $data['courses']= Courses::get()->pluck('name', 'id');
        // $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $exam = Exam::find($id);//dd($exam->toArray());
        $exam->highest_mark  = Result::where('exam_id' , $id)->orderBy('obtained_mark','desc')->value('obtained_mark');
        $doctor_courses = Result::where('exam_id' , $id)->orderBy('obtained_mark','desc')->get();
        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($doctor_courses as $k=>$row){
            if($obtained_mark!=$row->obtained_mark){
                $p = ($i+1);
                $th = ($p==1)?'st':(($p==2)?'nd':(($p==3)?'rd':'th'));
                $row->possition = $p.$th;
                $i++;
            }else{
                $row->possition = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;

            $row->candidate_possition = $this->candidate_possition($row->doctor_course->candidate_type ?? '',
                Subjects::where(['id'=>$row->subject_id])->value('name'), $id, $row->obtained_mark);

            $row->subject_possition = $this->subject_possition(Subjects::where(['id'=>$row->subject_id])->value('name'),$id,$row->obtained_mark);
            $row->batch_possition = $this->batch_possition($row->batch_id,$id,$row->obtained_mark);
            $row->faculty = BcpsFaculty::where(['id'=>$row->faculty_id])->value('name');
        }

        $data['sessions'] = Sessions::pluck('name','id');
        $data['courses'] = Courses::pluck('name','id');
        $data['batches'] = Batches::where('course_id',$exam->course_id)->pluck('name','id');

        $data['doctor_courses'] = $doctor_courses;
        $data['exam'] = $exam;
        $data['title'] = 'Results';

        $data['paper_faculty'] = QuestionTypes::where('id' , $exam->question_type_id)->value('paper_faculty');

        $data['examination_code']  = Result::where('exam_id' , $id)->value('examination_code');
        $data['candidate_code']  = Result::where('exam_id' , $id)->value('candidate_code');
        return view('admin.exam.view_result', $data);

    }

    public function exam_batch_list(Request $request){
        $year = $request->year;
        $course_id = $request->course_id;
        $session_id = $request->session_id;
        $batch_id = $request->batch_id;
        $exam_id = $request->exam_id;
        $result = DB::table('results as r' )
            ->leftjoin('batches as b', 'r.batch_id', '=','b.id' )
            ->leftjoin('exam as e' , 'r.exam_id', '=','e.id' )
            ->leftjoin('doctors_courses as dc', 'r.doctor_course_id', '=','dc.id')
            ->leftjoin('doctors as d', 'dc.doctor_id', '=','d.id')
            ->leftjoin('subjects as s', 'r.subject_id', '=','s.id')
            ->where('r.exam_id' , $exam_id);

        if($year){
            $result = $result->where('b.year', '=', $year);
        }
        if($course_id){
            $result = $result->where('dc.course_id', '=', $course_id);
        }
        if($session_id){
            $result = $result->where('b.session_id', '=', $session_id);
        }
        if($batch_id){
            $result = $result->where('r.batch_id', '=', $batch_id);
        }

        $results_list = $result->select('r.exam_id as exam_id','e.institute_id as institute_id','dc.candidate_type as candidate_type','r.id as id','d.name as doctor_name' ,'dc.reg_no as reg_no', 'r.batch_id as batch_id', 'b.name as batch_name' , 's.name as discipline_name','r.obtained_mark as obtained_mark','r.wrong_answers as wrong_answer', );

        return DataTables::of($results_list)
        ->addColumn('disciplain_position', function ($result) {

            return $this->subject_possition( $result->discipline_name, $result->exam_id, $result->obtained_mark );
        })
        ->addColumn('batch_position', function ($result) {

            return $this->batch_possition( $result->batch_id, $result->exam_id, $result->obtained_mark );
        })
        ->addColumn('candidate_position', function ($result) {
                return $this->candidate_possition($result->candidate_type, $result->discipline_name, $result->exam_id, $result->obtained_mark );
        })
        ->addColumn('overall_position', function ($result) {
            return $this->overall_possition($result->exam_id, $result->obtained_mark );
        })
        ->make(true);

    }

    function overall_possition($exam_id, $obtained_mark_single){
        $overall_result = Result::where('exam_id' , $exam_id)->orderBy('obtained_mark','desc')->get();
        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($overall_result as $k => $row) {

            if ( $obtained_mark != $row->obtained_mark ) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                $i++;
            } else {
                $pos = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $pos;

            if($row->obtained_mark == $obtained_mark_single){
                return $pos;
            }
        }
    }

    function candidate_possition($candidate_type, $subject_name, $exam_id, $obtained_mark_single)
    {
        $results = Result::with('doctor_course')->join('subjects', 'subjects.id', '=', 'results.subject_id')
            ->where(['subjects.name' => $subject_name,  'exam_id' => $exam_id])->orderBy('obtained_mark', 'desc')->get();
        // dd($results->toArray());

        $subjectresult = $results->where('doctor_course.candidate_type', $candidate_type );
    //    dd($subjectresult->toArray());

        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($subjectresult as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                $i++;
            } else {
                $pos = $possition;
            }

            $obtained_mark = $row->obtained_mark;

            if($row->obtained_mark == $obtained_mark_single){
                return $pos;
            }
        }
    }

    function subject_possition($subject_name,$exam_id,$obtained_mark_single)
    {
        $subjectresult = Result::join('subjects', 'subjects.id', '=', 'results.subject_id')
            ->where(['subjects.name' => $subject_name, 'exam_id' => $exam_id])
        ->orderBy('obtained_mark', 'desc')->get();

        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($subjectresult as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                $i++;
            } else {
                $pos = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;
            if($row->obtained_mark == $obtained_mark_single){
                return $pos;
            }



            /*foreach ($subjectresult as $k=>$single){
                if($single->obtained_mark == $obtained_mark){
                    $sp = ($k+1);
                    $th = ($sp==1)?'st':(($sp==2)?'nd':(($sp==3)?'rd':'th'));
                    return $sp.$th;
                }
            }*/
        }
    }


    function batch_possition($batch_id,$exam_id,$obtained_mark_single)
    {
        $batchresult = Result::where(['batch_id' => $batch_id, 'exam_id' => $exam_id])->orderBy('obtained_mark', 'desc')->get();
        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($batchresult as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                $i++;
            } else {
                $pos = $possition;
            }

            $obtained_mark = $row->obtained_mark;

            if($row->obtained_mark == $obtained_mark_single){
                return $pos;
            }
        }

    }


    public function result_submit(Request $request)
    {
        $file = $request->file('result');
        $answer = $request->file('answer');

        $result = explode("\r\n", trim(file_get_contents($file->getRealPath())));

        $canswertotal = file_get_contents($answer->getRealPath());

        $exam_id = $request->exam_id;
        $question_type_id = Exam::where('id', $exam_id)->first()->question_type_id;
        $exam_year = Exam::where('id', $exam_id)->first()->year;
        $exam = Exam::where('id', $exam_id)->first();
        $question_type = QuestionTypes::where('id', $question_type_id)->first();
        $mcq_mark = $question_type->mcq_mark/5;
        $mcq_negative_mark = $question_type->mcq_negative_mark;
        $sba_mark = $question_type->sba_mark;
        $sba_negative_mark = $question_type->sba_negative_mark;
        $exam_question_ids = Exam_question::where('exam_id', $exam_id)->get();
        $result_insert = 0;

        $paper_faculty = $question_type->paper_faculty;


        //Result File save to result/exam_id/file_name
        $file_name = time()."_".$file->getClientOriginalName();
        $file_Path = 'result/'.$request->exam_id.'/';
        $file->move($file_Path,$file_name);

        for ($i = 0; $i < count($result); $i++) {  //echo 'ok';
            $ind_result = $result[$i];
            $registration =  substr($exam_year,2,2).substr($ind_result, 0, 6);

            $doctors_course = DoctorsCourses::where('reg_no', $registration)->first();

            $doctor_course_id = DoctorsCourses::where('reg_no', $registration)->value('id');
            $batch_id = DoctorsCourses::where('reg_no', $registration)->value('batch_id');
            $course_id = DoctorsCourses::where('reg_no', $registration)->value('course_id');

            $doctor_exam = DoctorExam::where(['doctor_course_id' => $doctor_course_id,'exam_id'=>$request->exam_id])->first() ?? new DoctorExam();
            $doctor_exam->status = 'Completed';
            $doctor_exam->doctor_course_id = $doctor_course_id;
            $doctor_exam->exam_id = $request->exam_id;
            $doctor_exam->save( );

            $omr_subject_code = substr($ind_result, 8, 1);
//            $subject_id = Subjects::where(['course_id'=>$course_id,'subject_omr_code'=> $omr_subject_code])->value('id');

            $set_code = substr($ind_result, 6, 1);


            if($paper_faculty=='Paper'){
                $paper_code = substr($ind_result, 6, 1);
                $faculty_id= '';
            }elseif ($paper_faculty=='Faculty'){
                $omr_faculty_code = substr($ind_result, 7, 1);
//                $faculty_id = BcpsFaculty::where('omr_code', $omr_faculty_code)->value('id');
                $paper_code = '';
            }else{
                $faculty_id= '';
                $paper_code = '';
            }

            $subject_id = $doctors_course->subject_id ?? '';
            $faculty_id = $doctors_course->faculty_id ?? '';

            if($doctor_course_id && !(Result::where(['doctor_course_id'=> $doctor_course_id,'exam_id'=> $exam_id])->exists())){

                $result_insert++;
                if($paper_faculty=='None'){
                    $ind_result = substr($ind_result, 8);
                } else {
                    $ind_result = substr($ind_result, 9);
                }

                $mcqans = substr($ind_result, 0, $question_type->mcq_number * 5);
                $sbaans = substr($ind_result, $question_type->mcq_number * 5, ($question_type->sba_number + $question_type->mcq_number * 5));

                /*doctor result insert batch wise */
                $mark = 0;
                $m = 0;
                $s = 0;
                $wrong_answer = 0;
                $n_mark = 0;

                $total_question = $question_type->mcq_number+$question_type->sba_number;

                $sba_correct_answer = substr($canswertotal,$question_type->mcq_number*5);

                for ($x=0;$x<$total_question;$x++) {


                    if ($question_type->mcq_number > $x) {
                        $y = 0;
                        $asb = substr($mcqans, $m, 5);

                        $correcans = str_split(substr($canswertotal, $m, 5));

                        for ( $j=0;$j<5;$j++ ) {
                            if ($correcans[$j] == substr($asb, $y, 1)) {
                                $mark += $mcq_mark;
                            } else {

                                if(substr($asb, $y, 1)!='.'){
                                    $n_mark += $mcq_negative_mark;
                                    if(substr($asb, $y, 1)!='>'){
                                        $wrong_answer++;
                                    }
                                }
                            }
                            ++$y;
                        }
                        $m +=5;
                    } else {
                        if (substr($sba_correct_answer, $s, 1) == substr($sbaans, $s, 1)) {
                            $mark += $sba_mark;
                        }else{
                            if(substr($sbaans, $s, 1)!='.'){
                                $n_mark += $sba_negative_mark;
                                if(substr($sbaans, $s, 1)!='>'){
                                    $wrong_answer++;
                                }
                            }
                        }
                        $s++;
                    }
                }

                Result::insert([
                    'doctor_course_id' => $doctor_course_id,
                    'exam_id' => $exam_id,
                    'subject_id'=> $subject_id,
                    'correct_mark' => $mark,
                    'negative_mark' => $n_mark,
                    'obtained_mark' => $mark-$n_mark,
                    'obtained_mark_decimal' => ($mark-$n_mark)*100,
                    'wrong_answers'=>$wrong_answer,
                    'batch_id'=>$batch_id,
                    'file_name'=>$file_name,
                    'paper_code'=>$paper_code,
                    'faculty_id'=>$faculty_id
                ]);

                $doctor_course = DoctorsCourses::find( $doctor_course_id );
                $doctor = Doctors::find( $doctor_course->doctor_id );
                $website = 'https://genesisedu.info';
                $sms = "Dear Doctor\nyour {$exam->name} exam result published, please visit" .$website;
                $sms = Sms::init()->setRecipient($doctor->mobile_number )->setText($sms );
                $sms->send();
                $sms->save_log('OMR Result Published', $doctor->id );
            }
        }

        $result_insert = ($result_insert==0)?'No result Added':$result_insert.' results added successfully';
        Session::flash('message', $result_insert);

        return redirect('admin/view-result/' . $exam_id);

    }

    public function result_submit_faculty(Request $request)
    {
        $file_front_part = $request->file('result_front_part');
        $file_back_part = $request->file('result_back_part');
        $file_last_part = $request->file('result_last_part');
        $answer = $request->file('answer');

        $result_front_part = explode("\r\n", file_get_contents($file_front_part->getRealPath()));
        if($file_back_part){
            $result_back_part = explode("\r\n", file_get_contents($file_back_part->getRealPath()));
        }
        if($file_last_part){
            $result_last_part = explode("\r\n", file_get_contents($file_last_part->getRealPath()));
        }

        $canswertotal = file_get_contents($answer->getRealPath());


        //Result File save to result/exam_id/file_name
        $file_name = time()."_".$file_front_part->getClientOriginalName();
        $file_Path = 'result/'.$request->exam_id.'/';
        $file_front_part->move($file_Path,$file_name);

        $exam_id = $request->exam_id;
        $question_type_id = Exam::where('id', $exam_id)->first()->question_type_id;
        $exam_year = Exam::where('id', $exam_id)->first()->year;
        $exam = Exam::where('id', $exam_id)->first();

        $question_type = QuestionTypes::where('id', $question_type_id)->first();
        $exam_question_ids = Exam_question::where('exam_id', $exam_id)->get();

        $mcq_mark = $question_type->mcq_mark/5;
        $mcq_negative_mark = $question_type->mcq_negative_mark;
        $sba_mark = $question_type->sba_mark;
        $sba_negative_mark = $question_type->sba_negative_mark;

        $result_insert = 0;


        for ($i = 0; $i < count($result_front_part); $i++) {  //print_r($result_back_part);exit;
            $ind_result = $result_front_part[$i];
            $registration_without_year = substr($ind_result, 0, 6);
            $registration =  substr($exam_year,2,2).$registration_without_year;

            $doctors_course = DoctorsCourses::where('reg_no', $registration)->first();

            $doctor_course_id = $doctors_course->id ?? 0;
            $course_id = $doctors_course->course_id ?? 0;

            $doctor_exam = DoctorExam::where([ 'doctor_course_id' => $doctor_course_id, 'exam_id'=>$request->exam_id ])->first() ?? new DoctorExam();

            $doctor_exam->doctor_course_id = $doctor_course_id;
            $doctor_exam->exam_id = $request->exam_id;
            $doctor_exam->status = 'Completed';
            $doctor_exam->save();


            $omr_faculty_code = substr($ind_result, 8, 1);
            //$faculty_id = Faculty::where('faculty_omr_code', $omr_faculty_code)->value('id');

            $batch_id = DoctorsCourses::where('reg_no', $registration)->value('batch_id');

            $examination_code = substr($ind_result, 6, 1);
            $set_code = substr($ind_result, 7, 1);

            $candidate_code = substr($ind_result, 9, 1);

            $s_code_10 = substr($ind_result, 10, 1);
            $s_code_11 = substr($ind_result, 11, 1);

            $subject_id = $doctors_course->subject_id ?? '';
            $faculty_id = $doctors_course->faculty_id ?? '';



            $not_result = !Result::where( ['doctor_course_id'=> $doctor_course_id,'exam_id'=> $exam_id ] )->exists( );


//            if($registration_without_year && $doctor_course_id && !(Result::where(['doctor_course_id'=> $doctor_course_id,'exam_id'=> $exam_id])->exists())){

            if( $registration_without_year && $doctor_course_id  && $not_result ){
//                return $doctor_course_id;


//                dd( $doctor_course_id );


                $result_insert++;

                $ind_result = substr($ind_result, 12);

                $input = preg_quote($registration_without_year, '~'); // don't forget to quote input string!

                if(isset($result_back_part)){
                    $ans_back_part = preg_grep('~' . $input . '~', $result_back_part);
                    $ans_back_part = implode("|",$ans_back_part);
                    $ans_back_part = substr($ans_back_part,6);
                    $ind_result = trim($ind_result).trim($ans_back_part);
                }

                if(isset($result_last_part)){
                    $ans_last_part = preg_grep('~' . $input . '~', $result_last_part);
                    $ans_last_part = implode("|",$ans_last_part);
                    $ans_last_part = substr($ans_last_part,6);
                    $ind_result = trim($ind_result).trim($ans_last_part);
                }

                $mcqans = substr($ind_result, 0, $question_type->mcq_number * 5);
                $sbaans = substr($ind_result, $question_type->mcq_number * 5, ($question_type->sba_number + $question_type->mcq_number * 5));

                /*doctor result insert batch wise */
                $mark = 0;
                $m = 0;
                $s = 0;
                $wrong_answer = 0;
                $n_mark = 0;

                $total_question = $question_type->mcq_number+$question_type->sba_number;

                $sba_correct_answer = substr($canswertotal,$question_type->mcq_number*5);

                for ($x=0;$x<$total_question;$x++) {


                    if ($question_type->mcq_number > $x) {
                        $y = 0;
                        $asb = substr($mcqans, $m, 5);

                        $correcans = str_split(substr($canswertotal, $m, 5));

                        for ($j=0;$j<5;$j++) {
                            if ($correcans[$j] == substr($asb, $y, 1)) {
                                $mark += $mcq_mark;
                            }else{

                                if(substr($asb, $y, 1)!='.'){
                                    $n_mark += $mcq_negative_mark;
                                    if(substr($asb, $y, 1)!='>'){
                                        $wrong_answer++;
                                    }

                                }
                            }
                            ++$y;
                        }
                        $m +=5;
                    } else {
                        if (substr($sba_correct_answer, $s, 1) == substr($sbaans, $s, 1)) {
                            $mark += $sba_mark;
                        }else{
                            if(substr($sbaans, $s, 1)!='.'){
                                $n_mark += $sba_negative_mark;
                                if(substr($sbaans, $s, 1)!='>'){
                                    $wrong_answer++;
                                }
                            }
                        }
                        $s++;
                    }
                }

                Result::insert([
                    'doctor_course_id' => $doctor_course_id,
                    'exam_id' => $exam_id,
                    'subject_id'=>$subject_id,
                    'correct_mark' => $mark,
                    'negative_mark' => $n_mark,
                    'obtained_mark' => $mark-$n_mark,
                    'obtained_mark_decimal' => ($mark-$n_mark)*100,
                    'wrong_answers'=>$wrong_answer,
                    'batch_id'=>$batch_id,
                    'file_name'=>$file_name,
                    'faculty_id'=>$faculty_id,
                    'examination_code'=>$examination_code,
                    'candidate_code'=>$candidate_code
                ]);


                $doctor_course = DoctorsCourses::find( $doctor_course_id );

                $doctor = Doctors::find( $doctor_course->doctor_id );
                $website = 'https://genesisedu.info';
                $sms = "Dear Doctor\nyour {$exam->name} exam result published, please visit" .$website;
                $sms = Sms::init()->setRecipient($doctor->mobile_number )->setText($sms );
                $sms->send();
                $sms->save_log('OMR Result Published', $doctor->id );
            }

        }

        $result_insert = ($result_insert==0)?'No result Added':$result_insert.' results added successfully';
        Session::flash('message', $result_insert);


        return redirect('admin/view-result/' . $exam_id);
    }

    public function doctor_batch_exam_reopen($doctor_course_id,$exam_id)
    {
        DoctorExam::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();
        DoctorAnswers::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();
        Result::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();

        echo "Doctor Exam Reopened Successfully. ";
    }

    public function result_excel($paras=null){
        session(['paras'=>$paras]);

        $params_array = explode('_',$paras);

        $batch = Batches::where('id',$params_array[2])->value('name');
        $file_name = str_replace(' ', '_', $batch).'_'.$params_array[0];


        return (new ResultExport(1))->download($file_name.'.xlsx');
    }

    public function resultExcelExport($params)
    {
        $params = json_decode($params);
        
        $year = $params->year ?? '';
        $course_id = $params->course_id ?? '';
        $session_id = $params->session_id ?? '';
        $batch_id = $params->batch_id ?? '';
        $exam_id = $params->exam_id ?? '';

        $string = ' where ( ';
        if(!empty($year))
        {
            $string.=' `b`.`year`="'.$year.'"';
        }
        if(!empty($course_id))
        {
            $string.=' and `dc`.`course_id`="'.$course_id.'"';
        }
        if(!empty($session_id))
        {
            $string.=' and `b`.`session_id`="'.$session_id.'"';
        }
        if(!empty($batch_id))
        {
            $string.=' and `r`.`batch_id`="'.$batch_id.'"';
        }
        if(!empty($exam_id))
        {
            $string.=' and `r`.`exam_id`="'.$exam_id.'"';
        }
        
        $string.=" ) ";


        $raw = 'select `r`.`exam_id` as `exam_id`, `e`.`institute_id` as `institute_id`, `dc`.`candidate_type` as `candidate_type`,`dc`.`course_id`, `r`.`id` as `id`, `d`.`name` as `doctor_name`, `dc`.`reg_no` as `reg_no`, `r`.`batch_id` as `batch_id`, `b`.`name` as `batch_name`, `s`.`name` as `discipline_name`, `r`.`obtained_mark` as `obtained_mark`, `r`.`wrong_answers` as `wrong_answer` from `results` as `r` left join `batches` as `b` on `r`.`batch_id` = `b`.`id` left join `exam` as `e` on `r`.`exam_id` = `e`.`id` left join `doctors_courses` as `dc` on `r`.`doctor_course_id` = `dc`.`id` left join `doctors` as `d` on `dc`.`doctor_id` = `d`.`id` left join `subjects` as `s` on `r`.`subject_id` = `s`.`id` '.$string.' order by `dc`.`id` asc';

        $results = DB::select(DB::raw($raw));
        //echo "<pre>";print_r($string);exit;
        // $results = DB::table('reults as r' )
        //     ->leftjoin('batches as b', 'r.batch_id', '=','b.id' )
        //     ->leftjoin('exam as e' , 'r.exam_id', '=','e.id' )
        //     ->leftjoin('doctors_courses as dc', 'r.doctor_course_id', '=','dc.id')
        //     ->leftjoin('doctors as d', 'dc.doctor_id', '=','d.id')
        //     ->leftjoin('subjects as s', 'r.subject_id', '=','s.id')
        //     ->where('r.exam_id' , $exam_id)->orderby('dc.id','asc');

        //     if($year){
        //         $results = $results->where('`b`.`year`', '=', $year);
        //     }
        //     if($course_id){
        //         $results = $results->where('`dc`.`course_id`', '=', $course_id);
        //     }
        //     if($session_id){
        //         $results = $results->where('`b`.`session_id`', '=', $session_id);
        //     }
        //     if($batch_id){
        //         $results = $results->where('`r`.`batch_id`', '=', $batch_id);
        //     }
            
        //     $results = $results->select('r.exam_id as exam_id','e.institute_id as institute_id','dc.candidate_type as candidate_type','r.id as id','d.name as doctor_name' ,'dc.reg_no as reg_no', 'r.batch_id as batch_id', 'b.name as batch_name' , 's.name as discipline_name','r.obtained_mark as obtained_mark','r.wrong_answers as wrong_answer', )
        //     ->get();

            

        $array = [];
        
        foreach($results as $result){
            $array[] = [
                'reg_no' => $result->reg_no,
                'doctor_name' => $result->doctor_name,
                'batch' => $result->batch_name,
                'discipline' => $result->discipline_name,
                'obtained_mark' => $result->obtained_mark,
                'wrong_answer' => $result->wrong_answer,
                'discipline_position' =>$this->subject_possition( $result->discipline_name, $result->exam_id, $result->obtained_mark ) ,
                'batch_position' => $this->batch_possition( $result->batch_id, $result->exam_id, $result->obtained_mark ),
                'candidate_position' => $this->candidate_possition($result->candidate_type, $result->discipline_name, $result->exam_id, $result->obtained_mark ),
                'overall_position' => $this->overall_possition($result->exam_id, $result->obtained_mark),
            ];
        }

        return Excel::download(new ResultExport($array), 'download.xlsx');
    }
    




}
