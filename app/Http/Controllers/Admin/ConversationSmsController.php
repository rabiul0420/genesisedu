<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctors;
use App\Questionlink;
use DB;
use Validator;
use App\ConversationSms;
use App\SendSms;
use App\SmsLog;
use Session;
use Auth;
use Yajra\DataTables\Facades\DataTables;

class ConversationSmsController extends Controller
{
    use SendSms;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $conversations_sms = ConversationSms::with('question_link','user')->get();
        return view('admin.call_center.list',compact('conversations_sms'));
    }

    public function con_sms_list() {
        $con_sms_list = DB::table('conversation_sms as d1')
                        ->leftjoin('question-link as d2', 'd1.question_link_id', '=','d2.id' )
                        ->leftjoin('users as d3', 'd1.sms_sender', '=','d3.id' );

        $con_sms_list->select(
            'd1.id as id',
            'd1.mobile_number as mobile_number',
            'd1.short_sms as sms',
            'd2.title as question_title',
            'd2.question_link as question_link',
            'd3.name as sender_name',
        );

        return DataTables::of($con_sms_list)
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['doctors'] = Doctors::pluck('name', 'id');
        $data['question_tittles'] = Questionlink::pluck('title','id');
        return view('admin.call_center.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $validator= Validator::make($request->all(),[
        'mobile_number' => ['required']
       ]);

       if ($validator->fails()){
        Session::flash('class', 'alert-danger');
        session()->flash('message','Please enter valid data!!!');
        return redirect()->action('Admin\ConversationSmsController@create')->withInput();
        }
       $doctor = Doctors::where('mobile_number',$request->mobile_number)->first();
        $conversation_sms =new ConversationSms();
        $conversation_sms->mobile_number = $request->mobile_number;
        if($request->title_id){
            $conversation_sms->question_link_id = $request->title_id ;
        }
        if($request->short_sms){
            $conversation_sms->short_sms = $request->short_sms ;
        }
        
        if($doctor != null){
            $conversation_sms->doctor_id =$doctor->id;
            // dd($conversation_sms);
        }
        
        $conversation_sms->sms_sender = Auth::id();
        
        $conversation_sms->save();

        if( $doctor == null)
        {
            $this->sendMessageUnregistered($request->mobile_number);
        }else{

            if( $request->mobile_number)
            {
                $this->sendMessage($request->mobile_number) ;
            }
        }

        return redirect('admin/conversation-sms');
    }

    protected function sendMessage($mobile_number){
        $user_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;
        $conversation_sms = ConversationSms::with('question_link')->where('mobile_number', $mobile_number)->orderBy('id','DESC')->first();
        $websitename='https://www.genesisedu.info/';
        if($conversation_sms->question_link_id && $conversation_sms->short_sms == null ){
            $msg = 'Dear Doctor, ' . $conversation_sms->question_link->question_link . ' - ' . 'GENESIS';
        }

        if($conversation_sms->question_link_id == null && $conversation_sms->short_sms ){
            $msg = 'Dear Doctor, ' . $conversation_sms->short_sms . ' - ' . 'GENESIS';
        }

        if($conversation_sms->question_link_id != null && $conversation_sms->short_sms != null ){
            $msg = 'Dear Doctor, ' . $conversation_sms->question_link->question_link . ' - ' .$conversation_sms->short_sms. ' - ' . 'GENESIS';
        }

        $mob = '88' . $mobile_number;
        $doctor = Doctors::where('mobile_number',$mobile_number)->first();
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$conversation_sms->doctor_id ?? ' ',$mob,$user_id)->set_event('Call Center')->save();
        $this->send_custom_sms($doctor,$msg,'Call Center',$isAdmin = true);     
    }

    protected function sendMessageUnregistered($mobile_number){
        $user_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;
        $conversation_sms = ConversationSms::with('question_link')->where('mobile_number', $mobile_number)->orderBy('id','DESC')->first();
        $websitename='https://www.genesisedu.info/';

        if($conversation_sms->question_link_id && $conversation_sms->short_sms == null ){
            $msg = 'Dear Doctor, ' . $conversation_sms->question_link->question_link . ' - ' . 'GENESIS';
        }

        if($conversation_sms->question_link_id == null && $conversation_sms->short_sms ){
            $msg = 'Dear Doctor, ' . $conversation_sms->short_sms . ' - ' . 'GENESIS';
        }

        if($conversation_sms->question_link_id != null && $conversation_sms->short_sms != null ){
            $msg = 'Dear Doctor, ' . $conversation_sms->question_link->question_link . ' - ' .$conversation_sms->short_sms. ' - ' . 'GENESIS';
        }

        $mob = '88' . $mobile_number;
        $this->send_custom_sms_unregistered($mobile_number,$msg,'Call Center',$isAdmin = true);     
    }
   

    public function search_doctors_phone(){
        $text =  $_GET['term'];
        $text = $text['term'];
        $data = Doctors::select(DB::raw("CONCAT(name,' - ',mobile_number) AS name_phone"),'mobile_number')
            ->where('name', 'like', '%'.$text.'%')
            ->orWhere('mobile_number', 'like', '%'.$text.'%')
            ->get();
        //$data = DB::table('institution')->where('institution_type_id',$content_section_id)->where('name', 'like', $text.'%')->get();
        echo json_encode( $data);
    }

    public function question_link(Request $request){
        $question_link = Questionlink::where('id',$request->title_id)->first();
        return view('admin.ajax.question_link',compact('question_link'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
