<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\LectureSheetTopic;
use App\Sessions;
use Illuminate\Http\Request;

use App\Institutes;
use App\LectureSheet;
use App\LectureSheetTopicLectureSheet;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class LectureSheetTopicController extends Controller
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
        $data['lecture_sheet_topics'] = LectureSheetTopic::get();
        $data['module_name'] = 'Lecture Sheet Folder';
        $data['title'] = 'Lecture Sheet Folder List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_sheet_topic.list',$data);
                
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

        $data['module_name'] = 'Lecture Sheet Folder';
        $data['title'] = 'Lecture Sheet Folder Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_sheet_topic.create',$data);
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
            return redirect()->action('Admin\LectureSheetTopicController@create')->withInput();
        }

        if (LectureSheetTopic::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This lecture sheet already exists for the batch');
            return redirect()->action('Admin\LectureSheetTopicController@create')->withInput();
        }
        else{

            $lecture_sheet_topic = new LectureSheetTopic();
            $lecture_sheet_topic->name = $request->name;
            $lecture_sheet_topic->created_by = Auth::id();
            $lecture_sheet_topic->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureSheetTopicController@index');
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
        $lecture_sheet_topic=LectureSheetTopic::select('lecture_sheet_topics.*')->find($id);
        return view('admin.lecture_sheet_topic.show',['lecture_sheet_topic'=>$lecture_sheet_topic]);
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
        $lecture_sheet_topic = LectureSheetTopic::find($id);
        $data['lecture_sheet_topic'] = LectureSheetTopic::find($id);

        $data['module_name'] = 'Lecture Sheet Folder';
        $data['title'] = 'Lecture Sheet Folder Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.lecture_sheet_topic.edit', $data);
        
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
            'name' => ['required'],
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $lecture_sheet_topic = LectureSheetTopic::find($id);

        if($lecture_sheet_topic->name != $request->name) {

            if (LectureSheetTopic::where(['name'=>$request->name])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Sheet Folder already exists for the batch');
                return redirect()->action('Admin\LectureSheetTopicController@edit',[$id])->withInput();
            }

        }

        $lecture_sheet_topic->name = $request->name;
        $lecture_sheet_topic->updated_by=Auth::id();
        $lecture_sheet_topic->push();
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
        
        $lecture_sheet_topic_deletedby = LectureSheetTopic::find($id);
        $lecture_sheet_topic_deletedby->deleted_by=Auth::id();
        $lecture_sheet_topic_deletedby->push();

        LectureSheetTopic::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureSheetTopicController@index');
    }

    public function view_lecture_sheet($id)
    {
        $lecture_sheets = LectureSheetTopicLectureSheet::with('lecture_sheet')->where(['lecture_sheet_topic_id' => $id ])->get();
        return view('admin.lecture_sheet_topic.view' , compact('lecture_sheets'));
    }
}