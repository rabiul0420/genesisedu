<?php

namespace App\Http\Controllers\Admin;

use App\Batches;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Discount;
use App\DiscountHistory;
use App\Doctors;
use App\SendSms;
use App\SmsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MentorTopicsController extends Controller
{
    use SendSms;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['discounts']=DB::table('discounts')
        ->join('doctors','doctors.id','discounts.doctor_id')
        ->join('batches','batches.id','discounts.batch_id')
        ->join('users','users.id','discounts.created_by')
        ->select('discounts.*','doctors.name','batches.name as batch_name' , 'users.name as user_name','doctors.bmdc_no')
        ->get();     
        return view('admin.discount.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $data['batches'] = Batches::where('status',1)
             ->get();
         $data['doctors'] = Doctors::pluck('name', 'id');
         $data['number']= Doctors:: pluck('mobile_number');
        
        $years = array(''=>'Select year');
        for($year = date("Y");$year>=2017;$year--){
            $years[$year] = $year;
        }
        return view('admin.discount.create',[
            'years'=>$years,
        ]);
    }




    public function generateCouponCode( $doctor_id ){
        $code = strtoupper( Str::random(6));
        if( Discount::where( 'discount_code', $code )->exists() ) {
            return $this->generateCouponCode( $doctor_id);
        }
        return $code;
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
            'batch_id' => ['required'],
            'amount' => ['required'],
            'doctor_id' => ['required'],
            'code_duration' => ['required'],
            'reference'=> ['required'],
            // 'status' => ['required']
            // // 'mobile_number' => ['required'],

        ]);

        if ($validator->fails()){  
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\DiscountController@create')->withInput();
        }

        //dd($request->batch_id);

        // $discount = new Discount;
        // $discount->batch_id = $request->batch_id;
        // $discount->amount = $request->amount;
        // $doctor_ids = $request->doctor_id;
        // $discount->code_duration = $request->code_duration;
        // $discount->mobile_number = $request->mobile_number;

        // $doctor_discount = [];

        // $discount->reference = $request->reference;

        foreach( $request->doctor_id as $doctor_id){
            foreach( $request->batch_id as $batch_id){

                    if ( !Discount::where(['doctor_id'=>$doctor_id,'batch_id'=>$request->batch_id,'used'=>0])->exists() ){ 
                        
                        $doctor_discount = [
                            'batch_id'=>$batch_id,
                            'amount'=> $request->amount,
                            'doctor_id'=>$doctor_id,
                            'code_duration'=>$request->code_duration,
                            'discount_code'=> $this->generateCouponCode( $doctor_id ),
                            'reference'=> $request->reference,
                            'created_by'   => Auth::id(), 
                            
                        ];

                      
                    
                        if( $discount  = Discount::create( $doctor_discount ) )
                        {
                            $doctor= Doctors::where('id','doctor_id')->first();
                            $admin_id = Auth::id();
                            $smsLog = new SmsLog();
                            $response = null;

                            $mob = '88' . Doctors::where('id', $doctor_id)->value('mobile_number');
                            $ch = curl_init();
                            $msg = 'Dear Doctor, Use promo code "' . $discount->discount_code . '" worth discount TK.' . $discount->amount . ' for Batch "' . $discount->batch->name . '". within ' . $discount->code_duration . ' hours.' . ' Please login at ' . 'www.genesisedu.info';
                            $msg = $msg;
                            // $ch = curl_init();
                            // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
                            // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                            // $response = curl_exec($ch);
                            // curl_close($ch);
                            // $smsLog->set_response( $response,$doctor_id,$mob,$admin_id)->set_event('Discount')->save();
                            $this->send_custom_sms($doctor,$msg,'Discount',$isAdmin = true);
                        }
                    }
            }
        }
        
        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\DiscountController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)

    {   
        $data['batches'] = Batches::pluck('name', 'id');
        $data['discounts'] = Discount::find($id);
        return view('admin.discount.edit', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        $data['discounts']=DB::table('discounts')
        ->join('doctors','doctors.id','discounts.doctor_id')
        ->join('batches','batches.id','discounts.batch_id')->select('discounts.*','doctors.name','batches.name as batch_name')->where('discounts.id',$id)
        ->first(); 
        // dd($data['discounts']->id);
        return view('admin.discount.edit', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        $validator = Validator::make($request->all(), [
            'batch_id' => ['required'],
            'amount' => ['required'],
            'doctor_id' => ['required'],
            'code_duration' => ['required']

        ]);
        
       
        // if ($validator->fails()){
        //     Session::flash('class', 'alert-danger');
        //     return redirect()->action('Admin\DiscountController@edit')->withInput();
        // }

        $discount = Discount::find($id);
        $discount->amount = $request->amount;
        $discount->code_duration = $request->code_duration;
        $discount->status=$request->status;
        $discount->push();

        $discount = new DiscountHistory;
        $discount->discount_code_id = $id;
        $discount->updated_at =Carbon::now();
        $discount->updated_by =Auth::id();
        $discount->save();
        
        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\DiscountController@index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Discount::destroy($id);
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DiscountController@index');
    }
}
