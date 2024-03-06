<?php

namespace App\Http\Controllers\Admin;
use App\Advertisements;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Institutes;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;


class AdvertisementsController extends Controller
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
        $data['advertisements'] = Advertisements::get();
        $data['title'] = 'Genesis Admin : Advertisements';
        return view('admin.advertisements.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
        $data['module_name'] = 'Advertisements';
        $data['advertisements'] = Advertisements::get();
        $data['title'] = 'Genesis Admin : Advertisements Create';
        return view('admin.advertisements.create',$data);
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
            'image' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Enter Fields Correctly');
            return redirect()->action('Admin\AdvertisementsController@create')->withInput();
        }

        $advertisements = new Advertisements;
        $advertisements->image = $request->image;

        if ($request->file('image')){
            $file=$request->file('image');
            $fileName = md5(uniqid(rand(), true)).'.'.strtolower(pathinfo($file->getClientOriginalName(),PATHINFO_EXTENSION)) ;
            $destinationPath = 'store/' ;
            $file->move($destinationPath,$fileName);
            $advertisements->image = $destinationPath.$fileName;
        }

        $advertisements->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\AdvertisementsController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['module_name'] = 'Advertisements';
        $data['title'] = 'Genesis Admin : Advertisements Create';
        return view('admin.advertisements.create',$data);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data['advertisements'] = Advertisements::find($id);

        $data['title'] = 'Genesis Admin : Advertisements';


        return view('admin.advertisements.edit',$data);
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
            'image' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\AdvertisementsController@update')->withInput();
        }

        $advertisements = Advertisements::find($id);
        
        if($request->image){
            $advertisements->image = $request->image;
        }

        if ($request->file('image')){
            $file=$request->file('image');
            $fileName = md5(uniqid(rand(), true)).'.'.strtolower(pathinfo($file->getClientOriginalName(),PATHINFO_EXTENSION)) ;
            $destinationPath = 'store/' ;
            $file->move($destinationPath,$fileName);
            $advertisements->image = $destinationPath.$fileName;
        }

        $advertisements->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\AdvertisementsController@index');

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Advertisements::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\AdvertisementsController@index');
    }





}
