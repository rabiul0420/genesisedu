<?php

namespace App\Http\Controllers\Admin;

use App\DiscountRequestNumber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Session;

class DiscountRequestNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discount_request_numbers = DiscountRequestNumber::get();
        return view('admin.discount_request_number.list' ,compact('discount_request_numbers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.discount_request_number.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required'],
            'mobile_number' => ['required'],
            'status' => ['required'],
        ]
        );

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DiscountRequestNumberController@create')->withInput();
        }

        $discount_request_number = new DiscountRequestNumber();
        $discount_request_number->name = $request->name ?? ' ';
        $discount_request_number->mobile_number = $request->mobile_number ?? ' ';
        $discount_request_number->status = $request->status ?? ' ';
        $discount_request_number->save() ?? ' ';
        
        Session::flash('message', 'Record has been added successfully');
        
        return redirect()->action('Admin\DiscountRequestNumberController@index');

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
     $discount_request_number =   DiscountRequestNumber::find($id);
     return view('admin.discount_request_number.edit',compact('discount_request_number'));
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

        $validator = Validator::make($request->all(),[
            'name' => ['required'],
            'mobile_number' => ['required'],
            'status' => ['required'],
        ]
        );

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DiscountRequestNumberController@create')->withInput();
        }

        $discount_request_number =   DiscountRequestNumber::find($id);
        $discount_request_number->name = $request->name ?? '';
        $discount_request_number->mobile_number = $request->mobile_number ?? '';
        $discount_request_number->status = $request->status ?? '';
        $discount_request_number->push();

        Session::flash('message', 'Record has been updated successfully');
        
        return redirect()->action('Admin\DiscountRequestNumberController@index');    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $discount_request_number = DiscountRequestNumber::find($id);
        $discount_request_number->delete();
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DiscountRequestNumberController@index');
    }
}
