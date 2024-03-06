<?php

namespace App\Http\Controllers\Admin;

use App\Batches;
use App\Courses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Discount;
use App\DiscountHistory;
use App\Doctors;
use App\Exports\DiscountExport;
use App\SendSms;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
use Session;
use App\SmsLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DiscountController extends Controller
{ 
    use SendSms;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $start_date = date('Y-m-d', strtotime(Discount::min('created_at')));

        $end_date = date('Y-m-d', strtotime(Discount::max('created_at')));

        return view('admin.discount.list', compact('start_date', 'end_date'));
    }

    public function listQuery() 
    {
        return DB::table('discounts')
            ->leftJoin('doctors', 'doctors.id', 'discounts.doctor_id')
            ->join('batches', 'batches.id', 'discounts.batch_id')
            ->join('users', 'users.id', 'discounts.created_by')
            ->select(
                'discounts.*',
                'doctors.name as doctor_name',
                'doctors.bmdc_no as bmdc_no',
                'batches.name as batch_name',
                'users.name as user_name'
            )
            ->orderBy('id', 'desc')
            ->whereNull('discounts.deleted_at')
            ->when(request()->date_from, function ($query, $date_from) {
                $query->whereDate('discounts.created_at', '>=', $date_from);
            })
            ->when(request()->date_to, function ($query, $date_to) {
                $query->whereDate('discounts.created_at', '<=', $date_to);
            });
    }

    public function discount_list(Request $request)
    {
        return DataTables::of($this->listQuery())
            ->addColumn('action', function ($discount) {
                return '
                    <a href="' . url('admin/discount/' . $discount->id . '/edit') . '" class="btn btn-xs btn-primary">Edit</a>
                    <button type="button" class="btn btn-xs btn-primary edit_history_btn" data-toggle="modal" data-target="#edit_history_' . $discount->id . '" data-id="' . $discount->id . '">Edit History</button>
                ';
            })
            ->addColumn('used', function ($discount) {
               return  ($discount->used == 0)? 'No':'Yes' ;
            })
            ->addColumn('status', function ($discount) {
               return ($discount->status == 1)? 'Active':'Inactive' ;
            })
            ->rawColumns(['action'])
            ->make(true);
        
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
         $data['courses'] = Courses::where('status',1)
            ->pluck('name' , 'id');
         $data['doctors'] = Doctors::pluck('name', 'id');
         $data['number']= Doctors:: pluck('mobile_number');
        $years = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2019;$year--){
            $years[$year] = $year;
        }


        return view('admin.discount.create',$data,[
            'years'=>$years,
        ]);
    }

    
    public function generateCouponCode()
    {
        $code = strtoupper(Str::random(6));

        if(Discount::where('discount_code', $code)->exists()) {
            return $this->generateCouponCode();
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
        // return $request;
        $validator = Validator::make($request->all(), [
            'batch_id'      => ['required'],
            'amount'        => ['required'],
            'phone'         => ['required'],
            'code_duration' => ['required'],
            'reference'     => ['required'],

        ]);

        if ($validator->fails()){  
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\DiscountController@create')->withInput();
        }

        $collections = $request->phone;

        if(is_array($collections)){
            foreach( $collections as $item){
                $doctor = Doctors::where('id', $item)->first();
                $phone = $doctor->phone ?? $item;

                $check = Discount::query()
                    ->where([
                        'batch_id'  => $request->batch_id,
                        'status'    => 1,
                        'used'      => 0,
                    ])
                    ->where(function ($query) use ($item) {
                        $query->where('doctor_id', $item)
                            ->orWhere('phone', $item);
                    })
                    ->exists();

                if (!$check){
                    $smsLog = new SmsLog();
                    $response = null;

                    $discount_data = [
                        'batch_id'      => $request->batch_id,
                        'amount'        => $request->amount,
                        'doctor_id'     => $doctor->id ?? null,
                        'phone'         => $phone,
                        'code_duration' => $request->code_duration,
                        'discount_code' => $this->generateCouponCode(),
                        'reference'     => $request->reference,
                        'created_by'    => Auth::id(),
                    ];
                
                    if($discount = Discount::create($discount_data))
                    {
                        $msg = 'Dear Doctor, Use promo code "' . $discount->discount_code . '" worth discount TK.' . $discount->amount . ' for Batch "' . $discount->batch->name . '". within ' . $discount->code_duration . ' hours.' . ' Please login at ' . 'www.genesisedu.info';

                        $this->send_custom_sms_unregistered($phone, $msg, 'Discount'); 
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
            'batch_id'      => ['required'],
            'amount'        => ['required'],
            'doctor_id'     => ['required'],
            'code_duration' => ['required']

        ]);
        

        $discount = Discount::find($id);   
        $previous_amount = $discount->amount;
        $previous_status = $discount->status;
        $previous_duration = $discount->code_duration;

        $discount->amount = $request->amount;
        $discount->code_duration = $request->code_duration;
        $discount->status=$request->status;


        $discount->push();
 
        $discount = new DiscountHistory;
        $discount->discount_code_id = $id;

        $discount->amount_change = $previous_amount != $request->amount ? 
                $previous_amount . ' - ' . $request->amount : '';

        $discount->status_change = $previous_status != $request->status ?
                ( $previous_status == 0 ? 'Inactive':'Active' ) . ' - ' . ($request->status == 0 ? 'Inactive':'Active') : '';

        $discount->duration_change = $previous_duration != $request->code_duration ?
                $previous_duration . ' - ' . $request->code_duration : '';
        


        if( !$discount->amount_change && !$discount->status_change && !$discount->duration_change ) {
                Session::flash('class', 'alert-danger');
                Session::flash('message', 'Nothing changed');
            return redirect()->action('Admin\DiscountController@index');
        }

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

    public function excelDownload(Request $request)
    {
        $discounts = $this->listQuery()->get();

        foreach($discounts as $discount){
            $array[] = [
                $discount->doctor_name,
                $discount->bmdc_no,
                $discount->phone ? "88" . $discount->phone : "",
                $discount->batch_name,
                $discount->discount_code,
                $discount->amount,
                $discount->code_duration,
                $discount->used ? 'Yes' : 'No',
                $discount->reference,
                $discount->user_name,
                $discount->status ? 'Active' : 'Inactive',
            ];
        }

        return Excel::download(new DiscountExport($array), "discount_{$request->date_from}_{$request->date_to}.xlsx");
    }
}
