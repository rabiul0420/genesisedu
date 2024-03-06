<?php

namespace App\Http\Controllers\Admin;

use App\Chapters;
use App\Doctors;
use App\Exam_question;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Sms;
use App\Label;
use App\LogHistory;
use App\McqQuestionEditHistory;
use App\QuestionReference;
use App\QuestionReferenceExam;
use App\ReferenceCourse;
use App\ReferenceFaculty;
use App\ReferenceInstitute;
use App\ReferenceSession;
use App\ReferenceSubject;
use App\Subjects;
use App\Topics;
use Illuminate\Http\Request;
use App\Question;
use App\QuestionSubject;
use App\QuestionTopic;
use App\QuestionChapter;
use App\Question_ans;
use App\QuestionLabel;
use App\QuestionReferenceBook;
use App\QuestionVideoLink;
use App\ReferenceBook;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Validator;
use Yajra\DataTables\DataTables;


class QuestionsController extends Controller
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

    public function set_subject_chapter_topic(&$data)
    {
        $mentor = Auth::user()->question();

        $data['subjects'] = $mentor->subjects->pluck('subject_name', 'id');
        $data['chapters'] = $mentor->chapters->pluck('chapter_name', 'id');
        $data['topics'] = $mentor->topics->pluck('topic_name', 'id');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $this->set_subject_chapter_topic($data);

        $data['references']        = QuestionReferenceExam::orderBy('reference_code', 'asc')->pluck('reference_code', 'id');
        $data['source_institutes'] = ReferenceInstitute::pluck('name', 'id');
        $data['source_courses']    = ReferenceCourse::groupBy('name')->pluck('name', 'id');
        $data['source_faculties']  = ReferenceFaculty::groupBy('name')->pluck('name', 'id');
        $data['source_subjects']   = ReferenceSubject::groupBy('name')->pluck('name', 'id');
        $data['source_sessions']   = ReferenceSession::groupBy('name')->pluck('name', 'id');
        $data['print_allows']       = Setting::property('question_print_allow')->first();

        $data['years']             = array('' => 'Select year');
        for ($year = date("Y") + 1; $year >= 2000; $year--) {
            $data['years'][$year] = $year;
        }

        $data['title'] = 'MCQ List';

        return view('admin.questions.list', $data);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function mcq_list(Request $request)
    {
        //$mcq_list = Question::where('type', 1)->orderBy('id', 'desc')->select('*');
        $subject_id = $request->subject_id;
        $chapter_id = $request->chapter_id;
        $topic_id = $request->topic_id;
        $source_institute_id = $request->source_institute_id;
        $source_course_id = $request->source_course_id;
        $source_faculty_id = $request->source_faculty_id;
        $source_subject_id = $request->source_subject_id;
        $source_session_id = $request->source_session_id;
        $year = $request->year;
        $reference_id = $request->references;

        $mcq_list = DB::table('questions as d1')->where(['type' => 1])
            ->leftjoin('ques_topics as d2', 'd1.topic_id', '=', 'd2.id')
            ->leftjoin('ques_chapters as d3', 'd1.chapter_id', '=', 'd3.id')
            ->leftjoin('ques_subjects as d4', 'd1.subject_id', '=', 'd4.id')
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

        if ($reference_id) {
            $references = QuestionReference::query()
                ->where('reference_id', $reference_id)
                ->pluck('question_id')
                ->toArray() ?? [];

            $mcq_list->whereIn('d1.id', $references);
        }

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

        $mcq_list = $mcq_list->select('d1.id', 'd1.question_title', 'd2.topic_name as topic_name', 'd3.chapter_name as chapter_name', 'd4.subject_name as subject_name');



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
                $data['mcq_list'] = $mcq_list;
                //$data['question_answers'] = Question_ans::where('question_id',$mcq_list->id)->get();
                //$data['subject'] = QuestionSubject::where(['id'=>$mcq_list->subject_id])->get();
                //$data['chapter'] = QuestionChapter::where(['id'=>$mcq_list->chapter_id])->get();
                //$data['topic'] = QuestionTopic::where(['id'=>$mcq_list->topic_id])->get();
                return view('admin.questions.ajax_list', $data);
            })
            ->rawColumns(['question_title', 'references', 'action'])
            ->make(true);
    }

    public function question_view(Request $request)
    {
        $question = Question::where('id', $request->question_id)->first();
        return view('admin.ajax.mcq_search.question_view', compact('question'));
    }

    public function question_edit_log(Request $request)
    {

        $log_histories = LogHistory::with('user')->where(['loghistory_type' => 'App\Question', 'loghistory_id' => $request->question_id])->orderby('id', 'desc')->get();
        //    foreach($log_histories as $log_history){
        //       $details = ($log_history->details);
        //       return var_dump($details);
        //    }
        //    if($log_history->details != null){
        //     $log_history->
        //    }
        return view('admin.ajax.mcq_search.question_edit_history', compact('log_histories'));
    }


    public function create()
    {

        $previous_url = url()->previous();
        $previous_url = explode('/', $previous_url);

        $ques = Auth::user()->question();
        $data['subjects'] = $ques->subjects->pluck('subject_name', 'id');

        $data['prev_create'] = 'yes';
        if (!(isset($previous_url[5]) && $previous_url[5] == 'create')) {
            session(['subject_id' => '', 'chapter_id' => '', 'topic_id' => '']);
            $data['prev_create'] = 'no';
        } else {
            $data['chapters'] = $ques->chapters->where(['subject_id' => session('subject_id')])
                ->orderBy('chapter_name', 'asc')->pluck('chapter_name', 'id');
            $data['topics'] = $ques->topics->where(['subject_id' => session('subject_id'), 'chapter_id' => session('chapter_id')])
                ->orderBy('topic_name', 'asc')->pluck('topic_name', 'id');
        }


        $data['question_references'] = QuestionReferenceExam::orderBy('reference_code', 'asc')->pluck('reference_code', 'id');

        $data['question_reference_books'] = ReferenceBook::pluck('name', 'id');

        $data['labels'] = Label::query()
            ->orderBy('name')
            ->where('status', 1)
            ->get();

        $data['module_name'] = 'MCQ Question';
        $data['title'] = 'MCQ Question Create';
        $data['breadcrumb'] = explode('/', $_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.questions.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->references;
        $validator = Validator::make($request->all(), [
            'question_title' => ['required'],
            'subject_id' => ['required'],
            'chapter_id' => ['required'],
            'topic_id' => ['required'],
        ]);

        if ($validator->fails()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Please input valid data');
            return redirect()->action('Admin\QuestionsController@create')->withInput();
        }

        $array_data = array_values(array_filter(explode(PHP_EOL, $request->question_title), [$this, 'filter_empty_row']));

        if (Question::where(['subject_id' => $request->subject_id, 'chapter_id' => $request->chapter_id, 'topic_id' => $request->topic_id, 'question_title' => $array_data[0]])->exists()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'This Question  already exists');
            return redirect()->action('Admin\QuestionsController@create')->withInput();
        }

        if (count($array_data) != 7) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'This Question ans format is not correct');
            return redirect()->action('Admin\QuestionsController@create')->withInput();
        }

        $tr = str_split(strip_tags($array_data[6]));

        if (count($tr) != 5) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'This Question ans format is not correct');
            return redirect()->action('Admin\QuestionsController@create')->withInput();
        }

        session(['subject_id' => $request->subject_id, 'chapter_id' => $request->chapter_id, 'topic_id' => $request->topic_id]);

        $question = new Question;
        $question->subject_id = $request->subject_id;
        $question->chapter_id = $request->chapter_id;
        $question->topic_id = $request->topic_id;
        $question->question_title = $array_data[0];
        $question->question_and_answers = $request->question_title;
        $question->type = 1;
        // $question->reference = $request->reference;
        $question->discussion = $request->discussion;
        // $question->video_link = $request->video_link;
        $question->video_password = $request->password;

        $question->created_by = Auth::id();

        $question->save();

        $question_id = $question->id;

        $answer = new Question_ans;
        $answer->question_id = $question_id;
        $answer->answer = $array_data[1];
        $answer->sl_no = 'A';
        $answer->correct_ans = $tr[0];
        $answer->save();
        $answer = new Question_ans;
        $answer->question_id = $question_id;
        $answer->answer = $array_data[2];
        $answer->sl_no = 'B';
        $answer->correct_ans = $tr[1];
        $answer->save();
        $answer = new Question_ans;
        $answer->question_id = $question_id;
        $answer->answer =  $array_data[3];
        $answer->sl_no = 'C';
        $answer->correct_ans = $tr[2];
        $answer->save();
        $answer = new Question_ans;
        $answer->question_id = $question_id;
        $answer->answer =  $array_data[4];
        $answer->sl_no = 'D';
        $answer->correct_ans = $tr[3];
        $answer->save();
        $answer = new Question_ans;
        $answer->question_id = $question_id;
        $answer->answer =  $array_data[5];
        $answer->sl_no = 'E';
        $answer->correct_ans = $tr[4];
        $answer->save();

        if (QuestionReference::where('question_id', $question->id)->first()) {
            QuestionReference::where('question_id', $question->id)->delete();
        }

        if ($request->reference_id) {
            foreach ($request->reference_id as $key => $value) {
                if ($value == '') continue;
                QuestionReference::create([
                    'question_id' => $question->id,
                    'reference_id' => $value
                ]);
            }
        }

        if (is_array($request->videos)) {
            foreach ($request->videos as $video) {
                if ($video == '') {
                    continue;
                }

                QuestionVideoLink::insert(
                    [
                        'question_id' => $question->id,
                        'video_link' => $video['link'],
                        'video_password' => $video['password'],
                        'deleted_at' => NULL
                    ]
                );
            }
        }

        //reference book

        $this->saveReferenceBook($request, $question);

        // Save Question Label
        $this->saveQuestionLabel($request, $question);

        Session::flash('message', 'Record has been added successfully');

        //return back();
        if ($request->submit == "submit_add_reference") {
            Session::put('question_id', $question->id);
            return redirect()->action('Admin\QuestionReferenceController@create')->with('question_id', $question->id);
        } else {
            return redirect()->back();
        }
    }

    public function saveQuestionLabel(Request $request, $question)
    {
        QuestionLabel::where('question_id', $question->id)->delete();

        if (is_array($request->labels)) {
            foreach ($request->labels as $label) {

                if ($label) {
                    QuestionLabel::onlyTrashed()->updateOrCreate(
                        [
                            'question_id'   => $question->id,
                        ],
                        [
                            'label_id'      => $label,
                            'deleted_at'    => NULL
                        ]
                    );
                }
            }
        }
    }

    public function saveReferenceBook(Request $request, $question)
    {
        QuestionReferenceBook::where('question_id', $question->id)->delete();

        if (is_array($request->reference_books)) {
            foreach ($request->reference_books as $index => $reference_book) {

                if ($reference_book == '') {
                    continue;
                }

                QuestionReferenceBook::onlyTrashed()->updateOrCreate(
                    [
                        'question_id'       => $question->id,
                    ],
                    [
                        'reference_book_id' => $reference_book,
                        'page_no'           => $request->reference_pages[$index],
                        'deleted_at'        => NULL
                    ]
                );
            }
        }
    }

    public function filter_empty_row($var)
    {
        if ($var != "\r" && $var != "\n") {
            return $var;
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
        $question = Question::find($id);
        return view('admin.questions.show', ['question' => $question]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // return
        $data['question'] = Question::with('question_video_links', 'reference_books')->find($id);
        //        $data['subjects'] = QuestionSubject::pluck('subject_name', 'id');
        //        $data['chapters'] = QuestionChapter::where('subject_id',$data['question']->subject_id)->orderBy('chapter_name','asc')->pluck('chapter_name', 'id');
        //        $data['topics'] = QuestionTopic::where('chapter_id',$data['question']->chapter_id)->orderBy('topic_name','asc')->pluck('topic_name', 'id');

        $ques = Auth::user()->question();

        $data['subjects'] = $ques->subjects->pluck('subject_name', 'id');
        $data['chapters'] = $ques->chapters->where('subject_id', $data['question']->subject_id)->orderBy('chapter_name', 'asc')->pluck('chapter_name', 'id');
        $data['topics'] = $ques->topics->where('chapter_id', $data['question']->chapter_id)->orderBy('topic_name', 'asc')->pluck('topic_name', 'id');

        $data['question_references'] = QuestionReferenceExam::orderBy('reference_code', 'asc')->pluck('reference_code', 'id');
        $question_references = QuestionReference::where('question_id', $data['question']->id)->get();
        $selected_question_references = array();
        foreach ($question_references as $question_reference) {
            $selected_question_references[] = $question_reference->reference_id;
        }

        $data['selected_question_references'] = collect($selected_question_references);

        $data['question_reference_books'] = ReferenceBook::pluck('name', 'id');

        // return $data['question_reference_books'];

        $data['labels'] = Label::query()
            ->orderBy('name')
            ->where('status', 1)
            ->get();

        $data['selected_labels'] = $data['question']->labels()->get()->pluck('id')->toArray();

        $data['module_name'] = 'MCQ Question';
        $data['title'] = 'MCQ Question Edit';
        $data['breadcrumb'] = explode('/', $_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.questions.edit', $data);
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
            'question_title' => ['required'],
            'subject_id' => ['required'],
            'chapter_id' => ['required'],
            'topic_id' => ['required'],
        ]);

        if ($validator->fails()) {
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data");
            return back()->withInput();
        }

        $question = Question::find($id);

        $data['title'] = $question->question_title;
        $data['question_and_answers'] = $question->question_and_answers;
        // $data['reference'] = $question->reference;
        $data['discussion'] = $question->discussion;
        $question->log()->create([
            'user_id' => Auth::id(),
            'details' => json_encode($data),
            'updated_at' => Carbon::now(),
        ]);

        $array_data = array_values(array_filter(explode(PHP_EOL, $request->question_title), [$this, 'filter_empty_row']));

        if ($question->subject_id != $request->subject_id || $question->chapter_id != $request->chapter_id || $question->topic_id != $request->topic_id || $question->question_title != $array_data[0]) {

            if (Question::where(['subject_id' => $request->subject_id, 'chapter_id' => $request->chapter_id, 'topic_id' => $request->topic_id, 'question_title' => $array_data[0]])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This Question  already exists');
                return redirect()->action('Admin\QuestionsController@edit', [$id])->withInput();
            }
        }

        if (count($array_data) != 7) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'This Question ans format is not correct');
            return redirect()->action('Admin\QuestionsController@edit', [$id])->withInput();
        }

        $tr = str_split(strip_tags($array_data[6]));

        if (count($tr) != 5) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'This Question ans format is not correct');
            return redirect()->action('Admin\QuestionsController@edit', [$id])->withInput();
        }

        $question->type = 1;
        $question->subject_id = $request->subject_id;
        $question->chapter_id = $request->chapter_id;
        $question->topic_id = $request->topic_id;
        $question->question_title = $array_data[0];
        $question->question_and_answers = $request->question_title;
        // $question->reference = $request->reference;
        $question->discussion = $request->discussion;
        // $question->video_link = $request->video_link;
        $question->video_password = $request->password;

        $question->updated_by = Auth::id();
        $question->push();


        if (isset($question->question_answers[0])) {
            $question_ans = Question_ans::find($question->question_answers[0]->id);
            $question_ans->answer = $array_data[1];
            $question_ans->correct_ans = $tr[0];
            $question_ans->push();
            $question_ans = Question_ans::find($question->question_answers[1]->id);
            $question_ans->answer = $array_data[2];
            $question_ans->correct_ans = $tr[1];
            $question_ans->push();
            $question_ans = Question_ans::find($question->question_answers[2]->id);
            $question_ans->answer = $array_data[3];
            $question_ans->correct_ans = $tr[2];
            $question_ans->push();
            $question_ans = Question_ans::find($question->question_answers[3]->id);
            $question_ans->answer = $array_data[4];
            $question_ans->correct_ans = $tr[3];
            $question_ans->push();
            $question_ans = Question_ans::find($question->question_answers[4]->id);
            $question_ans->answer = $array_data[5];
            $question_ans->correct_ans = $tr[4];
            $question_ans->push();
        } else {
            $question_id = $question->id;
            $answer = new Question_ans;
            $answer->question_id = $question_id;
            $answer->answer = $array_data[1];
            $answer->sl_no = 'A';
            $answer->correct_ans = $tr[0];
            $answer->save();
            $answer = new Question_ans;
            $answer->question_id = $question_id;
            $answer->answer = $array_data[2];
            $answer->sl_no = 'B';
            $answer->correct_ans = $tr[1];
            $answer->save();
            $answer = new Question_ans;
            $answer->question_id = $question_id;
            $answer->answer =  $array_data[3];
            $answer->sl_no = 'C';
            $answer->correct_ans = $tr[2];
            $answer->save();
            $answer = new Question_ans;
            $answer->question_id = $question_id;
            $answer->answer =  $array_data[4];
            $answer->sl_no = 'D';
            $answer->correct_ans = $tr[3];
            $answer->save();
            $answer = new Question_ans;
            $answer->question_id = $question_id;
            $answer->answer =  $array_data[5];
            $answer->sl_no = 'E';
            $answer->correct_ans = $tr[4];
            $answer->save();
        }
        if (QuestionReference::where('question_id', $question->id)->first()) {
            QuestionReference::where('question_id', $question->id)->delete();
        }

        if ($request->reference_id) {
            foreach ($request->reference_id as $key => $value) {
                if ($value == '') continue;
                QuestionReference::insert(['question_id' => $question->id, 'reference_id' => $value]);
            }
        }

        QuestionVideoLink::where('question_id', $question->id)->delete();

        //  dd ($request->videos);

        if (is_array($request->videos)) {
            foreach ($request->videos as $video) {
                // dd($video);
                if ($video == '') {
                    continue;
                }
                QuestionVideoLink::onlyTrashed()->updateOrCreate(
                    ['question_id' => $question->id],
                    ['video_link' => $video['link'], 'video_password' => $video['password'], 'deleted_at' => null]
                );
            }
        }

        //reference book update

        $this->saveReferenceBook($request, $question);
        
        // Save Question Label
        $this->saveQuestionLabel($request, $question);

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
        // $user=Question::find(Auth::id());

        // if(!$user->hasRole('Admin')){
        //     return abort(404);
        // }

        $question = Question::find($id);
        $question->deleted_by = Auth::id();
        $question->push();

        Question::destroy($id); // 1 way
        Qusetion_ans::where(['question_id' => $id])->delete();
        Exam_question::where(['question_id' => $id])->delete();
        QuestionReference::where(['question_id' => $id])->delete();
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\QuestionsController@index');
    }

}
