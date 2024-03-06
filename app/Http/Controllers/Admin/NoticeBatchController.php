<?php
namespace App\Http\Controllers\Admin;
use App\NoticeBatch;
use App\NoticeBatchNotice;
use App\Http\Controllers\Controller;
use App\Notice;
use App\Sessions;
use Illuminate\Http\Request;
use App\Notice_question;
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


class NoticeBatchController extends Controller
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
        $data['notice_batches'] = NoticeBatch::get();
        $data['module_name'] = 'Notice Batch';
        $data['title'] = 'Notice Batch List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.notice_batch.list',$data);

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

        $data['notices'] = Notice::pluck('title', 'id');



        $data['module_name'] = 'Notice Batch';
        $data['title'] = 'Notice Batch Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.notice_batch.create',$data);
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
            'notice_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\NoticeBatchController@create')->withInput();
        }

        if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
        {
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Batch does not exist in the selected Branch !!!');
            return redirect()->action('Admin\NoticeBatchController@create')->withInput();

        }

        if (NoticeBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\NoticeBatchController@create')->withInput();
        }
        else{

            $notice_batch = new NoticeBatch();

            $notice_batch->year = $request->year;
            $notice_batch->session_id = $request->session_id;
            $notice_batch->branch_id=$request->branch_id;
            $notice_batch->institute_id=$request->institute_id;
            $notice_batch->course_id=$request->course_id;
            $notice_batch->faculty_id=$request->faculty_id;
            $notice_batch->subject_id=$request->subject_id;
            $notice_batch->batch_id=$request->batch_id;
            $notice_batch->status=$request->status;
            $notice_batch->created_by=Auth::id();
            $notice_batch->save();


            $notice_ids = $request->notice_id;

            if (is_array($notice_ids)) {
                foreach ($notice_ids as $key => $value) {

                    if($value == '')continue;

                    unset($notice_batch_notice);
                    $notice_batch_notice = new NoticeBatchNotice();
                    $notice_batch_notice->notice_batch_id = $notice_batch->id;
                    $notice_batch_notice->notice_id = $value;
                    //$notice_batch_notice->status = $request->status;
                    //$notice_batch_notice->created_by = Auth::id();
                    $notice_batch_notice->save();

                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\NoticeBatchController@index');
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
        $notice_batch=NoticeBatch::select('notice_batchs.*')->find($id);
        return view('admin.notice_batch.show',['notice_batch'=>$notice_batch]);
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
        $notice_batch = NoticeBatch::find($id);
        $data['notice_batch'] = NoticeBatch::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['notices'] = Notice::pluck('title', 'id');

        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$notice_batch->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$notice_batch->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$notice_batch->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$notice_batch->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$notice_batch->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::where('institute_id',$notice_batch->institute_id)
            ->where('course_id',$notice_batch->course_id)
            ->where('branch_id',$notice_batch->branch_id)
            ->pluck('name', 'id');


        $data['notices'] = Notice::pluck('title', 'id');

        $selected_notices = array();
        foreach($notice_batch->notices as $notice)
        {
            $selected_notices[] = $notice->notice_id;
        }


        $data['selected_notices'] = collect($selected_notices);


        $data['notices'] = Notice::pluck('title', 'id');


        $data['module_name'] = 'Notice Batch';
        $data['title'] = 'Notice Batch Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.notice_batch.edit', $data);

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
            'notice_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $notice_batch = NoticeBatch::find($id);

        if($notice_batch->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\NoticeBatchController@edit',[$id])->withInput();

            }

        }

        if($notice_batch->year != $request->year || $notice_batch->session_id != $request->session_id || $notice_batch->branch_id != $request->branch_id || $notice_batch->institute_id != $request->institute_id || $notice_batch->course_id != $request->course_id || $notice_batch->batch_id != $request->batch_id) {

            if (NoticeBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\NoticeBatchController@edit',[$id])->withInput();
            }

        }

        $notice_batch->year = $request->year;
        $notice_batch->session_id = $request->session_id;
        $notice_batch->branch_id=$request->branch_id;
        $notice_batch->institute_id=$request->institute_id;
        $notice_batch->course_id=$request->course_id;
        $notice_batch->faculty_id=$request->faculty_id;
        $notice_batch->subject_id=$request->subject_id;
        $notice_batch->batch_id=$request->batch_id;
        $notice_batch->status=$request->status;
        $notice_batch->updated_by=Auth::id();
        $notice_batch->push();


        $notice_ids = $request->notice_id;

        if(NoticeBatchNotice::where('notice_batch_id',$notice_batch->id)->first())
        {
            NoticeBatchNotice::where('notice_batch_id',$notice_batch->id)->delete();
        }

        if (is_array($notice_ids)) {

            foreach ($notice_ids as $key => $value) {

                if($value == '')continue;

                unset($notice_batch_notice);
                $notice_batch_notice = new NoticeBatchNotice();
                $notice_batch_notice->notice_batch_id = $notice_batch->id;
                $notice_batch_notice->notice_id = $value;
                //$notice_batch_notice->status = $request->status;
                //$notice_batch_notice->created_by = Auth::id();
                $notice_batch_notice->save();

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
        NoticeBatch::destroy($id); // 1 way
        NoticeBatchNotice::where('notice_batch_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\NoticeBatchController@index');
    }
}