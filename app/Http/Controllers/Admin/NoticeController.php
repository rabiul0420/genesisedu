<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Notice;
use App\Doctors;
use App\DoctorNotices;
use App\DoctorNoticeView;
use App\NoticeBatch;
use Illuminate\Http\Request;
use App\Batches;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Sessions;
use App\Institutes;
use App\Courses;
use App\NoticeAssign;
use App\NoticeBatchNotice;
use App\NoticeDiscipline;
use App\NoticeFaculty;

class NoticeController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Auth::loginUsingId(1);
        //$this->middleware('auth');
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
        $data['notices'] = Notice::get();
        return view('admin.notice.list',$data);
    }

    public function notice_list() {
        $notice_list = DB::table('notice as d1');

        $notice_list->select(
            'd1.id as id',
            'd1.created_at as create_time',
            'd1.title as notice_title',
            'd1.attachment as attachment',
            'd1.type as notice_type',
            'd1.status as status',
        );

        $notice_list = $notice_list->whereNull('d1.deleted_at');

        return DataTables::of($notice_list)
            ->addColumn('action', function ($notice_list) {
                return view('admin.notice.notice_ajax_list',(['notice_list'=>$notice_list]));
            })
            
            ->addColumn('attachment',function($notice_list) {
                return '<span>'. '<a href="'.('/'.$notice_list->attachment ).' " target="_blank">'.( $notice_list->attachment ? "View Attachment" : "" ).'</a>' . '</span>';
            })

            ->addColumn('status',function($notice_list) {
                return '<span style="color:' .( $notice_list->status == 1 ? 'green;':'red;').' font-size: 14px;  ">'. ($notice_list->status == 1 ? 'Active':'Inactive') . '</span>';
            })

            ->addColumn('notice_type',function($notice_list) {

                if($notice_list->notice_type == "I") {
                    return "Individual";
                }
                else if($notice_list->notice_type == "A") {
                    return "All";
                }
                else if($notice_list->notice_type == "B") {
                    return "Batch";
                }
                else {
                    return "Others";
                }
            })
            ->rawColumns(['action','status', 'attachment', 'notice_type'])

        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user=Institutes::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        
        $data['title'] = 'Genesis Admin : Notice Create';
        return view('admin.notice.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $notice = new Notice();
        $notice->title = $request->title;
        $notice->notice = $request->notice;

        if($request->hasFile('attachment')){
            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $filename = rand(12,98).'_'.time().'.'.$extension;
            $file->move('upload/notice/',$filename);
            $notice->attachment = 'upload/notice/'.$filename;
        }
        else {
            $notice->attachment = '';
        }

        $notice->type = $request->type;
                
        $notice->created_by = Auth::id();
        $notice->status = $request->status;

        $notice->save();

        if ($request->doctor_id) {
            foreach ($request->doctor_id as $k => $value) {
                
                DoctorNotices::insert(['doctor_id' => $value, 'notice_id' => $notice->id]);
            }
        }

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\NoticeController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['id'] = $id;
        $notice = Notice::where('id', $id)->first();
        $data['notices'] = Notice::where('id', $id)->first();

        if ($notice->type=='I'){
            $data['doctors'] = DoctorNotices::where('notice_id', $id)->get();
        }

        return view('admin.notice.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=Institutes::find(Auth::id());
 
         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/
        //echo $id;exit;
        $data['notice'] = Notice::find($id);

        $data['doctors'] = Doctors::select(DB::raw("CONCAT(name,' - ',bmdc_no) AS full_name"),'id')->orderBy('id', 'DESC')->pluck('full_name', 'id');
        //$data['doctors'] = Doctors::pluck('name', 'id');

        $selected_doctors = DoctorNotices::where('notice_id', $id)->pluck('doctor_id');
        $array_selected_doctor = array();
        foreach($selected_doctors as $doctors){
            $array_selected_doctor[] = $doctors;
        }
        $data['selected_doctors'] = collect($array_selected_doctor);

        return view('admin.notice.edit', $data);
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
            'title' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\NoticeController@edit')->withInput();
        }

        $notice = Notice::find($id);

        $notice->title = $request->title;
        $notice->notice = $request->notice;

        if($request->hasFile('attachment')){
            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $filename = rand(12,98).'_'.time().'.'.$extension;
            $file->move('upload/notice/',$filename);
            $notice->attachment = 'upload/notice/'.$filename;
        }

        $notice->type = $request->type;
        
        $notice->updated_by = Auth::id();
        $notice->status = $request->status;

        $notice->push();

        if ($request->doctor_id) {
            
            $doctor_notice = DoctorNotices::where('notice_id', $id)->get();
            foreach ($doctor_notice as $k => $doctors) {
                DoctorNotices::destroy($doctors->id);
            }

            foreach ($request->doctor_id as $k => $value) {
                DoctorNotices::insert(['doctor_id' => $value, 'notice_id' => $id]);
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


        if( $notice = Notice::find( $id ) ) {
            Notice::where( 'id', $id)->update( ['deleted_by' => Auth::id( ) ] );
            $notice->delete( );
        }

        if( NoticeAssign::where(['notice_id'=>$id])->exists() ) {
            NoticeAssign::where(['notice_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            NoticeAssign::where(['notice_id'=>$id])->delete();
        }

        if( NoticeDiscipline::where(['notice_id'=>$id])->exists() ) {
            NoticeDiscipline::where(['notice_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            NoticeDiscipline::where(['notice_id'=>$id])->delete();
        }

        if( NoticeFaculty::where(['notice_id'=>$id])->exists() ) {
            NoticeFaculty::where(['notice_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            NoticeFaculty::where(['notice_id'=>$id])->delete();
        }

        if( NoticeBatchNotice::where(['notice_id'=>$id])->exists() ) {
            NoticeBatchNotice::where(['notice_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            NoticeBatchNotice::where(['notice_id'=>$id])->delete();
        }

        if( DoctorNotices::where(['notice_id'=>$id])->exists() ) {
            DoctorNotices::where(['notice_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            DoctorNotices::where(['notice_id'=>$id])->delete();
        }


        if( DoctorNoticeView::where(['notice_id'=>$id])->exists() ) {
            DoctorNoticeView::where(['notice_id'=>$id])->update( ['deleted_by' => Auth::id( ) ] );
            DoctorNoticeView::where(['notice_id'=>$id])->delete();
        }


        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\NoticeController@index');
    }





}
