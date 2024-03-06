<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Coupons;
use App\Discount;
use App\Product;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class CouponcodeController extends Controller
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
        $batches = Discount::get()->pluck('batch_id', 'id');
        $coupon_code = Coupons::get();
        $title = 'Genesisedu.info : Coupon Code List';
        return view('admin.coupon_code.list',['coupon_code'=>$coupon_code,'batches'=>$batches,'title'=>$title]);
    }


    public function coupon_list()
    {
        $coupon_list = DB::table('coupon_code as d1')
            ->leftjoin('discount as d2', 'd1.batch_id', '=','d2.id');

        $coupon_list = $coupon_list->select('d1.*','d2.batch_id as batch_id');


        return Datatables::of($coupon_list)
            ->addColumn('action', function ($coupon_list) {
                return view('admin.coupon_code.ajax_list',(['coupon_list'=>$coupon_list]));
            })
            ->make(true); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // $user=Product::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $batches = Discount::get()->pluck('batch_name', 'id');
        $coupons = Coupons::get();
        $title = 'Genesisedu.info: Coupon Code Create';
        return view('admin.coupon_code.create',(['title'=>$title,'coupons'=>$coupons,'batches'=>$batches]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


            $coupon = new Coupons;
            $coupon->coupon_code = $request->coupon_code;
            $coupon->batch_id=$request->batch_id;
            //$coupon->updated_at=$request->updated_at;
            //$coupon->created_at =$request->created_at ;

       

            $coupon->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\CouponcodeController@index');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=Discount::select('users.*')
                   ->find($id);
        return view('admin.coupon_code.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=Product::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $batches=Discount::find($id);


        $title = 'Genesisedu.info : Coupon Code Edit';


        return view('admin.coupon_code.edit',['discount'=>$discount,'title'=>$title]);
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
            'batch_name' => ['required'],
            'price' => ['required'],
            'coupon_price' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid phone number");
            return back()->withInput();
        }

        $batch=Discount::find($id);

        if($request->batch_name != $batch_name->batch_name){
            if (Discount::where('batch_name',$request->batch_name)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Book already exists');
                return redirect()->back()->withInput();
            }
        }


        $batches->batch_name=$request->batch_name;
        $batches->coupon_price=$request->coupon_price;
        $batches->price = $request->price;
        


        $batches->push();


        Session::flash('message', 'Record has been updated successfully');

        return back();

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function coupon_create(){
        $data['batches'] =Discount::get()->pluck('batch_name', 'id');

        return view('admin.coupon_code.coupon_create',$data);
    }



    public function coupon_code_generate(Request $request){

        $batch_id = $request->batch_id;
        $number = $request->number;

        for($i=1;$i<=$number;$i++){
            $rand = rand(10000000, 99999999);
            if(!Coupons::where('coupon_code',$rand)->exists()){
                Coupons::insert([
                    'coupon_code'=>$rand,
                    'batch_id'=> $batch_id,
                    'type' => $type
                ]);
            }

        }
        return redirect();
    }



}
