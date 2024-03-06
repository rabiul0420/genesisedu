<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\OnlineLectureAddress;
use App\LectureSheetArticle;
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


class LectureSheetArticleController extends Controller
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
        $data['lecture_sheet_articles'] = LectureSheetArticle::get();
        
        $data['module_name'] = 'Lecture Sheet Article';
        $data['title'] = 'Lecture Sheet Article List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_sheet_article.list',$data);
                
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

        //$data['lecture_addresses'] = OnlineLectureAddress::get()->pluck('name', 'id');

        $data['module_name'] = 'Lecture Sheet Article';
        $data['title'] = 'Lecture Sheet Article Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_sheet_article.create',$data);
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
            'topic_id' => ['required'],
            'title' => ['required'],
            'description' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\LectureSheetArticleController@create')->withInput();
        }

        if (LectureSheetArticle::where(['title'=>$request->title,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'topic_id'=>$request->topic_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This lecture sheet already exists for the batch');
            return redirect()->action('Admin\LectureSheetArticleController@create')->withInput();
        }
        else{

            $lecture_sheet_article = new LectureSheetArticle();
            $lecture_sheet_article->title = $request->title;
            $lecture_sheet_article->description = $request->description;
            $lecture_sheet_article->institute_id=$request->institute_id;
            $lecture_sheet_article->course_id=$request->course_id;
            $lecture_sheet_article->topic_id=$request->topic_id;
            $lecture_sheet_article->status=$request->status;
            $lecture_sheet_article->created_by=Auth::id();
            $lecture_sheet_article->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureSheetArticleController@index');
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
        $lecture_sheet_article=LectureSheetArticle::select('lecture_sheet_articles.*')->find($id);
        return view('admin.lecture_sheet_article.show',['lecture_sheet_article'=>$lecture_sheet_article]);
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
        $lecture_sheet_article = LectureSheetArticle::find($id);
        $data['lecture_sheet_article'] = LectureSheetArticle::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        $data['lecture_addresses'] = OnlineLectureAddress::get()->pluck('name', 'id');

        $institute_type = Institutes::where('id',$lecture_sheet_article->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties':'courses-subjects';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::get()->where('institute_id',$lecture_sheet_article->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$lecture_sheet_article->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$lecture_sheet_article->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$lecture_sheet_article->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::get()->where('institute_id',$lecture_sheet_article->institute_id)
            ->where('course_id',$lecture_sheet_article->course_id)
            ->pluck('name', 'id');
        
        $data['topics'] = Topics::get()->where('course_id',$lecture_sheet_article->course_id)
            ->pluck('name', 'id');

        $data['module_name'] = 'Lecture Sheet Article';
        $data['title'] = 'Lecture Sheet Article Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.lecture_sheet_article.edit', $data);
        
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
            'topic_id' => ['required'],
            'title' => ['required'],
            'description' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $lecture_sheet_article = LectureSheetArticle::find($id);

        if($lecture_sheet_article->title != $request->title || $lecture_sheet_article->institute_id != $request->institute_id || $lecture_sheet_article->course_id != $request->course_id || $lecture_sheet_article->topic_id != $request->topic_id) {

            if (LectureSheetArticle::where(['title'=>$request->title,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'topic_id'=>$request->topic_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\LectureSheetArticleController@edit',[$id])->withInput();
            }

        }

        $lecture_sheet_article->title = $request->title;
        $lecture_sheet_article->description = $request->description;
        $lecture_sheet_article->institute_id=$request->institute_id;
        $lecture_sheet_article->course_id=$request->course_id;
        $lecture_sheet_article->topic_id=$request->topic_id;
        $lecture_sheet_article->status=$request->status;
        $lecture_sheet_article->updated_by=Auth::id();
        $lecture_sheet_article->push();
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
        LectureSheetArticle::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureSheetArticleController@index');
    }
}