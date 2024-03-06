<?php

namespace App\Http\Controllers\Admin;
use App\DoctorsReviews;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Institutes;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;


class DoctorsReviewsController extends Controller
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
        $data['doctors_reviews'] = DoctorsReviews::get();
        $data['title'] = 'Genesis Admin : Doctors Review';
        // $data['module_name'] = 'Coming By';
        // $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.doctors_reviews.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
        $data['module_name'] = 'Coming By';
        $data['title'] = 'Genesis Admin : Doctors Review Create';
        // $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        // $data['submit_value'] = 'Submit';
        return view('admin.doctors_reviews.create',$data);
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
            'designation' => ['required'],
            'comment' => ['required'],
            'image' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Enter Fields Correctly');
            return redirect()->action('Admin\DoctorsReviewsController@create')->withInput();
        }

        $doctors_reviews = new DoctorsReviews;
        $doctors_reviews->name = $request->name;
        $doctors_reviews->designation = $request->designation;
        $doctors_reviews->comment = $request->comment;
        $doctors_reviews->image = $request->image;

        if ($request->file('image')){
            $file=$request->file('image');
            $fileName = md5(uniqid(rand(), true)).'.'.strtolower(pathinfo($file->getClientOriginalName(),PATHINFO_EXTENSION)) ;

            $destinationPath = 'store/' ;

            $file->move($destinationPath,$fileName);
            $doctors_reviews->image = $destinationPath.$fileName;
        }

        $doctors_reviews->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\DoctorsReviewsController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $doctors_reviews=DoctorsReviews::select('service_packages.*')
        //     ->find($id);
        // return view('admin.doctors_reviews.show',['doctors_reviews'=>$service_package]);
        $data['doctors_reviews'] = DoctorsReviews::get();
        $data['title'] = 'Genesis Admin : Doctors Review';
        // $data['module_name'] = 'Coming By';
        // $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.doctors_reviews.list',$data);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=Institutes::find(Auth::id());
 
         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/

        $data['doctors_reviews'] = DoctorsReviews::find($id);

        $data['title'] = 'Genesis Admin : Coming ByEdit';
        // $data['module_name'] = 'Coming By';
        // $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        // $data['submit_value'] = 'Update';


        return view('admin.doctors_reviews.edit',$data);
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
            'designation' => ['required'],
            'comment' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\DoctorsReviewsController@update')->withInput();
        }

        $doctors_reviews = DoctorsReviews::find($id);

        $doctors_reviews->name = $request->name;
        $doctors_reviews->designation = $request->designation;
        $doctors_reviews->comment = $request->comment;
        
        if($request->image){
            $doctors_reviews->image = $request->image;
        }

        if ($request->file('image')){
            $file=$request->file('image');
            $fileName = md5(uniqid(rand(), true)).'.'.strtolower(pathinfo($file->getClientOriginalName(),PATHINFO_EXTENSION)) ;

            $destinationPath = 'store/' ;

            $file->move($destinationPath,$fileName);
            $doctors_reviews->image = $destinationPath.$fileName;
        }

        $doctors_reviews->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\DoctorsReviewsController@index');

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$user=Institutes::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        DoctorsReviews::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DoctorsReviewsController@index');
    }





}
