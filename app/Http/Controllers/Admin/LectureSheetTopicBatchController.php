<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\LectureSheetTopic;
use App\LectureSheetTopicBatch;
use App\LectureSheetTopicBatchLectureSheetTopic;
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
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;


class LectureSheetTopicBatchController extends Controller
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
        $data['lecture_sheet_topic_batches'] = LectureSheetTopicBatch::get();
        $data['module_name'] = 'Lecture Sheet Folder Batch';
        $data['title'] = 'Lecture Sheet Folder Batch List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_sheet_topic_batch.list',$data);

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

        $data['lecture_sheet_topics'] = LectureSheetTopic::pluck('name', 'id');

        $data['module_name'] = 'Lecture Sheet Folder Batch';
        $data['title'] = 'Lecture Sheet Folder Batch Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_sheet_topic_batch.create',$data);
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
            'lecture_sheet_topic_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\LectureSheetTopicBatchController@create')->withInput();
        }

        if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
        {
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Batch does not exist in the selected Branch !!!');
            return redirect()->action('Admin\LectureSheetTopicBatchController@create')->withInput();

        }

        if (LectureSheetTopicBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\LectureSheetTopicBatchController@create')->withInput();
        }
        else{

            $lecture_sheet_topic_batch = new LectureSheetTopicBatch();

            $lecture_sheet_topic_batch->year = $request->year;
            $lecture_sheet_topic_batch->session_id = $request->session_id;
            $lecture_sheet_topic_batch->branch_id=$request->branch_id;
            $lecture_sheet_topic_batch->institute_id=$request->institute_id;
            $lecture_sheet_topic_batch->course_id=$request->course_id;
            $lecture_sheet_topic_batch->faculty_id=$request->faculty_id;
            $lecture_sheet_topic_batch->subject_id=$request->subject_id;
            $lecture_sheet_topic_batch->batch_id=$request->batch_id;
            $lecture_sheet_topic_batch->status=$request->status;
            $lecture_sheet_topic_batch->created_by=Auth::id();
            $lecture_sheet_topic_batch->save();


            $lecture_sheet_topic_ids = $request->lecture_sheet_topic_id;

            if (is_array($lecture_sheet_topic_ids)) {
                foreach ($lecture_sheet_topic_ids as $key => $value) {

                    if($value == '')continue;

                    unset($lecture_sheet_topic_batch_lecture_sheet_topic);
                    $lecture_sheet_topic_batch_lecture_sheet_topic = new LectureSheetTopicBatchLectureSheetTopic();
                    $lecture_sheet_topic_batch_lecture_sheet_topic->lecture_sheet_topic_batch_id = $lecture_sheet_topic_batch->id;
                    $lecture_sheet_topic_batch_lecture_sheet_topic->lecture_sheet_topic_id = $value;
                    //$lecture_sheet_topic_batch_lecture_sheet_topic->status = $request->status;
                    //$lecture_sheet_topic_batch_lecture_sheet_topic->created_by = Auth::id();
                    $lecture_sheet_topic_batch_lecture_sheet_topic->save();

                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureSheetTopicBatchController@index');
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
        $lecture_sheet_topic_batch=LectureSheetTopicBatch::select('lecture_sheet_topic_batchs.*')->find($id);
        return view('admin.lecture_sheet_topic_batch.show',['lecture_sheet_topic_batch'=>$lecture_sheet_topic_batch]);
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
        $lecture_sheet_topic_batch = LectureSheetTopicBatch::find($id);
        $data['lecture_sheet_topic_batch'] = LectureSheetTopicBatch::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['lecture_sheet_topics'] = LectureSheetTopic::pluck('name', 'id');

        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$lecture_sheet_topic_batch->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$lecture_sheet_topic_batch->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$lecture_sheet_topic_batch->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$lecture_sheet_topic_batch->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$lecture_sheet_topic_batch->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::where('institute_id',$lecture_sheet_topic_batch->institute_id)
            ->where('course_id',$lecture_sheet_topic_batch->course_id)
            ->where('branch_id',$lecture_sheet_topic_batch->branch_id)
            ->where('session_id',$lecture_sheet_topic_batch->session_id)
            ->where('year',$lecture_sheet_topic_batch->year)
            ->pluck('name', 'id');

        $selected_sheets = array();
        foreach($lecture_sheet_topic_batch->lecture_sheet_topics as $lecture_sheet_topic)
        {
            $selected_sheets[] = $lecture_sheet_topic->lecture_sheet_topic_id;
        }

        $data['selected_sheets'] = collect($selected_sheets);

        $data['lecture_sheet_topics'] = LectureSheetTopic::pluck('name', 'id');

        $data['module_name'] = 'Lecture Sheet Folder Batch';
        $data['title'] = 'Lecture Sheet Folder Batch Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        // return $data;
        return view('admin.lecture_sheet_topic_batch.edit', $data);

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
            'lecture_sheet_topic_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $lecture_sheet_topic_batch = LectureSheetTopicBatch::find($id);

        if($lecture_sheet_topic_batch->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\LectureSheetTopicBatchController@edit',[$id])->withInput();

            }

        }

        if($lecture_sheet_topic_batch->year != $request->year || $lecture_sheet_topic_batch->session_id != $request->session_id || $lecture_sheet_topic_batch->branch_id != $request->branch_id || $lecture_sheet_topic_batch->institute_id != $request->institute_id || $lecture_sheet_topic_batch->course_id != $request->course_id || $lecture_sheet_topic_batch->batch_id != $request->batch_id) {

            if (LectureSheetTopicBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\LectureSheetTopicBatchController@edit',[$id])->withInput();
            }

        }

        $lecture_sheet_topic_batch->year = $request->year;
        $lecture_sheet_topic_batch->session_id = $request->session_id;
        $lecture_sheet_topic_batch->branch_id=$request->branch_id;
        $lecture_sheet_topic_batch->institute_id=$request->institute_id;
        $lecture_sheet_topic_batch->course_id=$request->course_id;
        $lecture_sheet_topic_batch->faculty_id=$request->faculty_id;
        $lecture_sheet_topic_batch->subject_id=$request->subject_id;
        $lecture_sheet_topic_batch->batch_id=$request->batch_id;
        $lecture_sheet_topic_batch->status=$request->status;
        $lecture_sheet_topic_batch->updated_by=Auth::id();
        $lecture_sheet_topic_batch->push();


        $lecture_sheet_topic_ids = $request->lecture_sheet_topic_id;
        if(LectureSheetTopicBatchLectureSheetTopic::where('lecture_sheet_topic_batch_id',$lecture_sheet_topic_batch->id)->first())

        {
            LectureSheetTopicBatchLectureSheetTopic::where('lecture_sheet_topic_batch_id',$lecture_sheet_topic_batch->id)->delete();
        }

        if (is_array($lecture_sheet_topic_ids)) {


            foreach ($lecture_sheet_topic_ids as $key => $value) {

                if($value == '')continue;

                unset($lecture_sheet_topic_batch_lecture_sheet_topic);
                $lecture_sheet_topic_batch_lecture_sheet_topic = new LectureSheetTopicBatchLectureSheetTopic();
                $lecture_sheet_topic_batch_lecture_sheet_topic->lecture_sheet_topic_batch_id = $lecture_sheet_topic_batch->id;
                $lecture_sheet_topic_batch_lecture_sheet_topic->lecture_sheet_topic_id = $value;
                //$lecture_sheet_topic_batch_lecture_sheet_topic->status = $request->status;
                //$lecture_sheet_topic_batch_lecture_sheet_topic->created_by = Auth::id();
                $lecture_sheet_topic_batch_lecture_sheet_topic->save();

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
        $lecture_sheet_batch = LectureSheetTopicBatch::find($id);
        $lecture_sheet_batch->deleted_by=Auth::id();
        $lecture_sheet_batch->push();

        LectureSheetTopicBatch::destroy($id); // 1 way
        LectureSheetTopicBatchLectureSheetTopic::where('lecture_sheet_topic_batch_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureSheetTopicBatchController@index');
    }
}