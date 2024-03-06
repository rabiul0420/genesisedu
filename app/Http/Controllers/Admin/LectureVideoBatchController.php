<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\LectureVideo;
use App\LectureVideoBatch;
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
use Yajra\DataTables\Facades\DataTables;


class LectureVideoBatchController extends Controller
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
        $data['lecture_video_batches'] = LectureVideoBatch::get();
        $data['module_name'] = 'Lecture Video Batch';
        $data['title'] = 'Lecture Video Batch List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.lecture_video_batch.list',$data);

    }

    public function lecture_video_batch_list() {
        $lecture_video_batch_list = DB::table('lecture_video_batch as d1')
        ->leftjoin('sessions as d2', 'd1.session_id', '=','d2.id')
        ->leftjoin('branches as d3', 'd1.branch_id', '=','d3.id')
        ->leftjoin('institutes as d4', 'd1.institute_id', '=','d4.id')
        ->leftjoin('courses as d5', 'd1.course_id', '=','d5.id')
        ->leftjoin('batches as d6', 'd1.batch_id', '=','d6.id');

        $lecture_video_batch_list->select(
            'd1.id as id',
            'd1.year as year',
            'd2.name as session_name',
            'd3.name as branches_name',
            'd4.name as institute_name',
            'd5.name as course_name',
            'd6.name as batche_name',
            'd1.status as status',
        );

        $lecture_video_batch_list = $lecture_video_batch_list->whereNull('d1.deleted_at');
        
        return DataTables::of($lecture_video_batch_list)
            ->addColumn('action', function ($lecture_video_batch_list) {
                return view('admin.lecture_video_batch.lecture_video_batch_ajax_list',(['lecture_video_batch_list'=>$lecture_video_batch_list]));
            })
            
            ->addColumn('status',function($lecture_video_batch_list){
                return '<span style="color:' .( $lecture_video_batch_list->status == 1 ? 'green;':'red;' ).'">'
                        . ($lecture_video_batch_list->status == 1 ? 'Active':'Inactive') . '</span>';
            })
            ->rawColumns(['action','status',])

        ->make(true);
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

        $data['module_name'] = 'Lecture Video Batch';
        $data['title'] = 'Lecture Video Batch Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_video_batch.create',$data);
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
            return redirect()->action('Admin\LectureVideoBatchController@create')->withInput();
        }

        if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
        {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\LectureVideoBatchController@create')->withInput();

        }

        if (LectureVideoBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\LectureVideoBatchController@create')->withInput();
        }
        else{

            $lecture_video_batch = new LectureVideoBatch();
                        
            $lecture_video_batch->year = $request->year;
            $lecture_video_batch->session_id = $request->session_id;
            $lecture_video_batch->branch_id=$request->branch_id;
            $lecture_video_batch->institute_id=$request->institute_id;
            $lecture_video_batch->course_id=$request->course_id;
            $lecture_video_batch->faculty_id=$request->faculty_id;
            $lecture_video_batch->subject_id=$request->subject_id;
            $lecture_video_batch->batch_id=$request->batch_id;
            $lecture_video_batch->status=$request->status;
            $lecture_video_batch->created_by=Auth::id();
            $lecture_video_batch->save();

            
            $lecture_video_ids = $request->lecture_video_id;

            if (is_array($lecture_video_ids)) {
                foreach ($lecture_video_ids as $key => $value) {
                        
                        if($value == '')continue;
                    
                        unset($lecture_video_batch_lecture_video);
                        $lecture_video_batch_lecture_video = new LectureVideoBatchLectureVideo();
                        $lecture_video_batch_lecture_video->lecture_video_batch_id = $lecture_video_batch->id;
                        $lecture_video_batch_lecture_video->lecture_video_id = $value;
                        //$lecture_video_batch_lecture_video->status = $request->status;
                        //$lecture_video_batch_lecture_video->created_by = Auth::id();
                        $lecture_video_batch_lecture_video->save();
                        
                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureVideoBatchController@index');
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
        $lecture_video_batch=LectureVideoBatch::select('lecture_video_batchs.*')->find($id);
        return view('admin.lecture_video_batch.show',['lecture_video_batch'=>$lecture_video_batch]);
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
        $lecture_video_batch = LectureVideoBatch::find($id);
        $data['lecture_video_batch'] = LectureVideoBatch::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['lecture_videos'] = LectureVideo::pluck('name', 'id');

        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$lecture_video_batch->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$lecture_video_batch->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$lecture_video_batch->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$lecture_video_batch->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$lecture_video_batch->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::where('institute_id',$lecture_video_batch->institute_id)
            ->where('course_id',$lecture_video_batch->course_id)
            ->where('branch_id',$lecture_video_batch->branch_id)
            ->pluck('name', 'id');
        
        
        $data['lecture_videos'] = LectureVideo::where(['institute_id'=>$lecture_video_batch->institute_id,'course_id'=>$lecture_video_batch->course_id])->pluck('name', 'id');
        $selected_videos = array();
        foreach($lecture_video_batch->lecture_videos as $lecture_video)
        {
            $selected_videos[] = $lecture_video->lecture_video_id;
        }        

        $data['selected_videos'] = collect($selected_videos);

        $data['lecture_videos'] = LectureVideo::pluck('name', 'id');
                
        $data['module_name'] = 'Lecture Video Batch';
        $data['title'] = 'Lecture Video Batch Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.lecture_video_batch.edit', $data);
        
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

        $lecture_video_batch = LectureVideoBatch::find($id);
        if($lecture_video_batch->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\LectureVideoBatchController@edit',[$id])->withInput();

            }

        }

        if($lecture_video_batch->year != $request->year || $lecture_video_batch->session_id != $request->session_id || $lecture_video_batch->branch_id != $request->branch_id || $lecture_video_batch->institute_id != $request->institute_id || $lecture_video_batch->course_id != $request->course_id || $lecture_video_batch->batch_id != $request->batch_id) {

            if (LectureVideoBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\LectureVideoBatchController@edit',[$id])->withInput();
            }

        }

        $lecture_video_batch->year = $request->year;
        $lecture_video_batch->session_id = $request->session_id;
        $lecture_video_batch->branch_id=$request->branch_id;
        $lecture_video_batch->institute_id=$request->institute_id;
        $lecture_video_batch->course_id=$request->course_id;
        $lecture_video_batch->faculty_id=$request->faculty_id;
        $lecture_video_batch->subject_id=$request->subject_id;
        $lecture_video_batch->batch_id=$request->batch_id;
        $lecture_video_batch->status=$request->status;
        $lecture_video_batch->updated_by=Auth::id();
        $lecture_video_batch->push();

        $lecture_video_ids = $request->lecture_video_id;

        $deletion = LectureVideoBatchLectureVideo::where( 'lecture_video_batch_id' , $lecture_video_batch->id )->whereNotIn( 'lecture_video_id' , $request->lecture_video_id ?? [] );
        $deletion->update(['deleted_by' => Auth::id()]);
        $deletion->delete();

        if( is_array(  $lecture_video_ids ) ) {
            $allIds = LectureVideoBatchLectureVideo::whereIn( 'lecture_video_id' , $request->lecture_video_id )->where( 'lecture_video_batch_id' , $lecture_video_batch->id )->withTrashed( )->get( ['id', 'lecture_video_id'] );
            $lecture_video_ids = $allIds->pluck( 'lecture_video_id' )->toArray();



            if( $allIds->count() ){
                LectureVideoBatchLectureVideo::whereIn( 'id', $allIds->pluck('id') )->onlyTrashed()->update([ 'deleted_at' => NULL, 'deleted_by' => null ]);
            }

            $insertingData = [ ];

            foreach ( $request->lecture_video_id as $lecture_video_id ) {

                if( !in_array($lecture_video_id, $lecture_video_ids)) {
                    $insertingData[ ] = [
                        'lecture_video_id' => $lecture_video_id,
                        'lecture_video_batch_id' => $lecture_video_batch->id,
                    ];
                }

            }

            LectureVideoBatchLectureVideo::insert( $insertingData );
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
        LectureVideoBatch::destroy($id); // 1 way
        LectureVideoBatchLectureVideo::where('lecture_video_batch_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureVideoBatchController@index');
    }
}