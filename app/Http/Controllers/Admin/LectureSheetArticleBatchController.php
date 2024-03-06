<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\OnlineLectureAddress;
use App\LectureSheetArticleBatch;
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
use App\LectureSheetArticleBatchLectureSheetArticle;
use App\LectureSheetArticle;
use App\Topics;
use App\LectureSheetArticleTopics;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;


class LectureSheetArticleBatchController extends Controller
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
        $data['lecture_sheet_article_batches'] = LectureSheetArticleBatch::get();
        //echo '<pre>';print_r($data['lecture_sheet_article_batchs']);exit;
        $data['module_name'] = 'Lecture Sheet Article Link';
        $data['title'] = 'Lecture Sheet Article Link List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_sheet_article_batch.list',$data);
                
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

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::get()->pluck('name', 'id');

        $data['institutes'] = Institutes::get()->pluck('name', 'id');
        $data['branches'] = Branch::pluck('name','id');

        $data['lecture_addresses'] = OnlineLectureAddress::get()->pluck('name', 'id');

        $data['module_name'] = 'Lecture Sheet Article Link';
        $data['title'] = 'Lecture Sheet Article Link Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_sheet_article_batch.create',$data);
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
            'topic_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\LectureSheetArticleBatchController@create')->withInput();
        }

        if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
        {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\LectureSheetArticleBatchController@create')->withInput();

        }

        if (LectureSheetArticleBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\LectureSheetArticleBatchController@create')->withInput();
        }
        else{

            $lecture_sheet_article_batch = new LectureSheetArticleBatch();
            //$lecture_sheet_article_batch->topic_id = $request->topic_id;            
            $lecture_sheet_article_batch->year = $request->year;
            $lecture_sheet_article_batch->session_id = $request->session_id;
            $lecture_sheet_article_batch->branch_id=$request->branch_id;
            $lecture_sheet_article_batch->institute_id=$request->institute_id;
            $lecture_sheet_article_batch->course_id=$request->course_id;
            $lecture_sheet_article_batch->faculty_id=$request->faculty_id;
            $lecture_sheet_article_batch->subject_id=$request->subject_id;
            $lecture_sheet_article_batch->batch_id=$request->batch_id;
            $lecture_sheet_article_batch->status=$request->status;
            $lecture_sheet_article_batch->created_by=Auth::id();
            $lecture_sheet_article_batch->save();

            $topic_ids = $request->topic_id;

            if (is_array($topic_ids)) {
                foreach ($topic_ids as $key => $value) {
                        
                        if($value == '')continue;
                    
                        unset($lecture_sheet_article_topics);
                        $lecture_sheet_article_topics = new LectureSheetArticleTopics();
                        $lecture_sheet_article_topics->lecture_sheet_article_batch_id = $lecture_sheet_article_batch->id;
                        $lecture_sheet_article_topics->topic_id = $value;
                        //$lecture_sheet_article_topics->status = $request->status;
                        //$lecture_sheet_article_topics->created_by = Auth::id();
                        $lecture_sheet_article_topics->save();
                        
                        $lecture_sheet_articles = LectureSheetArticle::where('topic_id',$value)->get();

                        foreach($lecture_sheet_articles as $lecture_sheet_article)
                        {
                            unset($lecture_sheet_article_batch_post);
                            $lecture_sheet_article_batch_post = new LectureSheetArticleBatchLectureSheetArticle();
                            $lecture_sheet_article_batch_post->lecture_sheet_article_batch_id = $lecture_sheet_article_batch->id;
                            $lecture_sheet_article_batch_post->lecture_sheet_article_id = $lecture_sheet_article->id;
                            //$lecture_sheet_article_batch_post->status = $request->status;
                            //$lecture_sheet_article_batch_post->created_by = Auth::id();
                            $lecture_sheet_article_batch_post->save();                            

                        }
                        
                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureSheetArticleBatchController@index');
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
        $lecture_sheet_article_batch=LectureSheetArticleBatch::select('lecture_sheet_article_batchs.*')->find($id);
        return view('admin.lecture_sheet_article_batch.show',['lecture_sheet_article_batch'=>$lecture_sheet_article_batch]);
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
        $lecture_sheet_article_batch = LectureSheetArticleBatch::find($id);
        $data['lecture_sheet_article_batch'] = LectureSheetArticleBatch::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        $data['lecture_addresses'] = OnlineLectureAddress::get()->pluck('name', 'id');
        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$lecture_sheet_article_batch->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::get()->where('institute_id',$lecture_sheet_article_batch->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$lecture_sheet_article_batch->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$lecture_sheet_article_batch->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$lecture_sheet_article_batch->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::get()->where('institute_id',$lecture_sheet_article_batch->institute_id)
            ->where('course_id',$lecture_sheet_article_batch->course_id)
            ->where('branch_id',$lecture_sheet_article_batch->branch_id)
            ->pluck('name', 'id');
        
        $data['topics'] = Topics::get()->where('course_id',$lecture_sheet_article_batch->course_id)->pluck('name', 'id');
        $selected_topics = array();
        foreach($lecture_sheet_article_batch->topics as $topics)
        {
            $selected_topics[] = $topics->topic->id;
        }

        $data['selected_topics'] = collect($selected_topics);
                
        $data['module_name'] = 'Lecture Sheet Article Link';
        $data['title'] = 'Lecture Sheet Article Link Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.lecture_sheet_article_batch.edit', $data);
        
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
            'topic_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $lecture_sheet_article_batch = LectureSheetArticleBatch::find($id);

        if($lecture_sheet_article_batch->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\LectureSheetArticleBatchController@edit',[$id])->withInput();

            }

        }

        if($lecture_sheet_article_batch->year != $request->year || $lecture_sheet_article_batch->session_id != $request->session_id || $lecture_sheet_article_batch->branch_id != $request->branch_id || $lecture_sheet_article_batch->institute_id != $request->institute_id || $lecture_sheet_article_batch->course_id != $request->course_id || $lecture_sheet_article_batch->batch_id != $request->batch_id) {

            if (LectureSheetArticleBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\LectureSheetArticleBatchController@edit',[$id])->withInput();
            }

        }

        //$lecture_sheet_article_batch->topic_id = $request->topic_id;
        $lecture_sheet_article_batch->year = $request->year;
        $lecture_sheet_article_batch->session_id = $request->session_id;
        $lecture_sheet_article_batch->branch_id=$request->branch_id;
        $lecture_sheet_article_batch->institute_id=$request->institute_id;
        $lecture_sheet_article_batch->course_id=$request->course_id;
        $lecture_sheet_article_batch->faculty_id=$request->faculty_id;
        $lecture_sheet_article_batch->subject_id=$request->subject_id;
        $lecture_sheet_article_batch->batch_id=$request->batch_id;
        $lecture_sheet_article_batch->status=$request->status;
        $lecture_sheet_article_batch->updated_by=Auth::id();
        $lecture_sheet_article_batch->push();

        $topic_ids = $request->topic_id;

        if (is_array($topic_ids)) {

            if(LectureSheetArticleTopics::where('lecture_sheet_article_batch_id',$lecture_sheet_article_batch->id)->first())
            {
                LectureSheetArticleTopics::where('lecture_sheet_article_batch_id',$lecture_sheet_article_batch->id)->delete();       
            }

            if(LectureSheetArticleBatchLectureSheetArticle::where('lecture_sheet_article_batch_id',$lecture_sheet_article_batch->id)->first())
            {
                LectureSheetArticleBatchLectureSheetArticle::where('lecture_sheet_article_batch_id',$lecture_sheet_article_batch->id)->delete();       
            }

            foreach ($topic_ids as $key => $value) {
                    
                    if($value == '')continue;
                
                    unset($lecture_sheet_article_topics);
                    $lecture_sheet_article_topics = new LectureSheetArticleTopics();
                    $lecture_sheet_article_topics->lecture_sheet_article_batch_id = $lecture_sheet_article_batch->id;
                    $lecture_sheet_article_topics->topic_id = $value;
                    //$lecture_sheet_article_topics->status = $request->status;
                    //$lecture_sheet_article_topics->created_by = Auth::id();
                    $lecture_sheet_article_topics->save();
                    
                    $lecture_sheet_articles = LectureSheetArticle::where('topic_id',$value)->get();

                    foreach($lecture_sheet_articles as $lecture_sheet_article)
                    {
                        unset($lecture_sheet_article_batch_post);
                        $lecture_sheet_article_batch_post = new LectureSheetArticleBatchLectureSheetArticle();
                        $lecture_sheet_article_batch_post->lecture_sheet_article_batch_id = $lecture_sheet_article_batch->id;
                        $lecture_sheet_article_batch_post->lecture_sheet_article_id = $lecture_sheet_article->id;
                        //$lecture_sheet_article_batch_post->status = $request->status;
                        //$lecture_sheet_article_batch_post->created_by = Auth::id();
                        $lecture_sheet_article_batch_post->save();                            

                    }
                    
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
        LectureSheetArticleBatch::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureSheetArticleBatchController@index');
    }
}