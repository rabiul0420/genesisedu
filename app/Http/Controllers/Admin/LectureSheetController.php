<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\LectureSheet;
use App\LectureSheetTopic;
use App\LectureSheetTopicLectureSheet;
use App\Sessions;
use Illuminate\Http\Request;

use App\Institutes;

use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;


class LectureSheetController extends Controller
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
        $data['lecture_sheets'] = LectureSheet::get();
        $data['module_name'] = 'Lecture Sheet';
        $data['title'] = 'Lecture Sheet List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.lecture_sheet.list',$data);
                
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

        $data['lecture_sheet_topics'] = LectureSheetTopic::pluck('name', 'id');

        $data['module_name'] = 'Lecture Sheet';
        $data['title'] = 'Lecture Sheet Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.lecture_sheet.create',$data);
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
            return redirect()->action('Admin\LectureSheetController@create')->withInput();
        }

        if (LectureSheet::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This lecture sheet already exists for the batch');
            return redirect()->action('Admin\LectureSheetController@create')->withInput();
        }
        else{

            $lecture_sheet = new LectureSheet();
            $lecture_sheet->name = $request->name;
            $lecture_sheet->pdf_file = $request->pdf_file;
            $lecture_sheet->created_by = Auth::id();
            $lecture_sheet->save();

            if($request->lecture_sheet_topic_id)
            {
                if(LectureSheetTopicLectureSheet::where(['lecture_sheet_id'=>$lecture_sheet->id])->first())
                {
                    LectureSheetTopicLectureSheet::where(['lecture_sheet_id'=>$lecture_sheet->id])->Delete();
                }

                foreach ($request->lecture_sheet_topic_id as $lecture_sheet_topic)
                {
                    if($lecture_sheet_topic == '')continue;
                    LectureSheetTopicLectureSheet::insert(['lecture_sheet_id'=>$lecture_sheet->id,'lecture_sheet_topic_id'=>$lecture_sheet_topic]);
                }
            }

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LectureSheetController@index');
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
        $lecture_sheet=LectureSheet::select('lecture_sheets.*')->find($id);
        return view('admin.lecture_sheet.show',['lecture_sheet'=>$lecture_sheet]);
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
        $lecture_sheet = LectureSheet::find($id);
        $data['lecture_sheet'] = LectureSheet::find($id);
        $data['lecture_sheet_topics'] = LectureSheetTopic::pluck('name', 'id');
        $lecture_sheet_topic_lecture_sheets = LectureSheetTopicLectureSheet::where(['lecture_sheet_id'=>$lecture_sheet->id])->get();

        $selected_lecture_sheet_topics = array();
        if(isset($lecture_sheet_topic_lecture_sheets))
        {
            foreach ($lecture_sheet_topic_lecture_sheets as $lecture_sheet_topic_lecture_sheet)
            {
                $selected_lecture_sheet_topics[] = $lecture_sheet_topic_lecture_sheet->lecture_sheet_topic_id;
            }
        }

        $data['selected_lecture_sheet_topics'] = $selected_lecture_sheet_topics;

        $data['module_name'] = 'Lecture Sheet';
        $data['title'] = 'Lecture Sheet Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.lecture_sheet.edit', $data);
        
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

        $lecture_sheet = LectureSheet::find($id);

        if($lecture_sheet->name != $request->name) {

            if (LectureSheet::where(['name'=>$request->name])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Lecture Sheet already exists for the batch');
                return redirect()->action('Admin\LectureSheetController@edit',[$id])->withInput();
            }

        }

        $lecture_sheet->name = $request->name;
        $lecture_sheet->pdf_file = $request->pdf_file;
        $lecture_sheet->updated_by=Auth::id();
        $lecture_sheet->push();

        if($request->lecture_sheet_topic_id)
        {
            if(LectureSheetTopicLectureSheet::where(['lecture_sheet_id'=>$lecture_sheet->id])->first())
            {
                LectureSheetTopicLectureSheet::where(
                 ['lecture_sheet_id'=>$lecture_sheet->id])->Delete();
            }

            foreach ($request->lecture_sheet_topic_id as $lecture_sheet_topic)
            {
                if($lecture_sheet_topic == '')continue;
                LectureSheetTopicLectureSheet::insert(['lecture_sheet_id'=>$lecture_sheet->id,'lecture_sheet_topic_id'=>$lecture_sheet_topic]);
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
      
        $lecture_sheet_deletedby = LectureSheet::find($id);
        $lecture_sheet_deletedby->deleted_by=Auth::id();
        $lecture_sheet_deletedby->push();

        LectureSheet::destroy($id); // 1 way
        LectureSheetTopicLectureSheet::where(['lecture_sheet_id'=>$id])->Delete();
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LectureSheetController@index');
    }
}