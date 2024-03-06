<?php

namespace App\Http\Controllers\Admin;

use App\DiscountRequest;
use App\Doctors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
class DiscountRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
      $discount_requests =  DiscountRequest::with('doctor','doctor_course.batch')->get();
        return view('admin.discount_request.list',compact('discount_requests'));
    }

    public function discount_request_feedback(Request $request){
        $value = $request->value;
        $id = $request->id;
        $note = $request->note;
        $discount = DiscountRequest::find($id);

       if( !$discount ) {
            return response( [ 'changed' => false ] );
        }

        $changed = $discount->status == $value;
        if($changed){
            $discount->admin_note = $note;
            $discount->status = '1';
            $discount->push();
        }

        return response([ 'discount' => $discount, 'id' => $id , 'changed' => $changed ]);


    }

    public function get_discount($id){
    //    $doctor = Doctors::find($id);
    //    Session::put('doctor_id',$doctor->id);
    //    Session::put('bmdc_no',$doctor->bmdc_no);
       return redirect()->action('Admin\DiscountController@create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
