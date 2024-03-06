<?php
namespace App\Http\Controllers\Admin;
use App\SmsYear;
use App\SmsYearSms;
use App\Http\Controllers\Controller;
use App\Sms;
use App\Sessions;
use Illuminate\Http\Request;
use App\Sms_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Coursees;
use App\Branch;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;


class SmsYearController extends Controller
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
        $data['sms_years'] = SmsYear::get();
        $data['module_name'] = 'Sms Year';
        $data['title'] = 'Sms Year List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.sms_year.list',$data);

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

        $data['smss'] = Sms::where(['type'=>"A"])->pluck('title', 'id');



        $data['module_name'] = 'Sms Year';
        $data['title'] = 'Sms Year Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.sms_year.create',$data);
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
            'sms_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\SmsYearController@create')->withInput();
        }

        if (SmsYear::where(['year'=>$request->year])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the year');
            return redirect()->action('Admin\SmsYearController@create')->withInput();
        }
        else{

            $sms_year = new SmsYear();

            $sms_year->year = $request->year;

            $sms_year->status=$request->status;
            $sms_year->created_by=Auth::id();
            $sms_year->save();


            $sms_ids = $request->sms_id;

            if (is_array($sms_ids)) {
                foreach ($sms_ids as $key => $value) {

                    if($value == '')continue;

                    unset($sms_year_sms);
                    $sms_year_sms = new SmsYearSms();
                    $sms_year_sms->sms_year_id = $sms_year->id;
                    $sms_year_sms->sms_id = $value;
                    //$sms_year_sms->status = $request->status;
                    //$sms_year_sms->created_by = Auth::id();
                    $sms_year_sms->save();

                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\SmsYearController@index');
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
        $sms_year=SmsYear::select('sms_years.*')->find($id);
        return view('admin.sms_year.show',['sms_year'=>$sms_year]);
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
        $sms_year = SmsYear::find($id);
        $data['sms_year'] = SmsYear::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $selected_smss = array();
        foreach($sms_year->smss as $sms)
        {
            $selected_smss[] = $sms->sms_id;
        }


        $data['selected_smss'] = collect($selected_smss);


        $data['smss'] = Sms::where(['type'=>"A"])->pluck('title', 'id');


        $data['module_name'] = 'Sms Year';
        $data['title'] = 'Sms Year Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.sms_year.edit', $data);

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
            'sms_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $sms_year = SmsYear::find($id);

        if($sms_year->year != $request->year) {

            if (SmsYear::where(['year'=>$request->year])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Sms Year already exists for the year');
                return redirect()->action('Admin\SmsYearController@edit',[$id])->withInput();
            }

        }

        $sms_year->year = $request->year;
        
        $sms_year->status=$request->status;
        $sms_year->updated_by=Auth::id();
        $sms_year->push();


        $sms_ids = $request->sms_id;

        if(SmsYearSms::where('sms_year_id',$sms_year->id)->first())
        {
            SmsYearSms::where('sms_year_id',$sms_year->id)->delete();
        }

        if (is_array($sms_ids)) {

            foreach ($sms_ids as $key => $value) {

                if($value == '')continue;

                unset($sms_year_sms);
                $sms_year_sms = new SmsYearSms();
                $sms_year_sms->sms_year_id = $sms_year->id;
                $sms_year_sms->sms_id = $value;
                //$sms_year_sms->status = $request->status;
                //$sms_year_sms->created_by = Auth::id();
                $sms_year_sms->save();

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
        SmsYear::destroy($id); // 1 way
        SmsYearSms::where('sms_year_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SmsYearController@index');
    }
}