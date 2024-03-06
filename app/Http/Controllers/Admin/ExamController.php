<?php

namespace App\Http\Controllers\Admin;

use App\Providers\AppServiceProvider;
use App\QuestionReferenceExam;
use App\ReferenceInstitute;
use App\ReferenceCourse;
use App\ReferenceFaculty;
use App\ReferenceSubject;
use App\ReferenceSession;
use App\QuestionChapter;
use App\QuestionSubject;
use App\QuestionTopic;
use App\QuestionReference;

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
use App\MentorAccess;
use App\OmrScript;
use App\SendSms;
use App\Teacher;
use Illuminate\Support\Facades\Schema;
use Session;
use Auth;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Excel;
use PDF;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Maatwebsite\Excel\Concerns\ToArray;

class ExamController extends Controller
{
    use ContentSelector;
    use SendSms;

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
    public function index() {
        $title = 'Exam List';

        return view('admin.exam.list', compact('title'));
    }

    public function exam_list() {
        $exam_list = ExamEdit::with('doctor_exams')->leftjoin('institutes as d2', 'd1.institute_id', '=', 'd2.id')
            ->leftjoin('courses as d3', 'd1.course_id', '=', 'd3.id')
            ->leftjoin('topics as d4', 'd1.class_id', '=', 'd4.id')
            ->leftjoin('sessions as d5', 'd1.session_id', '=', 'd5.id')
            ->leftjoin('question_types as d6', 'd1.question_type_id', '=', 'd6.id');

        $exam_list->select(
            'd1.id as id',
            'd1.name as exam_name',
            'd2.id as institute_id',
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
        )->whereNull('d1.deleted_at');

        if(Auth::user()->isMentor()) {
            $mentor_access = MentorAccess::query()
                ->firstOrCreate(
                    [
                        'mentor_id' => Auth::id(),
                    ],
                    [
                        'access_upto' => now(),
                    ]
                );

            if($mentor_access->access_upto < Carbon::make(date('Y-m-d'))) {
                $exam_list = $exam_list->limit(0);
            } else {
                $exam_list = $exam_list->whereIn('d1.id', $mentor_access->exam_ids ?? []);
            }
        }

        return DataTables::of($exam_list)
            ->addColumn('action', function ($exam_list) {
                return view('admin.exam.exam_ajax_list', (['exam_list' => $exam_list, 'user' => Auth::user()]));
            })

            ->addColumn('mcq_number', function ($exam_list) {
                return $exam_list->mcq_number . ' MCQ  / ' . $exam_list->sba_number . ' SBA';
            })

            ->addColumn('full_mark', function ($exam_list) {
                return (isset($exam_list->full_mark) ? $exam_list->full_mark : '') . '/' . (isset($exam_list->duration) ? $exam_list->duration / 60 : '');
            })

            ->addColumn('status', function ($exam_list) {
                return '<span style="color:' . ($exam_list->status == 1 ? 'green;' : 'red;') . ' font-size: 14px;">'
                    . ($exam_list->status == 1 ? 'Active' : 'Inactive') . '</span>';
            })
            ->rawColumns(['action', 'mcq_number', 'full_mark', 'status'])

            ->make(true);
    }

    protected function selection_config()
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
        for ($year = Date('Y',strtotime(Date('Y-m-d',time()). ' + 1 years')); $year >= 2017; $year--) {
            $years[$year] = $year;
        }

        $papers = array('' => 'Select Paper', '1' => 'Paper-I', '2' => 'Paper-II', '3' => 'Paper-III');
        $question_type = QuestionTypes::get()->pluck('title', 'id');
        $exam_type = Exam_type::get()->pluck('name', 'id');
        $institute = Institutes::get()->pluck('name', 'id');
        $hasClassIdCokumn = Schema::hasColumn('exam', 'class_id');



        $title = 'Exam Create';


        return view('admin.exam.create', ([
            'years' => $years,
            'institute' => $institute,
            'institutes_view' => $this->institutes(request())->render(),
            'courses_view' => $this->courses(request())->render(),
            'sessions_view' => $this->sessions(request())->render(),
            'papers' => $papers,
            'exam_type' => $exam_type,
            'question_type' => $question_type,
            'title' => $title,
            'has_class_id_column' => $hasClassIdCokumn
        ]));
    }

    static $exam_question = null;

    public static function exam_file_item($question = null, $question_id = null, $exam_question_id = null, &$data = [])
    {


        if (isset($question->question_title) && isset($question->question_type) && isset($question->exam_question_id) && isset($question->question_id)) {
            $question_id = $question->question_id;
            $exam_question_id = $question->exam_question_id;
            $answers = Question_ans::where(['question_id' => $question->id])->get();
        } else {
            $question = Question::with('question_answers')
                ->where('question_id', $question_id)
                ->where('exam_question.id', $exam_question_id)
                ->join('exam_question', 'exam_question.question_id', '=', 'questions.id')
                ->select(['*', 'exam_question.id as exam_question_id'])->first();
            $answers = Question_ans::where(['question_id' => $question_id])->get();
        }

        $question_type = $question->question_type;

        $data[$question_id] = [
            'question_id'      =>   (int) $question_id,
            'exam_question_id'  =>  (int) $exam_question_id,
            'question_type'     =>  $question_type,
            'correct_ans_sba'   => ($question_type == 2 || $question_type == 4) ? ($answers[0]->correct_ans ?? '') : '',
            'question_title'    =>  $question->question_title,
        ];

        $sls = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($answers as $i => $row) {
            $data[$question_id]['question_option'][] = [
                'option_serial' =>  $row->sl_no,
                'option_title'  =>  $row->answer,
                'correct_ans'   => ($question_type == 1 || $question_type == 3) ? $row->correct_ans : '',
            ];
        }

        return $data[$question_id];
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
            'year' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'session_id' => ['required'],
            'class' => ['required'],
            'description' => ['required'],
            'sif_only' => ['required'],
            'question_type_id' => ['required'],

        ]);

        if ($validator->fails()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Please enter proper input values!!!');
            return redirect()->action('Admin\ExamController@create')->withInput();
        }

        if (Exam::where(['name' => $request->name, 'class_id' => $request->class])->exists()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'This Exam  already exists');
            return redirect()->action('Admin\ExamController@create')->withInput();
        } else {
            $exam = new Exam;
            $exam->name = $request->name;
            $exam->nickname = $request->nickname;
            $exam->year = $request->year;
            $exam->exam_type_id = $request->exam_type_id;
            $exam->institute_id = $request->institute_id;
            $exam->course_id = $request->course_id;
            $exam->session_id = $request->session_id;
            $exam->description = $request->description;
            $exam->exam_details = $request->exam_details;
            $exam->paper = $request->paper;
            $exam->question_type_id = $request->question_type_id;
            $exam->sif_only = $request->sif_only;
            $exam->class_id = $request->class;
            $exam->collect_institute_roll = $request->roll ?? 0;

            $question_type = QuestionTypes::find($request->question_type_id);
            $total_question = $question_type->mcq_number + $question_type->sba_number + $question_type->mcq2_number;
            if ($total_question != 0 && ($exam->exam_questions->count() != $total_question || $exam->exam_questions()->where(['question_type' => '1'])->count() != $question_type->mcq_number  || $exam->exam_questions()->where(['question_type' => '2'])->count() != $question_type->sba_number || $exam->exam_questions()->where(['question_type' => '3'])->count() != $question_type->mcq2_number)) {
                $exam->status = '2';
            } else if ($total_question != 0 && $exam->exam_questions->count() == $total_question) {
                $exam->status = '1';
            } else {
                $exam->status = $request->status;
            }

            $exam->exam_file_link = self::EXAM_FILE_ROOT;
            $exam->created_by = Auth::id();

            $exam->save();

            // $data = array();

            // if ( $request->mcq_question_id ) {
            //     foreach ($request->mcq_question_id as $k => $value) {
            //         Exam_question::insert(['question_id' => $value, 'exam_id' => $exam->id, 'question_type' => 1]);
            //         $exam_question_id = DB::getPdo()->lastInsertId( );

            //         self::exam_file_item(NULL, $value, $exam_question_id, $data);

            //     }
            // }

            // if ($request->mcq2_question_id && is_array( $request->mcq2_question_id )) {

            //     foreach ( $request->mcq2_question_id as $k => $value ) {
            //         Exam_question::insert([ 'question_id' => $value,  'exam_id' => $exam->id,  'question_type' => 3 ]);
            //         $exam_question_id = DB::getPdo()->lastInsertId( );

            //         self::exam_file_item(NULL, $value, $exam_question_id, $data);
            //     }

            // }

            // if ( $request->sba_question_id ) {
            //     foreach ($request->sba_question_id as $k => $value) {
            //         Exam_question::insert(['question_id' => $value, 'exam_id' => $exam->id, 'question_type' => 2]);
            //         $exam_question_id = DB::getPdo()->lastInsertId();

            //         self::exam_file_item(NULL, $value, $exam_question_id, $data);

            //     }
            // }


            // if ( $request->topic_id ) {
            //     foreach ($request->topic_id as $k => $value) {
            //         Exam_topic::insert(['topic_id' => $value, 'exam_id' => $exam->id]);
            //     }
            // }

            // self::save_exam_file( $exam, $data );

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
            return redirect()->action('Admin\ExamController@edit', $exam->id);
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
        return 1;
    }



    public function duplicate($id)
    {
        $data = $this->duplicate_data($id);
        return view('admin.exam.duplicate', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->edit_data($id);
        $data['duplicate'] = false;
        return view('admin.exam.edit', $data);
    }


    public function edit_data($id)
    {
        $exam = Exam::find($id);

        $data['exam'] = $exam;

        $years = array('' => 'Select Year');
        for ($year = Date('Y',strtotime(Date('Y-m-d',time()). ' + 1 years')); $year >= 2017; $year--) {
            $years[$year] = $year;
        }

        $data['years'] = $years;

        $data['papers'] = array('' => 'Select Paper', '1' => 'Paper-I', '2' => 'Paper-II', '3' => 'Paper-III');
        $data['question_types'] = QuestionTypes::get()->pluck('title', 'id');
        $data['question_type'] = QuestionTypes::where('id', $data['exam']->question_type_id)->first();

        $data['exam_type'] = Exam_type::get()->pluck('name', 'id');

        $data['topic'] = Topics::where(['course_id' => $data['exam']->course_id, 'status' => 1])->pluck('name', 'id');

        $data['title'] = 'Exam Edit';
        $data['has_class_id_column']  = Schema::hasColumn('exam', 'class_id');


        $institute_id = old('institute_id', $exam->topic->institute_id ?? null);
        $course_id = old('course_id', $exam->topic->course_id ?? null);
        $session_id = old('session_id', $exam->topic->session_id ?? null);
        $year = old('year', $exam->topic->year ?? null);

        $data['institutes_view'] = $this->institutes(request(), $institute_id)->render();
        $data['courses_view'] = $this->courses(request(), $course_id, $institute_id)->render();
        $data['sessions_view'] = $this->sessions(request(), $session_id, $course_id, $year)->render();

        return $data;
    }

    public function duplicate_data($id)
    {
        $exam = Exam::find($id);

        $data['exam'] = $exam;

        $years = array('' => 'Select Year');
        for ($year = 2023; $year >= 2017; $year--) {
            $years[$year] = $year;
        }

        $data['years'] = $years;

        $data['papers'] = array('' => 'Select Paper', '1' => 'Paper-I', '2' => 'Paper-II', '3' => 'Paper-III');
        $data['question_types'] = QuestionTypes::get()->pluck('title', 'id');
        $data['question_type'] = QuestionTypes::where('id', $data['exam']->question_type_id)->first();

        $data['exam_type'] = Exam_type::get()->pluck('name', 'id');

        $data['topic'] = Topics::where(['course_id' => $data['exam']->course_id, 'status' => 1])->pluck('name', 'id');

        $data['title'] = 'Exam Duplicate';
        $data['has_class_id_column']  = Schema::hasColumn('exam', 'class_id');


        $institute_id = old('institute_id', $exam->topic->institute_id ?? null);
        $course_id = old('course_id', $exam->topic->course_id ?? null);
        $session_id = old('session_id', $exam->topic->session_id ?? null);
        $year = old('year', $exam->topic->year ?? null);

        $data['institutes_view'] = $this->institutes(request(), $institute_id)->render();
        $data['courses_view'] = $this->courses(request(), $course_id, $institute_id)->render();
        $data['sessions_view'] = $this->sessions(request(), $session_id, $course_id, $year)->render();

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function duplicate_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'description' => ['required'],
            'exam_type_id' => ['required'],
            'question_type_id' => ['required'],
            'year' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'session_id' => ['required'],
            'class' => ['required'],
            'sif_only' => ['required'],
        ]);

        if ($validator->fails()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Please enter proper input values!!!');
            return redirect()->action('Admin\ExamController@create')->withInput();
        }

        if (Exam::where(['name' => $request->name, 'class_id' => $request->class])->exists()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'This Exam  already exists');
            return redirect()->action('Admin\ExamController@create')->withInput();
        } else {
            $exam = new Exam;
            $exam->name = $request->name;
            $exam->nickname = $request->nickname;
            $exam->year = $request->year;
            $exam->exam_type_id = $request->exam_type_id;
            $exam->institute_id = $request->institute_id;
            $exam->course_id = $request->course_id;
            $exam->session_id = $request->session_id;
            $exam->description = $request->description;
            $exam->exam_details = $request->exam_details;
            $exam->paper = $request->paper;
            $exam->question_type_id = $request->question_type_id;
            $exam->sif_only = $request->sif_only;
            $exam->class_id = $request->class;
            $exam->collect_institute_roll = $request->roll ?? 0;

            $question_type = QuestionTypes::find($request->question_type_id);
            $total_question = $question_type->mcq_number + $question_type->sba_number + $question_type->mcq2_number;
            if ($total_question != 0 && ($exam->exam_questions->count() != $total_question || $exam->exam_questions()->where(['question_type' => '1'])->count() != $question_type->mcq_number  || $exam->exam_questions()->where(['question_type' => '2'])->count() != $question_type->sba_number || $exam->exam_questions()->where(['question_type' => '3'])->count() != $question_type->mcq2_number)) {
                $exam->status = '2';
            } else if ($total_question != 0 && $exam->exam_questions->count() == $total_question) {
                $exam->status = '1';
            } else {
                $exam->status = $request->status;
            }

            $exam->exam_file_link = self::EXAM_FILE_ROOT;
            $exam->created_by = Auth::id();

            $exam->save();

            $exam_questions = Exam_question::where(['exam_id' => $request->exam_id])->get();
            if (isset($exam_questions) && count($exam_questions) > 0) {
                Exam_question::where(['exam_id' => $exam->id])->update(['deleted_by' => Auth::id()]);
                Exam_question::where(['exam_id' => $exam->id])->delete();
                foreach ($exam_questions as $exam_question) {
                    Exam_question::insert(['exam_id' => $exam->id, 'question_id' => $exam_question->question_id, 'question_type' => $exam_question->question_type, 'created_by' => Auth::id()]);
                }
            }

            $exam_questions = Exam_question::where(['exam_id' => $exam->id])->get();
            $exam_question_type = $exam->question_type;
            $total_question = $exam_question_type->mcq_number + $exam_question_type->sba_number + $exam_question_type->mcq2_number;
            if ($total_question != 0 && $exam->exam_questions->count() == $total_question && $exam->exam_questions()->where(['question_type' => '1'])->count() == $exam_question_type->mcq_number  && $exam->exam_questions()->where(['question_type' => '2'])->count() == $exam_question_type->sba_number && $exam->exam_questions()->where(['question_type' => '3'])->count() == $exam_question_type->mcq2_number) {
                Exam::where(['id' => $exam->id])->update(['status' => '1', 'exam_file_link' => self::EXAM_FILE_ROOT]);

                $array_data = [];
                foreach ($exam_questions as $k => $exam_question) {

                    self::exam_file_item(NULL, $exam_question->question->id, $exam_question->id, $array_data);
                }
                self::save_exam_file($exam, $array_data);
            }


            Session::flash('message', 'Record has been added successfully');
            return redirect()->action('Admin\ExamController@edit', $exam->id);
        }
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
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'description' => ['required'],
            'exam_type_id' => ['required'],
            'question_type_id' => ['required'],
            'year' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'session_id' => ['required'],
            'class' => ['required'],
            'sif_only' => ['required'],
        ]);

        if ($validator->fails()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Please enter proper input values!!!');
            return redirect()->action('Admin\ExamController@edit', [$id])->withInput();
        }


        $exam = Exam::find($id);

        if (!($request->name == $exam->name && $request->class == $exam->class_id)) {

            if (Exam::where(['name' => $request->name, 'class_id' => $request->class])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This Exam already exists');
                return redirect()->back()->withInput();
            }
        }


        $exam->name = $request->name;
        $exam->nickname = $request->nickname;
        $exam->year = $request->year;
        $exam->exam_type_id = $request->exam_type_id;
        $exam->institute_id = $request->institute_id;
        $exam->course_id = $request->course_id;
        $exam->session_id = $request->session_id;
        $exam->description = $request->description;
        $exam->exam_details = $request->exam_details;
        $exam->paper = $request->paper;
        $exam->question_type_id = $request->question_type_id;
        $exam->sif_only = $request->sif_only;
        $exam->class_id = $request->class;
        $exam->collect_institute_roll = $request->roll ?? 0;

        $question_type = QuestionTypes::find($request->question_type_id);
        $total_question = $question_type->mcq_number + $question_type->sba_number + $question_type->mcq2_number;
        if ($total_question != 0 && ($exam->exam_questions->count() != $total_question || $exam->exam_questions()->where(['question_type' => '1'])->count() != $question_type->mcq_number  || $exam->exam_questions()->where(['question_type' => '2'])->count() != $question_type->sba_number || $exam->exam_questions()->where(['question_type' => '3'])->count() != $question_type->mcq2_number)) {
            $exam->status = '2';
        } else if ($total_question != 0 && $exam->exam_questions->count() == $total_question) {
            $exam->status = '1';
        } else {
            $exam->status = $request->status;
        }

        $exam->exam_file_link = self::EXAM_FILE_ROOT;
        $exam->created_by = Auth::id();

        $exam->push();

        $exam_questions = Exam_question::where(['exam_id' => $exam->id])->get();
        $exam_question_type = $exam->question_type;
        $total_question = $exam_question_type->mcq_number + $exam_question_type->sba_number + $exam_question_type->mcq2_number;
        if ($total_question != 0 && $exam->exam_questions->count() == $total_question && $exam->exam_questions()->where(['question_type' => '1'])->count() == $exam_question_type->mcq_number  && $exam->exam_questions()->where(['question_type' => '2'])->count() == $exam_question_type->sba_number && $exam->exam_questions()->where(['question_type' => '3'])->count() == $exam_question_type->mcq2_number) {
            Exam::where(['id' => $exam->id])->update(['status' => '1', 'exam_file_link' => self::EXAM_FILE_ROOT]);

            $array_data = [];
            foreach ($exam_questions as $k => $exam_question) {

                self::exam_file_item(NULL, $exam_question->question->id, $exam_question->id, $array_data);
            }
            self::save_exam_file($exam, $array_data);
        }

        // Exam_topic::where('exam_id', $id)->delete();
        // if ($request->topic_id) {
        //     foreach ($request->topic_id as $k => $value) {
        //         Exam_topic::insert(['topic_id' => $value, 'exam_id' => $exam->id]);
        //     }
        // }

        // Exam_question::where(['exam_id' => $id, 'question_type' => 1])->delete( );
        // if ($request->mcq_question_id) {
        //     foreach ($request->mcq_question_id as $k => $value) {
        //         Exam_question::insert(['question_id' => $value, 'exam_id' => $id, 'question_type' => 1]);
        //         $exam_question_id = DB::getPdo()->lastInsertId();

        //         self::exam_file_item(NULL, $value, $exam_question_id, $data);
        //     }
        // }

        // Exam_question::where(['exam_id' => $id, 'question_type' => 3])->delete();
        // if ($request->mcq2_question_id && is_array( $request->mcq2_question_id )) {
        //     foreach ($request->mcq2_question_id as $k => $value) {
        //         Exam_question::insert(['question_id' => $value, 'exam_id' => $id, 'question_type' => 3]);
        //         $exam_question_id = DB::getPdo()->lastInsertId();

        //         self::exam_file_item(NULL, $value, $exam_question_id, $data);
        //     }
        // }

        // Exam_question::where(['exam_id' => $id, 'question_type' => 2])->delete();
        // if ($request->sba_question_id) {
        //     foreach ( $request->sba_question_id as $k => $value ) {
        //         Exam_question::insert(['question_id' => $value, 'exam_id' => $id, 'question_type' => 2]);
        //         $exam_question_id = DB::getPdo()->lastInsertId();

        //         self::exam_file_item(NULL, $value, $exam_question_id, $data);
        //     }
        // }


        // Exam_question::where(['exam_id' => $id, 'question_type' => 4])->delete( );

        // if ( $request->sba2_question_id && is_array( $request->sba2_question_id ) ) {
        //     foreach ( $request->sba2_question_id as $k => $value ) {
        //         Exam_question::insert(['question_id' => $value, 'exam_id' => $id, 'question_type' => 4]);
        //         $exam_question_id = DB::getPdo()->lastInsertId();

        //         self::exam_file_item(NULL, $value, $exam_question_id, $data);
        //     }
        // }

        // self::save_exam_file( $exam, $data );

        Session::flash('message', 'Record has been updated successfully');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function add_questions($params)
    {
        $data = [];

        $params = json_decode($params);
        $data['exam'] = Exam::find($params->exam_id);
        $data['ques_type'] = QuestionTypes::find($params->question_type_id);
        $data['question_type'] = $params->question_type;
        $data['question_type_name'] = $params->question_type_name;

        $mentor = Auth::user()->question();

        $data['subjects'] = $mentor->subjects->pluck('subject_name', 'id');
        $data['chapters'] = $mentor->chapters->pluck('chapter_name', 'id');
        $data['topics'] = $mentor->topics->pluck('topic_name', 'id');

        $data['references'] = QuestionReferenceExam::orderBy('reference_code', 'asc')->pluck('reference_code', 'id');
        $data['source_institutes'] = ReferenceInstitute::pluck('name', 'id');
        $data['source_courses'] = ReferenceCourse::groupBy('name')->pluck('name', 'id');
        $data['source_faculties'] = ReferenceFaculty::groupBy('name')->pluck('name', 'id');
        $data['source_subjects'] = ReferenceSubject::groupBy('name')->pluck('name', 'id');
        $data['source_sessions'] = ReferenceSession::groupBy('name')->pluck('name', 'id');
        $data['years'] = array('' => 'Select year');
        for ($year = date("Y") + 1; $year >= 2000; $year--) {
            $data['years'][$year] = $year;
        }

        $data['title'] = 'Question List';

        return view('admin.exam.add_questions', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function add_exam_questionss($exam_id, $question_type)
    {
        $data = [];

        $question_types = ['1' => 'MCQ', '2' => 'SBA', '3' => 'MCQ2'];
        $data['exam'] = Exam::find($exam_id);
        $data['ques_type'] = $data['exam']->question_type;
        $data['question_type'] = $question_type;
        $data['question_type_name'] = $question_types[$question_type];

        $mentor = Auth::user()->question();

        $data['subjects'] = $mentor->subjects->pluck('subject_name', 'id');
        $data['chapters'] = $mentor->chapters->pluck('chapter_name', 'id');
        $data['topics'] = $mentor->topics->pluck('topic_name', 'id');

        $data['references'] = QuestionReferenceExam::orderBy('reference_code', 'asc')->pluck('reference_code', 'id');
        $data['source_institutes'] = ReferenceInstitute::pluck('name', 'id');
        $data['source_courses'] = ReferenceCourse::groupBy('name')->pluck('name', 'id');
        $data['source_faculties'] = ReferenceFaculty::groupBy('name')->pluck('name', 'id');
        $data['source_subjects'] = ReferenceSubject::groupBy('name')->pluck('name', 'id');
        $data['source_sessions'] = ReferenceSession::groupBy('name')->pluck('name', 'id');
        $data['years'] = array('' => 'Select year');
        for ($year = date("Y") + 1; $year >= 2000; $year--) {
            $data['years'][$year] = $year;
        }

        $data['title'] = 'Question List';

        return view('admin.exam.add_questions', $data);
    }



    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function question_list(Request $request)
    {

        //$mcq_list = Question::where('type', 1)->orderBy('id', 'desc')->select('*');
        $exam_id = $request->exam_id;
        $subject_id = $request->subject_id;
        $chapter_id = $request->chapter_id;
        $topic_id = $request->topic_id;
        $source_institute_id = $request->source_institute_id;
        $source_course_id = $request->source_course_id;
        $source_faculty_id = $request->source_faculty_id;
        $source_subject_id = $request->source_subject_id;
        $source_session_id = $request->source_session_id;
        $year = $request->year;
        $reference_id = $request->reference_id;
        $question_type = $request->question_type;
        if ($question_type == 3) $question_type = 1;

        $mcq_list = DB::table('questions as d1')
            ->where(['type' => $question_type])
            ->leftjoin('ques_topics as d2', 'd1.topic_id', '=', 'd2.id')
            ->leftjoin('ques_chapters as d3', 'd1.chapter_id', '=', 'd3.id')
            ->leftjoin('ques_subjects as d4', 'd1.subject_id', '=', 'd4.id')
            // ->leftjoin('question_ans as d7', 'd7.question_id', '=', 'd1.id')
            // ->leftjoin('questions_references as d5', 'd1.id', '=','d5.question_id')
            // ->leftjoin('questions_references_exams as d6', 'd5.reference_id', '=','d6.id')
        ;
        if ($subject_id) {
            $mcq_list = $mcq_list->where('d1.subject_id', '=', $subject_id);
        }
        if ($chapter_id) {
            $chapter_name = QuestionChapter::where(['id' => $chapter_id])->value('chapter_name');
            $chapter_ids = QuestionChapter::where(['chapter_name' => $chapter_name])->pluck('id')->toArray();
            $mcq_list = $mcq_list->whereIn('d1.chapter_id', $chapter_ids);
        }

        if ($topic_id) {
            $topic_name = QuestionTopic::where(['id' => $topic_id])->value('topic_name');
            $topic_ids = QuestionTopic::where(['topic_name' => $topic_name])->pluck('id')->toArray();
            $mcq_list = $mcq_list->whereIn('d1.topic_id', $topic_ids);
        }

        // if($reference_id){
        //     $mcq_list = $mcq_list->where('d5.reference_id', '=', $reference_id);
        // }
        // if($source_institute_id){
        //     $mcq_list = $mcq_list->whereIn('d6.institute_id', '=', $source_institute_id);
        // }
        if ($source_course_id) {
            $course_name = ReferenceCourse::where(['id' => $source_course_id])->value('name');
            $course_ids = ReferenceCourse::where(['name' => $course_name])->pluck('id')->toArray();
            $mcq_list = $mcq_list->whereIn('d6.course_id', $course_ids);
        }
        if ($source_faculty_id) {
            $faculty_name = ReferenceFaculty::where(['id' => $source_faculty_id])->value('name');
            $faculty_ids = ReferenceFaculty::where(['name' => $faculty_name])->pluck('id')->toArray();
            $mcq_list = $mcq_list->whereIn('d6.faculty_id', $faculty_ids);
        }
        if ($source_subject_id) {
            $subject_name = ReferenceSubject::where(['id' => $source_subject_id])->value('name');
            $subject_ids = ReferenceSubject::where(['name' => $subject_name])->pluck('id')->toArray();
            $mcq_list = $mcq_list->whereIn('d6.subject_id', $subject_ids);
        }
        if ($source_session_id) {
            $session_name = ReferenceSession::where(['id' => $source_session_id])->value('name');
            $session_ids = ReferenceSession::where(['name' => $session_name])->pluck('id')->toArray();
            $mcq_list = $mcq_list->whereIn('d6.session_id', $session_ids);
        }
        if ($year) {
            $mcq_list = $mcq_list->where('d6.year', '=', $year);
        }


        if (Auth::user()->need_to_filter_question_topic($topic_ids, $subject_ids, $chapter_ids)) {
            $mcq_list->where(function ($mcq_list) use ($topic_ids, $subject_ids, $chapter_ids) {
                $mcq_list->whereIn('d1.topic_id', $topic_ids);
                $mcq_list->orWhereIn('d1.chapter_id', $chapter_ids);
                $mcq_list->orWhereIn('d1.subject_id', $subject_ids);
            });
        }

        $mcq_list = $mcq_list->select(
            'd1.id',
            'd1.question_title',
            'd1.question_and_answers as question',
            'd2.topic_name as topic_name',
            'd3.chapter_name as chapter_name',
            'd4.subject_name as subject_name',
            // DB::raw('GROUP_CONCAT(d7.answer) as options'),
        )
        // ->groupBy('d1.id')
        ;

        $mcq_list = $mcq_list->addSelect(DB::raw($exam_id . " as exam_id"));

        return Datatables::of($mcq_list)
            ->addColumn('references', function ($mcq_list) {
                $question_references = QuestionReference::where('question_id', $mcq_list->id)->get();
                $question_reference_exams = array();
                foreach ($question_references as $question_reference) {
                    if (isset($question_reference->question_reference_exam->reference_code)) $question_reference_exams[] =  $question_reference->question_reference_exam->reference_code;
                }
                return implode(' , ', $question_reference_exams);
            })
            ->addColumn('action', function ($mcq_list) {
                $data['checked'] = "";
                $data['question_info'] = "";

                $exam_question = Exam_question::where(['exam_id' => $mcq_list->exam_id, 'question_id' => $mcq_list->id])->first();
                if (isset($exam_question)) {
                    $data['checked'] = "checked disabled";
                } else {
                    $data['checked'] = "";
                }

                $data['mcq_list'] = $mcq_list;
                //$data['question_answers'] = Question_ans::where('question_id',$mcq_list->id)->get();
                //$data['subject'] = QuestionSubject::where(['id'=>$mcq_list->subject_id])->get();
                //$data['chapter'] = QuestionChapter::where(['id'=>$mcq_list->chapter_id])->get();
                //$data['topic'] = QuestionTopic::where(['id'=>$mcq_list->topic_id])->get();
                return view('admin.exam.ajax_list', $data);
            })
            ->rawColumns(['question_title', 'references', 'action', 'question'])
            ->make(true);
    }

    public function add_exam_question(Request $request)
    {
        $question_types = ['1' => 'MCQ', '2' => 'SBA', '3' => 'MCQ2'];
        $question_types_number = ['1' => 'mcq_number', '2' => 'sba_number', '3' => 'mcq2_number'];
        $data['question_id'] = $request->question_id;
        $data['exam_id'] = $request->exam_id;
        $exam = Exam::where(['id' => $request->exam_id])->first();
        $exam_question_type = $exam->question_type;
        $data['question_add_status'] = "incomplete";
        if ($request->operation == "insert") {
            $question_numbers = '';
            if ($request->question_type == 1) {
                $question_numbers = $exam_question_type->mcq_number;
            } else if ($request->question_type == 2) {
                $question_numbers = $exam_question_type->sba_number;
            } else if ($request->question_type == 3) {
                $question_numbers = $exam_question_type->mcq2_number;
            }

            $exam_questions = Exam_question::where(['exam_id' => $request->exam_id, 'question_type' => $request->question_type])->get();
            if ($exam_questions->count() < $question_numbers) {
                $exam_question = Exam_question::where(['exam_id' => $request->exam_id, 'question_id' => $request->question_id])->first();
                if (!isset($exam_question)) {
                    $exam_question = Exam_question::insert(['question_id' => $request->question_id, 'exam_id' => $request->exam_id, 'question_type' => $request->question_type, 'created_by' => Auth::id()]);
                    if (isset($exam_question)) {
                        $data['success_status'] = "insert_success";
                        $data['message'] = '<br><span style="color:green;font-weight:700">Successfully Added ' . (($exam_questions->count() + 1) ?? '') . ' of ' . $question_numbers . ' ' . $question_types[$request->question_type] . ' question.</span';
                    }
                } else {

                    $data['question_add_status'] = "question_exist";
                    $data['message'] = '<br><span style="color:red;font-weight:700">This question already exist in this exam !!!</span';
                }

                $exam_questions = Exam_question::where(['exam_id' => $request->exam_id, 'question_type' => $request->question_type])->get();
                if ($exam_questions->count() == $question_numbers) {
                    Session::flash('class', 'alert-danger');
                    session()->flash('message', 'Exam ' . $question_types[$request->question_type] . ' add completed !!!');
                    $data['question_add_status'] = "completed";
                }
            } else {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Exam ' . $question_types[$request->question_type] . ' add completed !!!');
                $data['question_add_status'] = "completed";
            }
        } else if ($request->operation == "delete") {
            $exam_question = Exam_question::where(['question_id' => $request->question_id, 'exam_id' => $request->exam_id, 'question_type' => $request->question_type])->update(['deleted_by' => Auth::id()]);
            $exam_question = Exam_question::where(['question_id' => $request->question_id, 'exam_id' => $request->exam_id, 'question_type' => $request->question_type])->delete();
            $exam_questions = Exam_question::where(['exam_id' => $request->exam_id, 'question_type' => $request->question_type])->get();
            if (isset($exam_question)) {
                $data['success_status'] = "delete_success";
                $data['message'] = '<br><span style="color:red;font-weight:700">Successfully removed ' . (($exam_questions->count() + 1) ?? '') . ' no  ' . $question_types[$request->question_type] . ' question.</span';
            }
        }

        $exam_questions = Exam_question::where(['exam_id' => $exam->id])->get();
        $exam_question_type = $exam->question_type;
        $total_question = $exam_question_type->mcq_number + $exam_question_type->sba_number + $exam_question_type->mcq2_number;
        if ($total_question != 0 && $exam->exam_questions->count() == $total_question && $exam->exam_questions()->where(['question_type' => '1'])->count() == $exam_question_type->mcq_number  && $exam->exam_questions()->where(['question_type' => '2'])->count() == $exam_question_type->sba_number && $exam->exam_questions()->where(['question_type' => '3'])->count() == $exam_question_type->mcq2_number) {
            Exam::where(['id' => $exam->id])->update(['status' => '1', 'exam_file_link' => self::EXAM_FILE_ROOT]);

            $array_data = [];
            foreach ($exam_questions as $k => $exam_question) {

                self::exam_file_item(NULL, $exam_question->question->id, $exam_question->id, $array_data);
            }
            self::save_exam_file($exam, $array_data);
        }

        return response()->json($data);
    }

    public function exam_questions($exam_id)
    {
        $data['exam'] = Exam::with([
            'exam_questions:id,exam_id,question_id,question_type',
            'exam_questions.question:id,type,question_title',
            'exam_questions.question.question_answers:id,question_id,answer',
        ])->where(['id' => $exam_id])->first();
        $data['mcqs'] = $data['exam']->exam_questions()->where(['question_type' => 1])->count();
        $data['sbas'] = $data['exam']->exam_questions()->where(['question_type' => 2])->count();
        $data['mcq2s'] = $data['exam']->exam_questions()->where(['question_type' => 3])->count();

        $exam = $data['exam'];
        $question_type = QuestionTypes::find($exam->question_type_id);
        $total_question = $question_type->mcq_number + $question_type->sba_number + $question_type->mcq2_number;
        if ($total_question != 0 && ($exam->exam_questions->count() != $total_question || $exam->exam_questions()->where(['question_type' => '1'])->count() != $question_type->mcq_number  || $exam->exam_questions()->where(['question_type' => '2'])->count() != $question_type->sba_number || $exam->exam_questions()->where(['question_type' => '3'])->count() != $question_type->mcq2_number)) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Exam questions are not matching with exam properties ( mcq number/ sba number etc. )s !!!');
        }

        // return $data;

        return view('admin.exam.exam_questions', $data);
    }

    public function edit_exam_question($question_id)
    {
        $data = [];

        $exam_question = Exam_question::where(['id' => $question_id])->first();
        $data['exam_question'] = $exam_question;
        $question_types = ['1' => 'MCQ', '2' => 'SBA', '3' => 'MCQ2'];
        $data['exam'] = $exam_question->exam;
        $data['question_type'] = $exam_question->question_type;
        $data['question_type_name'] = $question_types[$exam_question->question_type];

        $mentor = Auth::user()->question();

        $data['subjects'] = $mentor->subjects->pluck('subject_name', 'id');
        $data['chapters'] = $mentor->chapters->pluck('chapter_name', 'id');
        $data['topics'] = $mentor->topics->pluck('topic_name', 'id');

        $data['references'] = QuestionReferenceExam::orderBy('reference_code', 'asc')->pluck('reference_code', 'id');
        $data['source_institutes'] = ReferenceInstitute::pluck('name', 'id');
        $data['source_courses'] = ReferenceCourse::groupBy('name')->pluck('name', 'id');
        $data['source_faculties'] = ReferenceFaculty::groupBy('name')->pluck('name', 'id');
        $data['source_subjects'] = ReferenceSubject::groupBy('name')->pluck('name', 'id');
        $data['source_sessions'] = ReferenceSession::groupBy('name')->pluck('name', 'id');
        $data['years'] = array('' => 'Select year');
        for ($year = date("Y") + 1; $year >= 2000; $year--) {
            $data['years'][$year] = $year;
        }

        $data['title'] = 'Question List';

        return view('admin.exam.edit_questions', $data);
    }

    public function update_exam_question(Request $request)
    {
        $question_types = ['1' => 'MCQ', '2' => 'SBA', '3' => 'MCQ2'];
        $data['exam_question'] = Exam_question::where(['id' => $request->exam_question_id])->first();
        $data['exam_id'] = $data['exam_question']->exam->id;
        $exam = $data['exam_question']->exam;
        $data['question_add_status'] = "incomplete";
        if ($request->operation == "insert") {
            $exam_question = Exam_question::where(['exam_id' => $exam->id, 'question_id' => $request->question_id])->first();
            if (!isset($exam_question)) {
                $data['updated'] = Exam_question::where(['id' => $request->exam_question_id])->update(['question_id' => $request->question_id, 'question_type' => $request->question_type, 'updated_by' => Auth::id()]);
                if ($data['updated']) {
                    Session::flash('class', 'alert-success');
                    session()->flash('message', 'Exam question updated successfully !!!');
                    $data['question_add_status'] = "completed";

                    $exam_questions = Exam_question::where(['exam_id' => $exam->id])->get();
                    $exam_question_type = $exam->question_type;
                    $total_question = $exam_question_type->mcq_number + $exam_question_type->sba_number + $exam_question_type->mcq2_number;
                    if ($total_question != 0 && $exam->exam_questions->count() == $total_question && $exam->exam_questions()->where(['question_type' => '1'])->count() == $exam_question_type->mcq_number  && $exam->exam_questions()->where(['question_type' => '2'])->count() == $exam_question_type->sba_number && $exam->exam_questions()->where(['question_type' => '3'])->count() == $exam_question_type->mcq2_number) {
                        Exam::where(['id' => $exam->id])->update(['status' => '1', 'exam_file_link' => self::EXAM_FILE_ROOT]);

                        $array_data = [];
                        foreach ($exam_questions as $k => $exam_question) {

                            self::exam_file_item(NULL, $exam_question->question->id, $exam_question->id, $array_data);
                        }
                        self::save_exam_file($exam, $array_data);
                    }
                } else {
                    Session::flash('class', 'alert-danger');
                    session()->flash('message', 'Exam question update unsuccessful !!!');
                    $data['question_add_status'] = "";
                }
            } else {
                Session::flash('class', 'alert-success');
                session()->flash('message', 'This question already exist in this exam !!!');
                $data['question_add_status'] = "completed";
                $data['message'] = '<br><span style="color:red;font-weight:700">This question already exist in this exam !!!</span';
            }
        }


        return response()->json($data);
    }

    public function delete_exam_question($question_id)
    {
        $data['exam_question'] = Exam_question::where(['id' => $question_id])->first();
        $data['deleted'] = Exam_question::where(['id' => $question_id])->update(['deleted_by' => Auth::id()]);
        $data['deleted'] = Exam_question::where(['id' => $question_id])->delete();
        if ($data['deleted']) {
            Session::flash('class', 'alert-info');
            Session::flash('message', 'Question has been removed successfully');
        } else {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Question remove unsuccessfull !!!');
        }

        return redirect(url('admin/exam-questions/' . $data['exam_question']->exam->id));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function add_exam_questions($params)
    {
        $data = [];

        $params = json_decode($params);
        $data['exam'] = Exam::find($params->exam_id);
        $data['ques_type'] = QuestionTypes::find($params->question_type_id);
        $data['question_type'] = $params->question_type;
        $data['question_type_name'] = $params->question_type_name;

        $mentor = Auth::user()->question();

        $data['subjects'] = $mentor->subjects->pluck('subject_name', 'id');
        $data['chapters'] = $mentor->chapters->pluck('chapter_name', 'id');
        $data['topics'] = $mentor->topics->pluck('topic_name', 'id');

        $data['references'] = QuestionReferenceExam::orderBy('reference_code', 'asc')->pluck('reference_code', 'id');
        $data['source_institutes'] = ReferenceInstitute::pluck('name', 'id');
        $data['source_courses'] = ReferenceCourse::groupBy('name')->pluck('name', 'id');
        $data['source_faculties'] = ReferenceFaculty::groupBy('name')->pluck('name', 'id');
        $data['source_subjects'] = ReferenceSubject::groupBy('name')->pluck('name', 'id');
        $data['source_sessions'] = ReferenceSession::groupBy('name')->pluck('name', 'id');
        $data['years'] = array('' => 'Select year');
        for ($year = date("Y") + 1; $year >= 2000; $year--) {
            $data['years'][$year] = $year;
        }

        $data['title'] = 'Question List';

        return view('admin.exam.add_questions', $data);
    }

    public function get_question_details(Request $request)
    {
        $data['question'] = Question::with('question_answers')->where(['id' => $request->question_id])->first();

        return view('admin.exam.question_details', $data);
    }


    public static function  save_exam_file(Exam $exam, $data)
    {
        if (empty($exam->exam_file_link)) {
            $exam->exam_file_link = self::EXAM_FILE_ROOT;
            $exam->save();
        }

        $file_path = $exam->exam_file_link;

        if (!preg_match("/[^\/]+\.json(?=\/$|$)/i", $file_path)) {
            $file_path = rtrim($file_path, '/') . '/' . $exam->id . '.json';
        }

        $dir_link = rtrim(preg_replace("/[^\/]+(?=\/$|$)/", '', $file_path), '/');

        //dd( $dir_link );

        if (!is_dir($dir_link)) {
            mkdir($dir_link, 0777, true);
        }

        //dd( $file_path );

        $my_file = fopen($file_path, "w") or die("Unable to open file!");
        $txt = json_encode($data);

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
        Exam_question::where('exam_id', $id)->delete();
        Result::where('exam_id', $id)->delete();
        DoctorExam::where('exam_id', $id)->delete();

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ExamController@index');
    }

    // public function print($id)
    // {
    //     return view('admin.exam.print', ['exam' => Exam::with('exam_questions.question.question_answers', 'question_type', 'exam_topic.topic')->find($id)]);
    // }

    public function print_exam($id)
    {
        $data['exam'] = Exam::find($id);
        $questions = array();
        if (count($data['exam']->exam_questions) % 2 == 0) {
            for ($i = 0; $i < count($data['exam']->exam_questions); $i += 2) {
                $questions[$i]['a'] = $data['exam']->exam_questions[$i]->question;
                $questions[$i]['b'] = $data['exam']->exam_questions[$i + 1]->question;
                $questions[$i]['c'] = $i;
            }
        } else {
            for ($i = 0; $i < count($data['exam']->exam_questions) - 1; $i += 2) {

                $questions[$i]['a'] = $data['exam']->exam_questions[$i]->question;
                $questions[$i]['b'] = $data['exam']->exam_questions[$i + 1]->question;

                $questions[$i]['c'] = $i;
            }
            $questions[$i - 1]['a'] = $data['exam']->exam_questions[$i]->question;
            $questions[$i - 1]['c'] = $i - 1;
        }

        $data['questions'] = $questions;

        return view('admin.exam.print_exam', $data);
    }

    public function print($id)
    {
        $exam = Exam::query()
            ->with([
                'question_type:id,mcq_number,sba_number,mcq2_number,full_mark,pass_mark,duration',
                'exam_questions:id,exam_id,question_id,question_type',
                'exam_questions.question:id,type,question_title',
                'exam_questions.question.question_answers:id,question_id,answer',
            ])
            ->findOrFail($id, [
                'id',
                'name',
                'exam_date',
                'question_type_id',
            ]);

        $exam->update(['is_print' => 'Yes']);

        return view('admin.exam.print', compact('exam'));
    }


    public function print_ans($id)
    {
        $exam = Exam::query()
            ->with([
                'question_type:id,mcq_number,sba_number,mcq2_number,full_mark,pass_mark,duration',
                'exam_questions:id,exam_id,question_id,question_type',
                'exam_questions.question:id,type,question_title,discussion,reference',
                'exam_questions.question.question_answers:id,question_id,answer,correct_ans',
                // 'exam_questions.question.reference_books',
            ])
            ->findOrFail($id, [
                'id',
                'name',
                'exam_date',
                'question_type_id',
            ]);

        $exam_questions = $exam->exam_questions;

        foreach($exam_questions as &$exam_question) {
            $options = $exam_question->question->question_answers;

            $answer_script = "";

            foreach($options as $option) {
                $answer_script .= $option->correct_ans ?? ".";
                if($exam_question->question->type == 2) {
                    break;
                }
            }

            $exam_question->question->script = $answer_script;
        }

        return view('admin.exam.print_ans', compact('exam'));
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

        foreach ($questions as $exam_question) {
            $answers = $exam_question->question->question_answers ?? [];
            $tfs = '';

            foreach ($answers as $ans) {
                $tfs .= $ans->correct_ans;
            }

            $text[] = $tfs;
        }

        return implode('', $text);
        //        return strlen('FFFTTTTTTTTTTTTFTTFTTTTTTTTTTTFTTTFTTTFFFTTTFFFFFFTTFFFTTTTFTFFTTTTTTTTTTTTFTTFFTTFFFTFTFFFTFFTTTFFFTTTTTFTTTFTTFFTTFTTTTTTTTTTTTTFFTFTFFTTTFTFTTFTTTFFFFTTTTFFFTTFFTTTTTFFFFFFTTTTTTFTFTTFFTFTTTTFFFFFTTTTTTTTTTTTFFFFTTTTTTTTTTTTTTTTTTTTTTT,TTTTFFFFFTTTFTTTFFFTTFTTTFFFFFTTTTTTFTFTFFFTFFTTFTFFTTFFFFFFFTTTTTFTTTTFTTTTTFTFFTTTTFFFFFTFTTTTTFFTFFTTTTTF>TTFTTTTTFFTFTTTTTTFTTTFTTTTT');

    }


    public function upload_result_old($id)
    {
        $data['exam_info'] = $exam_id = Exam::find($id);
        $data['title'] = 'Upload Result';
        $institute_type = $exam_id->institute->type;
        if ($institute_type == 1) {
            return view('admin.exam.upload_result_faculty', $data);
        }
        return view('admin.exam.upload_result', $data);
    }


    public function view_result($id)
    {

        $data['batches'] = Batches::get()->pluck('name', 'id');
        // $data['courses']= Courses::get()->pluck('name', 'id');
        // $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['years'] = array('' => 'Select year');
        for ($year = date("Y") + 1; $year >= 2017; $year--) {
            $data['years'][$year] = $year;
        }

        $exam = Exam::find($id); //dd($exam->toArray());
        $exam->highest_mark  = Result::where('exam_id', $id)->orderBy('obtained_mark', 'desc')->value('obtained_mark');
        $doctor_courses = Result::where('exam_id', $id)->orderBy('obtained_mark', 'desc')->get();
        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($doctor_courses as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $row->possition = $p . $th;
                $i++;
            } else {
                $row->possition = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;

            $row->candidate_possition = $this->candidate_possition(
                $row->doctor_course->candidate_type ?? '',
                Subjects::where(['id' => $row->subject_id])->value('name'),
                $id,
                $row->obtained_mark
            );

            $row->subject_possition = $this->subject_possition(Subjects::where(['id' => $row->subject_id])->value('name'), $id, $row->obtained_mark);
            $row->batch_possition = $this->batch_possition($row->batch_id, $id, $row->obtained_mark);
            $row->faculty = BcpsFaculty::where(['id' => $row->faculty_id])->value('name');
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['courses'] = Courses::pluck('name', 'id');
        $data['batches'] = Batches::where('course_id', $exam->course_id)->pluck('name', 'id');

        $data['doctor_courses'] = $doctor_courses;
        $data['exam'] = $exam;
        $data['title'] = 'Results';

        $data['paper_faculty'] = QuestionTypes::where('id', $exam->question_type_id)->value('paper_faculty');

        $data['examination_code']  = Result::where('exam_id', $id)->value('examination_code');
        $data['candidate_code']  = Result::where('exam_id', $id)->value('candidate_code');

        return view('admin.exam.view_result', $data);
    }

    public function exam_batch_list(Request $request)
    {
        $year = $request->year;
        $course_id = $request->course_id;
        $session_id = $request->session_id;
        $batch_id = $request->batch_id;
        $exam_id = $request->exam_id;
        $result = DB::table('results as r')
            ->leftjoin('batches as b', 'r.batch_id', '=', 'b.id')
            ->leftjoin('exam as e', 'r.exam_id', '=', 'e.id')
            ->leftjoin('doctors_courses as dc', 'r.doctor_course_id', '=', 'dc.id')
            ->leftjoin('doctors as d', 'dc.doctor_id', '=', 'd.id')
            ->leftjoin('subjects as s', 'r.subject_id', '=', 's.id')
            ->where('r.exam_id', $exam_id);

        if ($year) {
            $result = $result->where('b.year', '=', $year);
        }
        if ($course_id) {
            $result = $result->where('dc.course_id', '=', $course_id);
        }
        if ($session_id) {
            $result = $result->where('b.session_id', '=', $session_id);
        }
        if ($batch_id) {
            $result = $result->where('r.batch_id', '=', $batch_id);
        }

        $results_list = $result->select('r.exam_id as exam_id', 'e.institute_id as institute_id', 'dc.candidate_type as candidate_type', 'r.id as id', 'd.name as doctor_name', 'dc.reg_no as reg_no', 'r.batch_id as batch_id', 'b.name as batch_name', 's.name as discipline_name', 'r.obtained_mark as obtained_mark', 'r.wrong_answers as wrong_answer',);

        return DataTables::of($results_list)
            ->addColumn('disciplain_position', function ($result) {

                return $this->subject_possition($result->discipline_name, $result->exam_id, $result->obtained_mark);
            })
            ->addColumn('batch_position', function ($result) {

                return $this->batch_possition($result->batch_id, $result->exam_id, $result->obtained_mark);
            })
            ->addColumn('candidate_position', function ($result) {
                return $this->candidate_possition($result->candidate_type, $result->discipline_name, $result->exam_id, $result->obtained_mark);
            })
            ->addColumn('overall_position', function ($result) {
                return $this->overall_possition($result->exam_id, $result->obtained_mark);
            })
            ->make(true);
    }

    function overall_possition($exam_id, $obtained_mark_single, $all_exam_results = null)
    {
        $overall_result = $all_exam_results ?? Result::where('exam_id', $exam_id)->orderBy('obtained_mark', 'desc')->get();
        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($overall_result as $k => $row) {

            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = $p . $th;
                $i++;
            } else {
                $pos = $possition;
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $pos;

            if ($row->obtained_mark == $obtained_mark_single) {
                return $pos;
            }
        }
    }

    function candidate_possition($candidate_type, $subject_name, $exam_id, $obtained_mark_single, $all_exam_results = null)
    {
        $results = $subjectresult = $all_exam_results
            ? $all_exam_results->where('subject.name', $subject_name)
            : Result::with('doctor_course')
            ->join('subjects', 'subjects.id', '=', 'results.subject_id')
            ->where(['subjects.name' => $subject_name,  'exam_id' => $exam_id])
            ->orderBy('obtained_mark', 'desc')
            ->get();
        // dd($results->toArray());

        $subjectresult = $results->where('doctor_course.candidate_type', $candidate_type);
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

            if ($row->obtained_mark == $obtained_mark_single) {
                return $pos;
            }
        }
    }

    function subject_possition($subject_name, $exam_id, $obtained_mark_single, $all_exam_results = null)
    {
        $subjectresult = $all_exam_results
            ? $all_exam_results->where('subject.name', $subject_name)
            : Result::join('subjects', 'subjects.id', '=', 'results.subject_id')
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
            if ($row->obtained_mark == $obtained_mark_single) {
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


    function batch_possition($batch_id, $exam_id, $obtained_mark_single, $exam_batch_results = null)
    {
        $batchresult = $exam_batch_results ?? Result::where(['batch_id' => $batch_id, 'exam_id' => $exam_id])->orderBy('obtained_mark', 'desc')->get();
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

            if ($row->obtained_mark == $obtained_mark_single) {
                return $pos;
            }
        }
    }


    public function result_submit_old(Request $request)
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
        $mcq_mark = $question_type->mcq_mark / 5;
        $mcq_negative_mark = $question_type->mcq_negative_mark;
        $sba_mark = $question_type->sba_mark;
        $sba_negative_mark = $question_type->sba_negative_mark;
        $exam_question_ids = Exam_question::where('exam_id', $exam_id)->get();
        $result_insert = 0;

        $paper_faculty = $question_type->paper_faculty;


        //Result File save to result/exam_id/file_name
        $file_name = time() . "_" . $file->getClientOriginalName();
        $file_Path = 'result/' . $request->exam_id . '/';
        $file->move($file_Path, $file_name);

        for ($i = 0; $i < count($result); $i++) {  //echo 'ok';
            $ind_result = $result[$i];
            $registration =  substr($exam_year, 2, 2) . substr($ind_result, 0, 6);

            $doctors_course = DoctorsCourses::query()
                ->where([
                    'reg_no'    => $registration,
                    'is_trash'  => 0,
                ])
                ->first();

            $doctor_course_id = $doctors_course->id;
            $batch_id = $doctors_course->batch_id;
            $course_id = $doctors_course->course_id;

            $doctor_exam = DoctorExam::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $request->exam_id])->first() ?? new DoctorExam();
            $doctor_exam->status = 'Completed';
            $doctor_exam->doctor_course_id = $doctor_course_id;
            $doctor_exam->exam_id = $request->exam_id;
            $doctor_exam->save();

            $omr_subject_code = substr($ind_result, 8, 1);
            //            $subject_id = Subjects::where(['course_id'=>$course_id,'subject_omr_code'=> $omr_subject_code])->value('id');

            $set_code = substr($ind_result, 6, 1);


            if ($paper_faculty == 'Paper') {
                $paper_code = substr($ind_result, 6, 1);
                $faculty_id = '';
            } elseif ($paper_faculty == 'Faculty') {
                $omr_faculty_code = substr($ind_result, 7, 1);
                //                $faculty_id = BcpsFaculty::where('omr_code', $omr_faculty_code)->value('id');
                $paper_code = '';
            } else {
                $faculty_id = '';
                $paper_code = '';
            }

            $subject_id = $doctors_course->subject_id ?? '';
            $faculty_id = $doctors_course->faculty_id ?? '';

            if ($doctor_course_id && !(Result::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->exists())) {

                $result_insert++;
                if ($paper_faculty == 'None') {
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

                $total_question = $question_type->mcq_number + $question_type->sba_number;

                $sba_correct_answer = substr($canswertotal, $question_type->mcq_number * 5);

                for ($x = 0; $x < $total_question; $x++) {


                    if ($question_type->mcq_number > $x) {
                        $y = 0;
                        $asb = substr($mcqans, $m, 5);

                        $correcans = str_split(substr($canswertotal, $m, 5));

                        for ($j = 0; $j < 5; $j++) {
                            if ($correcans[$j] == substr($asb, $y, 1)) {
                                $mark += $mcq_mark;
                            } else {

                                if (substr($asb, $y, 1) != '.') {
                                    $n_mark += $mcq_negative_mark;
                                    if (substr($asb, $y, 1) != '>') {
                                        $wrong_answer++;
                                    }
                                }
                            }
                            ++$y;
                        }
                        $m += 5;
                    } else {
                        if (substr($sba_correct_answer, $s, 1) == substr($sbaans, $s, 1)) {
                            $mark += $sba_mark;
                        } else {
                            if (substr($sbaans, $s, 1) != '.') {
                                $n_mark += $sba_negative_mark;
                                if (substr($sbaans, $s, 1) != '>') {
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
                    'subject_id' => $subject_id,
                    'correct_mark' => $mark,
                    'negative_mark' => $n_mark,
                    'obtained_mark' => $mark - $n_mark,
                    'obtained_mark_decimal' => ($mark - $n_mark) * 100,
                    'wrong_answers' => $wrong_answer,
                    'batch_id' => $batch_id,
                    'file_name' => $file_name,
                    'paper_code' => $paper_code,
                    'faculty_id' => $faculty_id
                ]);

                $doctor_course = DoctorsCourses::find($doctor_course_id);
                $doctor = Doctors::find($doctor_course->doctor_id);
                $website = 'https://genesisedu.info';
                $sms = "Dear Doctor\nyour {$exam->name} exam result published, please visit " . $website;
                $sms = Sms::init()->setRecipient($doctor->mobile_number)->setText($sms);
                $sms->send();
                $sms->save_log('OMR Result Published', $doctor->id);
            }
        }

        $result_insert = ($result_insert == 0) ? 'No result Added' : $result_insert . ' results added successfully';
        Session::flash('message', $result_insert);

        return redirect('admin/view-result/' . $exam_id);
    }

    public function result_submit_faculty(Request $request)
    {
        //$this->result_combined($request);exit;
        $file_front_part = $request->file('result_front_part');
        $file_back_part = $request->file('result_back_part');
        $file_last_part = $request->file('result_last_part');
        $answer = $request->file('answer');

        $result_front_part = explode("\r\n", file_get_contents($file_front_part->getRealPath()));
        if ($file_back_part) {
            $result_back_part = explode("\r\n", file_get_contents($file_back_part->getRealPath()));
        }
        if ($file_last_part) {
            $result_last_part = explode("\r\n", file_get_contents($file_last_part->getRealPath()));
        }

        $canswertotal = file_get_contents($answer->getRealPath());


        //Result File save to result/exam_id/file_name
        $file_name = time() . "_" . $file_front_part->getClientOriginalName();
        $file_Path = 'result/' . $request->exam_id . '/';
        $file_front_part->move($file_Path, $file_name);

        $exam_id = $request->exam_id;
        $question_type_id = Exam::where('id', $exam_id)->first()->question_type_id;

        $exam_year = Exam::where('id', $exam_id)->first()->year;
        $exam = Exam::where('id', $exam_id)->first();

        $question_type = QuestionTypes::where('id', $question_type_id)->first();
        $exam_question_ids = Exam_question::where('exam_id', $exam_id)->get();

        $mcq_mark = $question_type->mcq_mark / 5;
        $mcq_negative_mark = $question_type->mcq_negative_mark;

        $mcq2_mark = $question_type->mcq2_mark / 5;
        $mcq2_negative_mark = $question_type->mcq2_negative_mark;


        $sba_mark = $question_type->sba_mark;
        $sba_negative_mark = $question_type->sba_negative_mark;

        $result_insert = 0;


        for ($i = 0; $i < count($result_front_part); $i++) {  //print_r($result_back_part);exit;
            $ind_result = $result_front_part[$i];
            $registration_without_year = substr($ind_result, 0, 6);
            $registration =  substr($exam_year, 2, 2) . $registration_without_year;

            $doctors_course = DoctorsCourses::query()
                ->where([
                    'reg_no'    => $registration,
                    'is_trash'  => 0,
                ])
                ->first();

            $doctor_course_id = $doctors_course->id ?? 0;
            $course_id = $doctors_course->course_id ?? 0;

            $doctor_exam = DoctorExam::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $request->exam_id])->first() ?? new DoctorExam();

            $doctor_exam->doctor_course_id = $doctor_course_id;
            $doctor_exam->exam_id = $request->exam_id;
            $doctor_exam->status = 'Completed';
            $doctor_exam->save();


            $omr_faculty_code = substr($ind_result, 8, 1);
            //$faculty_id = Faculty::where('faculty_omr_code', $omr_faculty_code)->value('id');

            $batch_id = $doctors_course->batch_id ?? 0;

            $examination_code = substr($ind_result, 6, 1);
            $set_code = substr($ind_result, 7, 1);

            $candidate_code = substr($ind_result, 9, 1);

            $s_code_10 = substr($ind_result, 10, 1);
            $s_code_11 = substr($ind_result, 11, 1);

            $subject_id = $doctors_course->subject_id ?? '';
            $faculty_id = $doctors_course->faculty_id ?? '';

            $not_result = !Result::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->exists();


            if (($registration_without_year && $doctor_course_id  && $not_result)) {

                $result_insert++;


                $ansStart = $exam->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID ? 11 : 12;

                $ind_result = substr($ind_result, $ansStart);

                $input = preg_quote($registration_without_year, '~'); // don't forget to quote input string!

                if (isset($result_back_part)) {
                    $ans_back_part = preg_grep('~' . $input . '~', $result_back_part);
                    $ans_back_part = implode("|", $ans_back_part);
                    $ans_back_part = substr($ans_back_part, 6);
                    $ind_result = trim($ind_result) . trim($ans_back_part);
                }

                if (isset($result_last_part)) {
                    $ans_last_part = preg_grep('~' . $input . '~', $result_last_part);
                    $ans_last_part = implode("|", $ans_last_part);
                    $ans_last_part = substr($ans_last_part, 6);
                    $ind_result = trim($ind_result) . trim($ans_last_part);
                }

                $mcqans = substr($ind_result, 0, $question_type->mcq_number * 5);
                $mcq2ans = substr($ind_result, $question_type->mcq_number * 5, $question_type->mcq2_number * 5);
                $sbaans = substr(
                    $ind_result,
                    ($question_type->mcq_number * 5 + $question_type->mcq2_number * 5),
                    ($question_type->sba_number + $question_type->mcq_number * 5 + $question_type->mcq2_number * 5)
                );

                /*doctor result insert batch wise */
                $mark = 0;
                $m = 0;
                $s = 0;
                $wrong_answer = 0;
                $n_mark = 0;

                $total_question = $question_type->mcq_number + $question_type->sba_number;

                if ($exam->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {
                    $total_question += (int) $question_type->mcq2_number;
                }

                $sba_correct_answer = substr($canswertotal, $question_type->mcq_number * 5 + (int)$question_type->mcq2_number * 5);


                $debug = [];

                for ($x = 0; $x < $total_question; $x++) {

                    $debug_item = &$debug[$x + 1];

                    $debug_item = [];

                    if ($question_type->mcq_number > $x) {

                        $answerCount = $m;

                        $this->setMcqAnswer(
                            $canswertotal,
                            $mcqans,
                            $answerCount,
                            $m,
                            $mcq_mark,
                            $mcq_negative_mark,
                            $mark,
                            $n_mark,
                            $wrong_answer,
                            $debug_item
                        );

                        if ($x == $question_type->mcq_number - 1) {
                            $answerCount = 0;
                        }

                        //
                        //                        $debug_item['mark'] = $mark;
                        //                        $debug_item['n_mark'] = $n_mark;
                        //                        $debug_item['wong_answer'] = $wrong_answer;

                        //                        $y = 0;
                        //                        $asb = substr($mcqans, $m, 5);
                        //
                        //                        $correcans = str_split(substr($canswertotal, $m, 5));
                        //
                        //                        for ($j=0;$j<5;$j++) {
                        //                            if ($correcans[$j] == substr($asb, $y, 1)) {
                        //                                $mark += $mcq_mark;
                        //                            }else{
                        //
                        //                                if(substr($asb, $y, 1)!='.'){
                        //                                    $n_mark += $mcq_negative_mark;
                        //                                    if(substr($asb, $y, 1)!='>'){
                        //                                        $wrong_answer++;
                        //                                    }
                        //
                        //                                }
                        //                            }
                        //                            ++$y;
                        //                        }
                        //                        $m +=5;


                    } else if (($question_type->mcq_number + $question_type->mcq2_number) > $x) {


                        $this->setMcqAnswer(
                            $canswertotal,
                            $mcq2ans,
                            $answerCount,
                            $m,
                            $mcq2_mark,
                            $mcq2_negative_mark,
                            $mark,
                            $n_mark,
                            $wrong_answer,
                            $debug_item
                        );
                    } else {

                        $debug_item['ans'] = $sbaans;
                        $debug_item['cmk'] = substr($sba_correct_answer, $s, 1);
                        if (substr($sba_correct_answer, $s, 1) == substr($sbaans, $s, 1)) {
                            $mark += $sba_mark;
                            $debug_item['obtained'] = $sba_mark;
                        } else {
                            if (substr($sbaans, $s, 1) != '.') {
                                $n_mark += $sba_negative_mark;
                                if (substr($sbaans, $s, 1) != '>') {
                                    $wrong_answer++;
                                    $debug_item['wrong_answer'] = substr($sbaans, $s, 1);
                                }
                            }
                        }
                        $s++;
                    }
                }

                //                dd(
                //                    $result_front_part[$i],
                //                    $this->strAfter( $ind_result ),
                //
                //                    $this->strAfter( $canswertotal ),
                //
                //                    $this->strAfter( $mcqans),
                //                    $this->strAfter( $mcq2ans),
                //                    $sbaans,
                //                    [
                //                        'doctor_course_id' => $doctor_course_id,
                //                        'exam_id' => $exam_id,
                //                        'subject_id'=>$subject_id,
                //                        'correct_mark' => $mark,
                //                        'negative_mark' => $n_mark,
                //                        'obtained_mark' => $mark-$n_mark,
                //                        'obtained_mark_decimal' => ($mark-$n_mark)*100,
                //                        'wrong_answers'=>$wrong_answer,
                //                        'batch_id'=>$batch_id,
                //                        'file_name'=>$file_name,
                //                        'faculty_id'=>$faculty_id,
                //                        'examination_code'=>$examination_code,
                //                        'candidate_code'=>$candidate_code
                //                    ],
                //                    $debug
                //                );

                Result::updateOrinsert(
                    [
                        'doctor_course_id' => $doctor_course_id,
                        'exam_id' => $exam_id
                    ],
                    [
                        'subject_id' => $subject_id,
                        'correct_mark' => $mark,
                        'negative_mark' => $n_mark,
                        'obtained_mark' => $mark - $n_mark,
                        'obtained_mark_decimal' => ($mark - $n_mark) * 100,
                        'wrong_answers' => $wrong_answer,
                        'batch_id' => $batch_id,
                        'file_name' => $file_name,
                        'faculty_id' => $faculty_id,
                        'examination_code' => $examination_code,
                        'candidate_code' => $candidate_code
                    ]
                );


                $doctor_course = DoctorsCourses::find($doctor_course_id);

                $doctor = Doctors::find($doctor_course->doctor_id);
                $website = 'https://genesisedu.info';
                $sms = "Dear Doctor\nyour {$exam->name} exam result published, please visit" . $website;
                $sms = Sms::init()->setRecipient($doctor->mobile_number)->setText($sms);
                $sms->send();
                $sms->save_log('OMR Result Published', $doctor->id);
            }
        }

        $result_insert = ($result_insert == 0) ? 'No result Added' : $result_insert . ' results added successfully';
        Session::flash('message', $result_insert);


        return redirect('admin/view-result/' . $exam_id);
    }

    public function upload_result($id)
    {
        $data['exam_info'] = $exam_id = Exam::find($id);
        $data['title'] = 'Upload Result';
        $data['omr_scripts'] = OmrScript::pluck('name', 'id');
        $result = Result::where(['exam_id' => $id])->first();
        $data['view_result'] = false;
        if (isset($result)) {
            $data['view_result'] = true;
        }
        return view('admin.exam.upload_result', $data);
    }

    public function result_submit(Request $request)
    {
        if ($request->exam_id) {
            $exam = Exam::query()
                ->with([
                    'question_type',
                    'exam_questions:id,exam_id,question_id,question_type',
                    'exam_questions.question:id,type,question_title',
                    'exam_questions.question.question_answers',
                ])
                ->findOrFail($request->exam_id);

            $exam_questions = $exam->exam_questions;

            foreach($exam_questions as &$exam_question) {
                $options = $exam_question->question->question_answers;

                $answer_script = "";

                foreach($options as $option) {
                    $answer_script .= $option->correct_ans ?? ".";
                    if($exam_question->question->type == 2) {
                        break;
                    }
                }

                $exam_question->question->script = $answer_script;
            }

            $omr_script = OmrScript::query()
                ->with('properties.omr_script_property')
                ->findOrFail($request->omr_script_id);

            $front_part_answer_start = $front_part_answer_end = 0;
            $back_part_answer_start = 6;
            $back_part_answer_end = 131;
            $last_part_answer_start = 6;
            $last_part_answer_end = 131;
            $set_code_start = $set_code_end = 0;
            $reg_no_start = $reg_no_end = 0;
            $faculty_start = $faculty_end = 0;
            $discipline_start = $discipline_end = 0;

            foreach ($omr_script->properties as $omr_script_property) {
                if ($omr_script_property->omr_script_property->name == "Set Code") {
                    $set_code_start = $omr_script_property->start_position;
                    $set_code_end = $omr_script_property->start_position;
                }

                if ($omr_script_property->omr_script_property->name == "Reg No") {
                    $reg_no_start = $omr_script_property->start_position;
                    $reg_no_end = $omr_script_property->end_position;
                }

                if ($omr_script_property->omr_script_property->name == "Faculty") {
                    $faculty_start = $omr_script_property->start_position;
                    $faculty_end = $omr_script_property->end_position;
                }

                if($omr_script_property->omr_script_property->name == "Subject")
                {
                    $discipline_start = $omr_script_property->start_position;
                    $discipline_end = $omr_script_property->end_position;
                }

                if ($omr_script_property->omr_script_property->name == "Front Part Answer") {
                    $front_part_answer_start = $omr_script_property->start_position;
                    $front_part_answer_end = $omr_script_property->end_position;
                }

                if ($omr_script_property->omr_script_property->name == "Back Part Answer") {
                    $back_part_answer_start = $omr_script_property->start_position;
                    $back_part_answer_end = $omr_script_property->end_position;
                }

                if ($omr_script_property->omr_script_property->name == "Last Part Answer") {
                    $last_part_answer_start = $omr_script_property->start_position;
                    $last_part_answer_end = $omr_script_property->end_position;
                }
            }

            $file_front_part = $request->file('result_front_part');
            $file_back_part = $request->file('result_back_part');
            $file_last_part = $request->file('result_last_part');

            $result_front_part = $result_back_part = $result_last_part = array();

            $result_front_part = explode("\r\n", file_get_contents($file_front_part->getRealPath()));
            if ($file_back_part) {
                $result_back_part = explode("\r\n", file_get_contents($file_back_part->getRealPath()));
            }
            if ($file_last_part) {
                $result_last_part = explode("\r\n", file_get_contents($file_last_part->getRealPath()));
            }

            $year = substr(trim($exam->year), 2, 4);
            $doctor_answers_front_part = array();

            foreach($result_front_part as $k=>$front_part)
            {                
                if(isset($front_part) && strlen($front_part)>1)
                {
                    $string_answer = '';
                    $reg_no = $year.substr(trim($front_part), $reg_no_start, $reg_no_end);
                    $string_answer .= substr(trim($front_part), $front_part_answer_start,$front_part_answer_end);
                    $doctor_answers_front_part[$reg_no] = $string_answer;
                }
            }

            $doctor_answers_back_part = array();
            foreach ($result_back_part as $k => $back_part) {
                if (isset($back_part) && strlen($back_part) > 1) {
                    $string_answer = '';
                    $reg_no = $year . substr(trim($back_part), 0, 6);
                    $string_answer .= substr(trim($back_part), $back_part_answer_start, $back_part_answer_end);
                    $doctor_answers_back_part[$reg_no] = $string_answer;
                }
            }

            $doctor_answers_last_part = array();
            foreach ($result_last_part as $k => $last_part) {
                if (isset($last_part) && strlen($last_part) > 1) {
                    $string_answer = '';
                    $reg_no = $year . substr(trim($last_part), 0, 6);
                    $string_answer .= substr(trim($last_part), $last_part_answer_start, $last_part_answer_end);
                    $doctor_answers_last_part[$reg_no] = $string_answer;
                }
            }

            $doctor_given_row_answers = array();
            foreach ($doctor_answers_front_part as $k => $front_part) {
                $doctor_given_row_answers[$k] = $front_part;
                if (key_exists($k, $doctor_answers_back_part)) {
                    $doctor_given_row_answers[$k] .= $doctor_answers_back_part[$k];
                }
                if (key_exists($k, $doctor_answers_last_part)) {
                    $doctor_given_row_answers[$k] .= $doctor_answers_last_part[$k];
                }
            }

            // foreach($doctor_given_row_answers as $registration => $doctor_answer) {
            //     return $this->checkAnserScript($exam, $registration, $doctor_answer);
            // }

            // return "ok";

            //echo "<pre>";print_r($doctor_given_row_answers);exit;

            $correct_answers = array();
            $string_correct_answers = '';
            $m = 0;
            foreach ($exam->exam_questions as $k => $exam_question) {
                if (isset($exam_question->question->question_title)) {
                    if ($exam_question->question->type == "1" || $exam_question->question->type == "3") {
                        foreach ($exam_question->question->question_answers as $k => $answer) {
                            $correct_answers[$exam_question->id][$exam_question->question->id][$exam_question->question_type][$answer->sl_no] = $answer->correct_ans;
                            $string_correct_answers .= $answer->correct_ans;
                        }
                    } else if ($exam_question->question->type == "2" || $exam_question->question->type == "4") {
                        foreach ($exam_question->question->question_answers as $k => $answer) {
                            $correct_answers[$exam_question->id][$exam_question->question->id][$exam_question->question_type][$answer->sl_no] = $answer->correct_ans;
                            $string_correct_answers .= $answer->correct_ans;
                            break;
                        }
                    }
                }
            }

            // return $string_correct_answers;

            $doctor_given_answers = array();
            foreach ($doctor_given_row_answers as $l => $doctor_answers) {
                $doctor_course = DoctorsCourses::query()
                    ->where([
                        'reg_no'    => $l,
                        'is_trash'  => 0,
                    ])
                    ->first();

                $doctor_exam = DoctorExam::updateOrCreate(
                    [
                        'exam_id'           => $exam->id,
                        'doctor_course_id'  => $doctor_course->id
                    ],
                    [
                        'status'            => "Completed",
                        'answers_file_link' => 'exam_answers/' . $doctor_course->doctor_id,
                    ]
                );

                if (isset($doctor_course) && isset($doctor_answers) && strlen($doctor_answers) > 1) {
                    $m = 0;
                    $array_doctor_answers = str_split($doctor_answers, 1);
                    if (is_array($array_doctor_answers) && count($array_doctor_answers) > 0) {
                        foreach ($correct_answers as $k => $correct_answer) {
                            if (is_array($correct_answer)) {
                                foreach ($correct_answer as $n => $ca) {
                                    foreach ($ca as $y => $as) {
                                        foreach ($as as $z => $a) {
                                            $doctor_given_answers[$doctor_course->id][$k][$n][$y][$z] = $array_doctor_answers[$m++];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $doctor_given_answers_formated = array();

                    foreach ($doctor_given_answers as $k => $doctor_given_answer) {
                        foreach ($doctor_given_answer as $l => $doctor_given_ans) {
                            foreach ($doctor_given_ans as $m => $doctor_given_an) {
                                foreach ($doctor_given_ans as $n => $doctor_given_a) {
                                    foreach ($doctor_given_a as $o => $a) {
                                        $doctor_given_answers_formated[$k][$l][$m][$o]['ans'] = implode('', $a);
                                    }
                                }
                            }
                        }
                    }

                    $calculated_data = $this->checkAnserScript($exam, $doctor_answers);

                    $doctorResultData = [
                        'exam_id' => $exam->id,
                        'doctor_course_id' => $doctor_course->id,
                        'subject_id' => $doctor_course->subject_id ?? '',
                        'faculty_id' => $doctor_course->faculty_id ?? '',
                        'bcps_subject_id' => $doctor_course->bcps_subject_id ?? '',
                        'candidate_code' => $doctor_course->candidate_type ?? '',
                        'batch_id' => $doctor_course->batch_id ?? '',
                        'correct_mark' => $calculated_data['correct_mark'],
                        'negative_mark' => $calculated_data['negative_mark'],
                        'obtained_mark' => $calculated_data['obtained_mark'],
                        'obtained_mark_percent' => $calculated_data['obtained_mark_percent'],
                        'obtained_mark_decimal' => $calculated_data['obtained_mark_decimal'],
                        'wrong_answers' => $calculated_data['wrong_answers'],
                    ];

                    Result::updateOrCreate(
                        [
                            'exam_id' => $exam->id,
                            'doctor_course_id' => $doctor_course->id
                        ],
                        $doctorResultData,
                    );

                    $this->result_publish($doctor_given_answers_formated, $exam, $doctor_course);
                }
            }

            // return $this->preOutput(313);

            Session::flash('message',isset($doctor_given_answers_formated), count($doctor_given_answers_formated) . " Results added successfully !!!");
            return redirect('admin/upload-result/' . $exam->id);
        }
    }

    public function checkAnserScript($exam, $doctor_answer)
    {
        $mcq_number = $exam->question_type->mcq_number ?? 0;
        $sba_number = $exam->question_type->sba_number ?? 0;
        $mcq2_number = $exam->question_type->mcq2_number ?? 0;
        
        $exam_answer_script = '';

        foreach($exam->exam_questions as $exam_question) {
            $exam_answer_script .= $exam_question->question->script;
        }

        $doctor_answer_array = str_split(str_replace(">", ".", $doctor_answer));
        $answer_script_array = str_split($exam_answer_script);

        $correct_mark = $negative_mark = $obtained_mark = $obtained_mark_percent = $obtained_mark_decimal = $correct_answers = $wrong_answers = 0;
        
        foreach($answer_script_array as $index => $answer) {
            if($doctor_answer_array[$index] == '.') {
                continue;
            }

            if($index < ($mcq_number * 5)) {
                $mark = abs($exam->question_type->mcq_mark/5);
                $negative = abs($exam->question_type->mcq_negative_mark);
            } elseif ($index < ($mcq_number * 5) + $sba_number) {
                $mark = abs($exam->question_type->sba_mark);
                $negative = abs($exam->question_type->sba_negative_mark);
            } else {
                $mark = abs($exam->question_type->mcq2_mark/5);
                $negative = abs($exam->question_type->mcq2_negative_mark);
            }

            if($doctor_answer_array[$index] == $answer) {
                $correct_mark += $mark;
                $correct_answers++;
            } else {
                $wrong_answers++;
                $negative_mark += $negative;
            }
        }

        $obtained_mark = round($correct_mark - $negative_mark, 2);
        $obtained_mark_decimal = round($obtained_mark) * 10;
        $obtained_mark_percent = round(($obtained_mark/$exam->question_type->full_mark) * 100, 2);

        return compact(
            'correct_mark',
            'negative_mark',
            'obtained_mark',
            'obtained_mark_percent',
            'obtained_mark_decimal',
            'correct_answers',
            'wrong_answers',
        );
    }

    public function result_publish($doctor_given_answers, $exam, $doctor_course)
    {

        $now = $date = new \DateTime("now", new \DateTimeZone('Asia/Dhaka'));
        foreach ($doctor_given_answers as $k => $doctor_given_answer) {
            $answers = array();
            if (isset($doctor_course)) {
                foreach ($doctor_given_answer as $l => $doctor_given_ans) {
                    foreach ($doctor_given_ans as $m => $doctor_given_an) {
                        foreach ($doctor_given_an as $n => $ans) {
                            $answers[$m] = [
                                'exam_question_id' => $l,
                                'answer' => $ans['ans'],
                                'question_type' => $n,
                            ];
                        }
                    }
                }

                $file_path = public_path('exam_answers/' . $doctor_course->doctor_id);

                $file = public_path('exam_answers/' . $doctor_course->doctor_id . '/' . $exam->id . '_' . $doctor_course->id . ".json");

                clearstatcache();
                if (!is_dir($file_path)) {
                    mkdir($file_path, 0777, true);
                }
                clearstatcache();
                $txt = json_encode($answers);
                file_put_contents($file, $txt);

                // $calculated_data = $this->calculate_marks($exam->id, $doctor_course->id); //echo "<pre>";print_r($calculated_data);exit;
            }
        }

        $website = 'https://genesisedu.info';
        $sms = "Dear Doctor your {$exam->name} exam result published, please visit " . $website;
        // $sms = Sms::init()->setRecipient($doctor_course->doctor->mobile_number)->setText($sms);
        // $sms->send();
        // $sms->save_log('OMR Result Published', $doctor_course->doctor->id);
        $this->send_custom_sms($doctor_course->doctor,$sms,'OMR Result Published',$isAdmin = true);

        return true;
    }

    public function calculate_marks($exam_id, $doctor_course_id)
    {
        $exam = Exam::query()
            ->with([
                'question_type',
                'exam_questions.question.question_answers',
                'exam_questions.exam.question_type',
            ])
            ->where(['id' => $exam_id])->first();

        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        $doctor_exam = DoctorExam::where(['doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id])->first();

        $given_answers = $doctor_exam->get_given_answers();

        $mcq_correct_mark =  $mcq2_correct_mark = $sba_correct_mark = 0;
        $mcq_negative_mark = $mcq2_negative_mark = $sba_negative_mark = 0;
        $mcq_wrong_answer = $mcq2_wrong_answer = $sba_wrong_answer = 0;

        foreach ($given_answers as $given_answer) {
            $exam_question = $exam->exam_questions
                ->where('id', $given_answer['exam_question_id'])
                ->first();

            $count_question_answer = count($exam_question->question->question_answers);

            if ($given_answer['question_type'] == 1 || $given_answer['question_type'] == 3) {
                if ($given_answer['question_type'] == 1) {
                    foreach ($exam_question->question->question_answers as $index => $question_answer) {
                        if (substr($given_answer['answer'], $index, 1) == $question_answer['correct_ans']) {
                            if ($given_answer['question_type'] == 1) {
                                $mcq_correct_mark += $exam_question->exam->question_type->mcq_mark / $count_question_answer;
                            }
                        } else if (substr($given_answer['answer'], $index, 1) != ".") {
                            $mcq_negative_mark += $exam_question->exam->question_type->mcq_negative_mark;
                            $mcq_wrong_answer++;
                        }
                    }
                } else if ($given_answer['question_type'] == 3) {
                    foreach ($exam_question->question->question_answers as $index => $question_answer) {
                        if (substr($given_answer['answer'], $index, 1) == $question_answer['correct_ans']) {
                            if ($given_answer['question_type'] == 3) {
                                $mcq2_correct_mark += $exam_question->exam->question_type->mcq2_mark / $count_question_answer;
                            }
                        } else if (substr($given_answer['answer'], $index, 1) != ".") {
                            $mcq2_negative_mark += $exam_question->exam->question_type->mcq2_negative_mark;
                            $mcq2_wrong_answer++;
                        }
                    }
                }
            } else if ($given_answer['question_type'] == 2 || $given_answer['question_type'] == 4) {
                foreach ($exam_question->question->question_answers as $index => $question_answer) {
                    if (substr($given_answer['answer'], $index, 1) == $question_answer['correct_ans']) {
                        if ($given_answer['question_type'] == 2) {
                            $sba_correct_mark += $exam_question->exam->question_type->sba_mark;
                        }
                    } else if (substr($given_answer['answer'], $index, 1) != "." && $given_answer['answer'] !== null) {
                        $sba_negative_mark += $exam_question->exam->question_type->sba_negative_mark;
                        $sba_wrong_answer++;
                    }
                    break;
                }
            }
        }
        //echo "<pre>";print_r($sba_wrong_answer);exit;
        $data['correct_mark'] = $mcq_correct_mark + $mcq2_correct_mark + $sba_correct_mark;
        $data['negative_mark'] = $mcq_negative_mark + $mcq2_negative_mark + $sba_negative_mark;
        $data['wrong_answer'] = $mcq_wrong_answer + $mcq2_wrong_answer + $sba_wrong_answer;
        $data['obtained_mark'] = $data['correct_mark'] - $data['negative_mark'];
        $data['obtained_mark_percent'] = $data['obtained_mark'] * 100 / $exam->question_type->full_mark;
        $data['obtained_mark_decimal'] = $data['obtained_mark'] * 10;

        return $data;
    }

    protected function strAfter($txt, $after = 5, $str = " ")
    {

        $output = "";

        for ($i = 0; $i < strlen($txt); $i = $i + $after) {
            for ($j = 0; $j < 5; $j++) {
                $output .= ($txt[$i + $j] ?? '');
            }
            $output .= $str;
        }

        return $output;
    }

    protected function setMcqAnswer($correctAnswers, $answers, &$answerCount, &$globalCount, $assigned_mark, $assigned_ng_mark, &$mark, &$ng_mark, &$wrong_answer, &$d = [])
    {
        $serial = 0;

        $slAl = ['A', 'B', 'C', 'D', 'E'];

        $answers_string = substr($answers, $answerCount, 5);
        $correct_ans = str_split(substr($correctAnswers, $globalCount, 5));

        $d['answer'] = $answers_string;
        $d['correct_answer'] = [];
        $d['aa'] = $this->strAfter($correctAnswers, 5, "|");
        $d['ab'] = $this->strAfter($answers, 5, "|");

        for ($j = 0; $j < 5; $j++) {
            if ($correct_ans[$j] == substr($answers_string, $serial, 1)) {
                $d['correct_answer'][$slAl[$serial]] = $correct_ans[$j];
                $mark += $assigned_mark;
            } else {
                if (substr($answers_string, $serial, 1) != '.') {
                    $ng_mark += $assigned_ng_mark;
                    if (substr($answers_string, $serial, 1) != '>') {
                        $wrong_answer++;
                        $d['wrong_answer'][$slAl[$serial]] = 1;
                    } else {
                        $d['not_answered'][$slAl[$serial]] = 1;
                    }
                } else {
                    $d['not_answered'][$slAl[$serial]] = 1;
                }
            }
            ++$serial;
        }

        $answerCount += 5;
        $globalCount += 5;
    }

    public function result_excel($paras = null)
    {
        session(['paras' => $paras]);

        $params_array = explode('_', $paras);

        $batch = Batches::where('id', $params_array[2])->value('name');
        $file_name = str_replace(' ', '_', $batch) . '_' . $params_array[0];


        return (new ResultExport(1))->download($file_name . '.xlsx');
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
        if (!empty($year)) {
            $string .= ' `b`.`year`="' . $year . '"';
        }
        if (!empty($course_id)) {
            $string .= ' and `dc`.`course_id`="' . $course_id . '"';
        }
        if (!empty($session_id)) {
            $string .= ' and `b`.`session_id`="' . $session_id . '"';
        }
        if (!empty($batch_id)) {
            $string .= ' and `r`.`batch_id`="' . $batch_id . '"';
        }
        if (!empty($exam_id)) {
            $string .= ' and `r`.`exam_id`="' . $exam_id . '"';
        }

        $string .= " ) ";


        $raw = 'select `r`.`exam_id` as `exam_id`, `e`.`institute_id` as `institute_id`, `dc`.`candidate_type` as `candidate_type`,`dc`.`course_id`, `r`.`id` as `id`, `d`.`name` as `doctor_name`, `dc`.`reg_no` as `reg_no`, `r`.`batch_id` as `batch_id`, `b`.`name` as `batch_name`, `s`.`name` as `discipline_name`, `r`.`obtained_mark` as `obtained_mark`, `r`.`wrong_answers` as `wrong_answer` from `results` as `r` left join `batches` as `b` on `r`.`batch_id` = `b`.`id` left join `exam` as `e` on `r`.`exam_id` = `e`.`id` left join `doctors_courses` as `dc` on `r`.`doctor_course_id` = `dc`.`id` left join `doctors` as `d` on `dc`.`doctor_id` = `d`.`id` left join `subjects` as `s` on `r`.`subject_id` = `s`.`id` ' . $string . ' order by `dc`.`id` asc';

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

        foreach ($results as $result) {
            $array[] = [
                'reg_no' => $result->reg_no,
                'doctor_name' => $result->doctor_name,
                'batch' => $result->batch_name,
                'discipline' => $result->discipline_name,
                'obtained_mark' => $result->obtained_mark,
                'wrong_answer' => $result->wrong_answer,
                'discipline_position' => $this->subject_possition($result->discipline_name, $result->exam_id, $result->obtained_mark),
                'batch_position' => $this->batch_possition($result->batch_id, $result->exam_id, $result->obtained_mark),
                'candidate_position' => $this->candidate_possition($result->candidate_type, $result->discipline_name, $result->exam_id, $result->obtained_mark),
                'overall_position' => $this->overall_possition($result->exam_id, $result->obtained_mark),
            ];
        }

        return Excel::download(new ResultExport($array), 'download.xlsx');
    }

    public function batch_wise_result_print(Request $request)
    {
        // return
        $all_exam_results = Result::query()
            ->with([
                'doctor_course:id,doctor_id,reg_no,candidate_type',
                'doctor_course.doctor:id,name',
                'subject:id,name',
                'faculty:id,name',
            ])
            ->where('exam_id', $request->exam)
            ->orderBy('obtained_mark', 'desc')
            ->get([
                'id',
                'doctor_course_id',
                'exam_id',
                'batch_id',
                'subject_id',
                'faculty_id',
                'obtained_mark',
                'wrong_answers'
            ]);

        $results = $all_exam_results;

        if($request->batch) {
            $results = $results->where('batch_id', $request->batch);
        }

        $batch = Batches::where(['id' => $request->batch])->first();

        $exam = Exam::where(['id' => $request->exam])->first();

        // return $exam;

        $array = [];

        foreach ($results as $result) {
            $array[] = [
                'reg_no'                => $result->doctor_course->reg_no,
                'doctor_name'           => $result->doctor_course->doctor->name ?? '',
                'batch_name'            => $result->batch->name ?? '',
                'batch_id'              => $result->batch_id ?? '',
                'subject'               => $result->subject->name ?? '',
                'faculty'               => $result->faculty->name ?? '',
                'obtained_mark'         => $result->obtained_mark ?? '',
                'overall_position'      => $this->overall_possition($result->exam_id, $result->obtained_mark, $all_exam_results),
                'subject_position'      => $this->subject_possition($result->subject->name ?? '', $result->exam_id, $result->obtained_mark, $all_exam_results),
                'batch_position'        => $this->batch_possition($result->batch_id, $result->exam_id, $result->obtained_mark, $results),
                'candidate_position'    => $this->candidate_possition($result->doctor_course->candidate_type, $result->subject->name ?? '', $result->exam_id, $result->obtained_mark, $all_exam_results),
                'wrong_answer'          => $result->wrong_answers,
            ];
        };
        // Sort by Reg_no
        // usort($array, function ($a, $b) {
        //     return strcmp($a['reg_no'] ?? '', $b['reg_no'] ?? '');
        // });

        // return $array;

        return view('tailwind.admin.batch-wise-result.batch-wise-result-print', compact(
            'array',
            'batch',
            'exam'
        )); // for tailwind

    }
}
