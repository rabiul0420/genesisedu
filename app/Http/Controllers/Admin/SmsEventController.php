<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\DoctorsCourses;
use App\SmsEventAssign;
use App\SmsEventLink;
use App\SmsEvent;
use App\SmsEventDiscipline;
use App\SmsEventFaculty;
use App\SmsEventBatchSmsEvent;
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
use Illuminate\Support\Facades\Response;


class SmsEventController extends Controller
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
        $data['sms_events'] = SmsEvent::get();
        $data['module_name'] = 'Sms Event';
        $data['title'] = 'Sms Event List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.sms_event.list',$data);
                
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

        $data['module_name'] = 'Sms Event';
        $data['title'] = 'Sms Event Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.sms_event.create',$data);
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
            'name' => ['required'],
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\SmsEventController@create')->withInput();
        }        

        if (SmsEvent::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This SmsEvent name already exists');
            return redirect()->action('Admin\SmsEventController@create')->withInput();
        }
        else{

            $sms_event = new SmsEvent();
            $sms_event->name = $request->name;
            $sms_event->details = $request->details;
            $sms_event->status = $request->status;
            $sms_event->created_by=Auth::id();
            $sms_event->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\SmsEventController@index');
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
        $sms_event=SmsEvent::select('sms_events.*')->find($id);
        return view('admin.sms_event.show',['sms_event'=>$sms_event]);
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
        $sms_event = SmsEvent::find($id);
        $data['sms_event'] = SmsEvent::find($id);

        $data['module_name'] = 'Sms Event';
        $data['title'] = 'Sms Event Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.sms_event.edit', $data);
        
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
        //echo '<pre>';print_r($request->subject_id);exit;
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $sms_event = SmsEvent::find($id);

        if($sms_event->name != $request->name) {

            if (SmsEvent::where(['name'=>$request->name])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This SmsEvent name already exists');
                return redirect()->action('Admin\SmsEventController@edit',[$id])->withInput();
            }

        }

        
        $sms_event->name = $request->name;
        $sms_event->details = $request->details;
        $sms_event->status = $request->status;
        $sms_event->updated_by=Auth::id();
        $sms_event->push();

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
        SmsEvent::destroy($id); // 1 way
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SmsEventController@index');
    }

    
}  