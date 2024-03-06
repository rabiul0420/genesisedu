<?php

namespace App\Http\Controllers\Admin;
use App\Sessions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Institutes;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;


class SessionsController extends Controller
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
        $data['sessions'] = Sessions::get();
        $data['module_name'] = 'Sessions';
        $data['title'] = 'Genesis Admin : Sessions List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.sessions.list',$data);
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


        $data['module_name'] = 'Sessions';
        $data['title'] = 'Genesis Admin : Sessions Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';
        return view('admin.sessions.create',$data);
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
            'name' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\SessionsController@create')->withInput();
        }

        if (Sessions::where(['name'=>$request->name,'duration'=>$request->duration])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Session already exists');
            return redirect()->action('Admin\SessionsController@create')->withInput();
        }

        $session = new Sessions();
        // $session->year = $request->year;
        $session->name = $request->name;
        $session->duration = $request->duration;
        $session->session_code = $request->session_code;
        $session->status = $request->status;
        // $session->show_admission_form = $request->show_admission_form;
        $session->created_by = Auth::id();


        $session->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\SessionsController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $session=Sessions::select('service_packages.*')
            ->find($id);
        return view('admin.sessions.show',['session'=>$session]);
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

        $data['session'] = Sessions::find($id);

        $data['module_name'] = 'Sessions';
        $data['title'] = 'Genesis Admin : SessionsEdit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.sessions.edit',$data);
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
            'name' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\SessionsController@edit',[$id])->withInput();
        }

        $session = Sessions::find($id);

        if($session->name != $request->name || $session->duration != $request->duration)
        {
            if (Sessions::where(['name'=>$request->name,'duration'=>$request->duration])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Session already exists');
                return redirect()->action('Admin\SessionsController@edit',[$id])->withInput();
            }
        }

        // $session->year = $request->year;
        $session->name = $request->name;
        $session->duration = $request->duration;
        $session->session_code = $request->session_code;
        $session->status = $request->status;
        // $session->show_admission_form = $request->show_admission_form;
        $session->updated_by = Auth::id();

        $session->push();

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

        Sessions::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\SessionsController@index');
    }





}
