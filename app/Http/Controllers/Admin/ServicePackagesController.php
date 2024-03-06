<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\ServicePackages;
use Illuminate\Http\Request;
use App\Institutes;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;


class ServicePackagesController extends Controller
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

        /*  if(!$user->hasRole('Admin')){
              return abort(404);
          }*/
        $data['service_packages'] = ServicePackages::get();
        $data['module_name'] = 'Service Packages';
        $data['title'] = 'Genesis Admin : Service Packages List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.service_packages.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user=Institutes::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/


        $data['module_name'] = 'Service Packages';
        $data['title'] = 'Genesis Admin : Service Packages Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';
        return view('admin.service_packages.create',$data);
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
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\ServicePackagesController@create')->withInput();
        }

        /*if (Institutes::where('bmdc_no',$request->bmdc_no)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This BMDC NO  already exists');
            return redirect()->action('Admin\Settings\InstitutesController@create')->withInput();
        }*/

        $service_package = new ServicePackages();
        $service_package->name = $request->name;
        $service_package->status = $request->status;

        $service_package->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\ServicePackagesController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service_package=ServicePackages::select('service_packages.*')
            ->find($id);
        return view('admin.service_packages.show',['service_package'=>$service_package]);
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

        $data['service_package'] = ServicePackages::find($id);

        $data['module_name'] = 'Service Packages';
        $data['title'] = 'Genesis Admin : Service Packages Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.service_packages.edit',$data);
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
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\ServicePackagesController@update')->withInput();
        }

        $service_package = ServicePackages::find($id);

        $service_package->name = $request->name;
        $service_package->status = $request->status;

        $service_package->push();

        Session::flash('message', 'Record has been updated successfully');

        return back();

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

        ServicePackages::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ServicePackagesController@index');
    }





}
