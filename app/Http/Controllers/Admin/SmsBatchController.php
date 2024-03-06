<?php
namespace App\Http\Controllers\Admin;
use App\SmsBatch;
use App\SmsBatchSms;
use App\Http\Controllers\Controller;
use App\Sms;
use App\Sessions;
use Illuminate\Http\Request;
use App\Sms_question;
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


class SmsBatchController extends Controller
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
        $data['sms_batches'] = SmsBatch::get();
        $data['module_name'] = 'Sms Batch';
        $data['title'] = 'Sms Batch List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.sms_batch.list',$data);

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

        $data['smss'] = Sms::where(['type'=>"B"])->pluck('title', 'id');



        $data['module_name'] = 'Sms Batch';
        $data['title'] = 'Sms Batch Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.sms_batch.create',$data);
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
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\SmsBatchController@create')->withInput();
        }

        if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
        {
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Batch does not exist in the selected Branch !!!');
            return redirect()->action('Admin\SmsBatchController@create')->withInput();

        }

        if (SmsBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the batch');
            return redirect()->action('Admin\SmsBatchController@create')->withInput();
        }
        else{

            $sms_batch = new SmsBatch();

            $sms_batch->year = $request->year;
            $sms_batch->session_id = $request->session_id;
            $sms_batch->branch_id=$request->branch_id;
            $sms_batch->institute_id=$request->institute_id;
            $sms_batch->course_id=$request->course_id;
            $sms_batch->faculty_id=$request->faculty_id;
            $sms_batch->subject_id=$request->subject_id;
            $sms_batch->batch_id=$request->batch_id;
            $sms_batch->status=$request->status;
            $sms_batch->created_by=Auth::id();
            $sms_batch->save();


            $sms_ids = $request->sms_id;

            if (is_array($sms_ids)) {
                foreach ($sms_ids as $key => $value) {

                    if($value == '')continue;

                    unset($sms_batch_sms);
                    $sms_batch_sms = new SmsBatchSms();
                    $sms_batch_sms->sms_batch_id = $sms_batch->id;
                    $sms_batch_sms->sms_id = $value;
                    //$sms_batch_sms->status = $request->status;
                    //$sms_batch_sms->created_by = Auth::id();
                    $sms_batch_sms->save();

                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\SmsBatchController@index');
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
        $sms_batch=SmsBatch::select('sms_batchs.*')->find($id);
        return view('admin.sms_batch.show',['sms_batch'=>$sms_batch]);
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
        $sms_batch = SmsBatch::find($id);
        $data['sms_batch'] = SmsBatch::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['institutes'] = Institutes::pluck('name', 'id');

        $data['smss'] = Sms::pluck('title', 'id');

        $data['branches'] = Branch::pluck('name','id');
        $institute_type = Institutes::where('id',$sms_batch->institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $data['url']  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';
        $data['institute_type']= $institute_type;

        $data['courses'] = Courses::where('institute_id',$sms_batch->institute_id)->pluck('name', 'id');

        if($data['institute_type']==1){
            $data['faculties'] = Faculty::where('course_id',$sms_batch->course_id)->pluck('name', 'id');
            $data['subjects'] = Subjects::where('faculty_id',$sms_batch->faculty_id)->pluck('name', 'id');
        }else{
            $data['subjects'] = Subjects::where('course_id',$sms_batch->course_id)->pluck('name', 'id');
        }

        $data['batches'] = Batches::where('institute_id',$sms_batch->institute_id)
            ->where('course_id',$sms_batch->course_id)
            ->where('branch_id',$sms_batch->branch_id)
            ->pluck('name', 'id');


        $data['smss'] = Sms::pluck('title', 'id');

        $selected_smss = array();
        foreach($sms_batch->smss as $sms)
        {
            $selected_smss[] = $sms->sms_id;
        }


        $data['selected_smss'] = collect($selected_smss);


        $data['smss'] = Sms::where(['type'=>"B"])->pluck('title', 'id');


        $data['module_name'] = 'Sms Batch';
        $data['title'] = 'Sms Batch Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.sms_batch.edit', $data);

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
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $sms_batch = SmsBatch::find($id);

        if($sms_batch->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\SmsBatchController@edit',[$id])->withInput();

            }

        }

        if($sms_batch->year != $request->year || $sms_batch->session_id != $request->session_id || $sms_batch->branch_id != $request->branch_id || $sms_batch->institute_id != $request->institute_id || $sms_batch->course_id != $request->course_id || $sms_batch->batch_id != $request->batch_id) {

            if (SmsBatch::where(['year'=>$request->year,'session_id'=>$request->session_id,'branch_id'=>$request->branch_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Address already exists for the batch');
                return redirect()->action('Admin\SmsBatchController@edit',[$id])->withInput();
            }

        }

        $sms_batch->year = $request->year;
        $sms_batch->session_id = $request->session_id;
        $sms_batch->branch_id=$request->branch_id;
        $sms_batch->institute_id=$request->institute_id;
        $sms_batch->course_id=$request->course_id;
        $sms_batch->faculty_id=$request->faculty_id;
        $sms_batch->subject_id=$request->subject_id;
        $sms_batch->batch_id=$request->batch_id;
        $sms_batch->status=$request->status;
        $sms_batch->updated_by=Auth::id();
        $sms_batch->push();


        $sms_ids = $request->sms_id;

        if(SmsBatchSms::where('sms_batch_id',$sms_batch->id)->first())
        {
            SmsBatchSms::where('sms_batch_id',$sms_batch->id)->delete();
        }

        if (is_array($sms_ids)) {

            foreach ($sms_ids as $key => $value) {

                if($value == '')continue;

                unset($sms_batch_sms);
                $sms_batch_sms = new SmsBatchSms();
                $sms_batch_sms->sms_batch_id = $sms_batch->id;
                $sms_batch_sms->sms_id = $value;
                //$sms_batch_sms->status = $request->status;
                //$sms_batch_sms->created_by = Auth::id();
                $sms_batch_sms->save();

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
        SmsBatch::destroy($id); // 1 way
        SmsBatchSms::where('sms_batch_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SmsBatchController@index');
    }
}