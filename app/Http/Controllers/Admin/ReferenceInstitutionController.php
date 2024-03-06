<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\QuestionSubject;
use App\Books;
use App\Institutes;
use App\ReferenceInstitute;
use Session;
use Auth;
use Validator;


class ReferenceInstitutionController extends Controller
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




//        $data['subjects'] = QuestionSubject::get();
        $data['institutes'] = ReferenceInstitute::get();

        $data['module_name'] = 'Reference Institute';
        $data['title'] = 'Reference Institute';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.reference_institute.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // $user=QuestionSubject::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['reference_institutes'] = ReferenceInstitute::pluck('name','id');
        $data['module_name'] = 'Reference Institute';
        $data['title'] = 'Reference Institute Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.reference_institute.create',$data);
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
            'institute_name' => ['required'],
            'reference_code' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please input valid data');
            return redirect()->action('Admin\ReferenceInstitutionController@create')->withInput();
        }



        if (ReferenceInstitute::where(['name'=>$request->institute_name])->first()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Institute  already exists');
            return redirect()->action('Admin\ReferenceInstitutionController@create')->withInput();
        }

        else{

            $institute = new ReferenceInstitute();
            $institute->timestamps = false;
            $institute->name = $request->institute_name;
            $institute->reference_code = $request->reference_code;
            $institute->created_by =Auth::id();
            $institute->save();

            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\ReferenceInstitutionController@index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=ReferenceInstitute::select('users.*')
                   ->find($id);
        return view('admin.reference_institute.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=QuestionSubject::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['institutes']=ReferenceInstitute::find($id);
        $data['module_name'] = 'Reference Institute';
        $data['title'] = 'Reference Institute Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';


        return view('admin.reference_institute.edit',$data);
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
            'institute_name' => ['required'],
            'reference_code' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data");
            return redirect()->action('Admin\ReferenceInstitutionController@edit',[$id])->withInput();
        }

        $institute=ReferenceInstitute::find($id);

        if($institute->name != $request->name)
        {
            if (ReferenceInstitute::where(['name'=>$request->name])->first()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Subject  already exists');
                return redirect()->action('Admin\ReferenceInstitutionController@edit',[$id])->withInput();
            }

        }

        $institute->name=$request->institute_name;
        $institute->reference_code=$request->reference_code;
        $institute->updated_by =Auth::id();

        $institute->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\ReferenceInstitutionController@edit',[$id])->withInput();

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        // if(!$user->hasRole('Admin')){
            //     return abort(404);
            // }
        $question_institute = ReferenceInstitute::find($id);
        $question_institute->deleted_by=Auth::id();
        $question_institute->push();
            
        $user=ReferenceInstitute::find(Auth::id());

        ReferenceInstitute::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\ReferenceInstitutionController@index');
    }





}
