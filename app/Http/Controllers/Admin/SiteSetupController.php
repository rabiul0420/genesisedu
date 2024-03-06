<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use Validator;
use Session;
use App\SiteSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class SiteSetupController extends Controller
{


    private $setup_names = [
        'manual_payment' => [ 'value' => 'NO' ],
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $data = [];

        $data ['setup_lists'] = SiteSetup::get( );

        // foreach( $this->setup_list as $name => $setup ) {
        //     $data['setup_list' ] = [
        //         'name' => $name,
        //         'value' => $setup_list->where('name', $name)->value( 'value' ) ?? $setup[ 'value' ]
        //     ];
        // }
        return view('admin.payment_status.list',$data);

        

    }
    public function create()
    {
        $data['courses'] = Courses::pluck('name','id');
        $data['payment_status'] = 'SiteSetup';
        $data['title'] = 'Genesis Admin : Payment Status ';
        $data['breadcrumb'] = explode('/', $_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';
        return view('admin.payment_status.create',$data);

    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'bkash_number' => 'required',
            'value' => 'required'
        ]);

        if($validator->fails()){
            Session::flash('class','alert-danger');
            return redirect()->action('Admin\SiteSetupController@create')->withInput();
        }

        $payment_status = new SiteSetup();
        $payment_status->course_id = $request->course_id;
        $payment_status->bkash_number = $request->bkash_number;
        $payment_status->value = $request->value;
        $payment_status->created_by = Auth::id();
        $payment_status->push();
        Session::flash('message' , 'Record has been added successfully');
        return redirect()->action('Admin\SiteSetupController@index');
    }

    public function show($id)
    {
        $site_setup=SiteSetup::select('site_setup.*')
            ->find($id);
        return view('admin.payment_status.edit',['site_setup'=>$site_setup]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    
        $data['payment_status']=SiteSetup::find($id);
        $data['name'] = 'SiteSetup';
        $data['title'] = 'Genesis Admin :SiteSetup Edit';
        $data['submit_value'] = 'Update';


        return view('admin.payment_status.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id )
    {
        //
        $validator = Validator::make($request->all(), [
            'value' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\SiteSetupController@edit',[$id])->withInput();
        }
        $paymen_value = SiteSetup::find($id);
        $paymen_value->bkash_number = $request->bkash_number;
        $paymen_value->value = $request->value;
        $paymen_value->updated_by = Auth::id();
        $paymen_value->push();
        Session::flash('message', 'Record has been updated successfully');

         return redirect()->action('Admin\SiteSetupController@index');
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
