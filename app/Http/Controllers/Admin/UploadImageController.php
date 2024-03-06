<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Institutes;
use App\Models\Moreinfo;
use Session;
use Auth;
use Illuminate\Support\Facades\Storage;
use Validator;


class UploadImageController extends Controller
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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data['photos'] = online_image::orderBy('id','desc')->get();
        // $data['title'] = 'Genesis Admin : Photos';
        return view('admin.upload_image.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
        // $data['module_name'] = 'Photos';
        // $data['photos'] = Photos::();
        // $data['title'] = 'Genesis Admin : Photos Create';
        return view('admin.upload_image.create');
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
            return redirect()->action('Admin\UploadImageController@create')->withInput();
        }

        

        // if ($request->file('image')){
        //     $file=$request->file('image');
        //     $fileName = md5(uniqid(rand(), true)).'.'.strtolower(pathinfo($file->getClientOriginalName(),PATHINFO_EXTENSION)) ;
        //     $destinationPath = 'online_image/' ;
        //     $file->move($destinationPath,$fileName);
        //     // $photos->image = $destinationPath.$fileName;
        // }

        $file=$request->file('image');

        $s3file = time().$file->getClientOriginalName();
            
        $filepath = $s3file;

        Storage::disk('s3')->put($filepath,file_get_contents($file));

        return $filepath;
        
        Session::flash('message', 'Image has been Uploaded successfully');

        return redirect()->action('Admin\UploadImageController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $data['module_name'] = 'Photos';
        $data['title'] = 'Genesis Admin : Photos Create';
        return view('admin.upload_image.create',$data);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data['photos'] = Photos::find($id);

        $data['title'] = 'Genesis Admin : Photos';


        return view('admin.upload_image.edit',$data);
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
            return redirect()->action('Admin\UploadImageController@update')->withInput();
        }

        $photos = Photos::find($id);
        
        if($request->image){
            $photos->image = $request->image;
        }

        if ($request->file('image')){
            $file=$request->file('image');
            $fileName = md5(uniqid(rand(), true)).'.'.strtolower(pathinfo($file->getClientOriginalName(),PATHINFO_EXTENSION)) ;
            $destinationPath = 'store/' ;
            $file->move($destinationPath,$fileName);
            $photos->image = $destinationPath.$fileName;
        }

        $photos->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\UploadImageController@index');

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Photos::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\UploadImageController@index');
    }





}
