<?php

namespace App\Http\Controllers\Admin;

use App\Districts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Upazilas;
use Validator;
use Session;

class UpazilaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['upazilas']= Upazilas::orderBy('id','desc')->get();

        return view('admin.upazila.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['districts']= Districts::get()->pluck('name','id');
        return view('admin.upazila.create', $data);
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
            'bn_name' => ['required'],
            'district_id' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\UpazilaController@edit')->withInput();
        }

        if (Upazilas::where(['name'=>$request->name , 'bn_name'=>$request->bn_name , 'district_id'=>$request->district_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Upazila  already exists');
            return redirect()->action('Admin\UpazilaController@create')->withInput();
        }

        $upazila = new Upazilas;
        $upazila->name = $request->name;
        $upazila->bn_name = $request->bn_name;
        $upazila->district_id = $request->district_id;
        $upazila->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\UpazilaController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //show
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        $data['upazila'] = Upazilas::find($id);
        $data['districts']= Districts::get()->pluck('name','id');
        return view('admin.upazila.edit', $data);
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
            'bn_name' => ['required'],
            'district_id' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\UpazilaController@create')->withInput();
        }

        if (Upazilas::where(['name'=>$request->name , 'district_id'=>$request->district_id])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Upazila  already exists');
            return redirect()->action('Admin\UpazilaController@create')->withInput();
        }

        $upazila = Upazilas::find($id);
        $upazila->name = $request->name;
        $upazila->bn_name = $request->bn_name;
        $upazila->district_id = $request->district_id;
        $upazila->push();
        // $discount->update([
        //     'name' => $request->name,
        //     'bn_name' => $request->bn_name,
        //     'district_id' => $request->district_id
        // ]);

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\UpazilaController@index');
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
        return redirect()->action('Admin\UpazilaController@index');
    }
}
