<?php
namespace App\Http\Controllers\Admin;
use App\NoticeYear;
use App\NoticeYearNotice;
use App\Http\Controllers\Controller;
use App\Notice;
use App\Sessions;
use Illuminate\Http\Request;
use App\Notice_question;
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


class NoticeYearController extends Controller
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
        $data['notice_years'] = NoticeYear::get();
        $data['module_name'] = 'Notice Year';
        $data['title'] = 'Notice Year List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.notice_year.list',$data);

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

        $data['notices'] = Notice::where(['type'=>"A"])->pluck('title', 'id');



        $data['module_name'] = 'Notice Year';
        $data['title'] = 'Notice Year Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.notice_year.create',$data);
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
            'notice_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\NoticeYearController@create')->withInput();
        }

        if (NoticeYear::where(['year'=>$request->year])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This link already exists for the year');
            return redirect()->action('Admin\NoticeYearController@create')->withInput();
        }
        else{

            $notice_year = new NoticeYear();

            $notice_year->year = $request->year;

            $notice_year->status=$request->status;
            $notice_year->created_by=Auth::id();
            $notice_year->save();


            $notice_ids = $request->notice_id;

            if (is_array($notice_ids)) {
                foreach ($notice_ids as $key => $value) {

                    if($value == '')continue;

                    unset($notice_year_notice);
                    $notice_year_notice = new NoticeYearNotice();
                    $notice_year_notice->notice_year_id = $notice_year->id;
                    $notice_year_notice->notice_id = $value;
                    //$notice_year_notice->status = $request->status;
                    //$notice_year_notice->created_by = Auth::id();
                    $notice_year_notice->save();

                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\NoticeYearController@index');
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
        $notice_year=NoticeYear::select('notice_years.*')->find($id);
        return view('admin.notice_year.show',['notice_year'=>$notice_year]);
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
        $notice_year = NoticeYear::find($id);
        $data['notice_year'] = NoticeYear::find($id);

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $selected_notices = array();
        foreach($notice_year->notices as $notice)
        {
            $selected_notices[] = $notice->notice_id;
        }


        $data['selected_notices'] = collect($selected_notices);


        $data['notices'] = Notice::where(['type'=>"A"])->pluck('title', 'id');


        $data['module_name'] = 'Notice Year';
        $data['title'] = 'Notice Year Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.notice_year.edit', $data);

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
            'notice_id' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $notice_year = NoticeYear::find($id);

        if($notice_year->year != $request->year) {

            if (NoticeYear::where(['year'=>$request->year])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Notice Year already exists for the year');
                return redirect()->action('Admin\NoticeYearController@edit',[$id])->withInput();
            }

        }

        $notice_year->year = $request->year;
        
        $notice_year->status=$request->status;
        $notice_year->updated_by=Auth::id();
        $notice_year->push();


        $notice_ids = $request->notice_id;

        if(NoticeYearNotice::where('notice_year_id',$notice_year->id)->first())
        {
            NoticeYearNotice::where('notice_year_id',$notice_year->id)->delete();
        }

        if (is_array($notice_ids)) {

            foreach ($notice_ids as $key => $value) {

                if($value == '')continue;

                unset($notice_year_notice);
                $notice_year_notice = new NoticeYearNotice();
                $notice_year_notice->notice_year_id = $notice_year->id;
                $notice_year_notice->notice_id = $value;
                //$notice_year_notice->status = $request->status;
                //$notice_year_notice->created_by = Auth::id();
                $notice_year_notice->save();

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
        NoticeYear::destroy($id); // 1 way
        NoticeYearNotice::where('notice_year_id',$id)->delete(); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\NoticeYearController@index');
    }
}