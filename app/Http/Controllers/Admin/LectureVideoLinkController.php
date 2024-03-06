<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\LectureVideo;
use App\LectureVideoLink;
use App\LectureVideoBatchLectureVideo;
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


class LectureVideoLinkController extends Controller
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
        $data['lecture_video_links'] = LectureVideoLink::get();
        $data['module_name'] = 'Lecture Video Link';
        $data['title'] = 'Lecture Video Link List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_video_link.list',$data);

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

        $data['lecture_videos'] = LectureVideo::pluck('name', 'id');

        $data['module_name'] = 'Lecture Video Link';
        $data['title'] = 'Lecture Video Link Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_video_link.create',$data);
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
            return redirect()->action('Admin\LectureVideoLinkController@create')->withInput();
        }

        if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
        {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\LectureVideoLinkController@create')->withInput();

        }

        if (LectureVideoLink::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\LectureVideoLinkController@create')->withInput();
        }
        else{

            $lecture_video_link = new LectureVideoLink();
                        
            $lecture_video_link->year = $request->year;
            $lecture_video_link->session_id = $request->session_id;
            $lecture_video_link->branch_id=$request->branch_id;
            $lecture_video_link->institute_id=$request->institute_id;
            $lecture_video_link->course_id=$request->course_id;
            $lecture_video_link->faculty_id=$request->faculty_id;
            $lecture_video_link->subject_id=$request->subject_id;
            $lecture_video_link->batch_id=$request->batch_id;
            $lecture_video_link->status=$request->status;
            $lecture_video_link->created_by=Auth::id();
            $lecture_video_link->save();

            
            $lecture_video_ids = $request->lecture_video_id;

            if (is_array($lecture_video_ids)) {
                foreach ($lecture_video_ids as $key => $value) {
                        
                        if($value == '')continue;
                    
                        unset($lecture_video_batch_lecture_video);
                        $lecture_video_batch_lecture_video = new LectureVideoBatchLectureVideo();
                        $lecture_video_batch_lecture_video->lecture_video_batch_id = $lecture_video_link->id;
                        $lecture_video_batch_lecture_video->lecture_video_id = $value;
                        //$lecture_video_batch_lecture_video->status = $request->status;
                        //$lecture_video_batch_lecture_video->created_by = Auth::id();
                        $lecture_video_batch_lecture_video->save();
                        
                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureVideoLinkController@index');
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
        $lecture_video_link=LectureVideoLink::select('lecture_video_links.*')->find($id);
        return view('admin.lecture_video_link.show',['lecture_video_link'=>$lecture_video_link]);
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
        $lecture_video_link = LectureVideoLink::find($id);
        $data['lecture_video_link'] = LectureVideoLink::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['lecture_videos'] = LectureVideo::pluck('name', 'id');

        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$lecture_video_link->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$lecture_video_link->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$lecture_video_link->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$lecture_video_link->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$lecture_video_link->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::where('institute_id',$lecture_video_link->institute_id)
            ->where('course_id',$lecture_video_link->course_id)
            ->where('branch_id',$lecture_video_link->branch_id)
            ->pluck('name', 'id');
        
        
        $data['lecture_videos'] = LectureVideo::where(['institute_id'=>$lecture_video_link->institute_id,'course_id'=>$lecture_video_link->course_id])->pluck('name', 'id');
        $selected_videos = array();
        foreach($lecture_video_link->lecture_videos as $lecture_video)
        {
            $selected_videos[] = $lecture_video->lecture_video_id;
        }        

        $data['selected_videos'] = collect($selected_videos);
                
        $data['module_name'] = 'Lecture Video Link';
        $data['title'] = 'Lecture Video Link Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.lecture_video_link.edit', $data);
        
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

        $lecture_video_link = LectureVideoLink::find($id);

        if($lecture_video_link->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\LectureVideoLinkController@edit',[$id])->withInput();

            }

        }

        if($lecture_video_link->year != $request->year || $lecture_video_link->session_id != $request->session_id || $lecture_video_link->branch_id != $request->branch_id || $lecture_video_link->institute_id != $request->institute_id || $lecture_video_link->course_id != $request->course_id || $lecture_video_link->batch_id != $request->batch_id) {

            if (LectureVideoLink::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\LectureVideoLinkController@edit',[$id])->withInput();
            }

        }

        $lecture_video_link->year = $request->year;
        $lecture_video_link->session_id = $request->session_id;
        $lecture_video_link->branch_id=$request->branch_id;
        $lecture_video_link->institute_id=$request->institute_id;
        $lecture_video_link->course_id=$request->course_id;
        $lecture_video_link->faculty_id=$request->faculty_id;
        $lecture_video_link->subject_id=$request->subject_id;
        $lecture_video_link->batch_id=$request->batch_id;
        $lecture_video_link->status=$request->status;
        $lecture_video_link->updated_by=Auth::id();
        $lecture_video_link->push();


        $lecture_video_ids = $request->lecture_video_id;

        if (is_array($lecture_video_ids)) {

            if(LectureVideoBatchLectureVideo::where('lecture_video_batch_id',$lecture_video_link->id)->first())
            {
                LectureVideoBatchLectureVideo::where('lecture_video_batch_id',$lecture_video_link->id)->delete();       
            }
            foreach ($lecture_video_ids as $key => $value) {
                    
                    if($value == '')continue;
                
                    unset($lecture_video_batch_lecture_video);
                    $lecture_video_batch_lecture_video = new LectureVideoBatchLectureVideo();
                    $lecture_video_batch_lecture_video->lecture_video_batch_id = $lecture_video_link->id;
                    $lecture_video_batch_lecture_video->lecture_video_id = $value;
                    //$lecture_video_batch_lecture_video->status = $request->status;
                    //$lecture_video_batch_lecture_video->created_by = Auth::id();
                    $lecture_video_batch_lecture_video->save();
                    
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
        LectureVideoLink::destroy($id); // 1 way
        LectureVideoBatchLectureVideo::where('lecture_video_batch_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureVideoLinkController@index');
    }
}