<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Institutes;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;
// use SoftDeletes;


class InstitutesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $institutes = Institutes::get();
        $module_name = 'Institute';
        $title = 'Institutes List';
        $breadcrumb = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.settings.institute_list',['institutes'=>$institutes,'title'=>$title , 'breadcrumb'=>$breadcrumb, 'module_name'=>$module_name]);
    }

    public function create()
    {
        $module_name = 'Institute';
        $title = 'Institute Create';
        $breadcrumb = explode('/',$_SERVER['REQUEST_URI']);
        $submit_value = 'Submit';
        return view('admin.settings.institute_create',(['title'=>$title , 'breadcrumb'=>$breadcrumb,'module_name'=>$module_name,'submit_value'=>$submit_value]));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'type' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\InstitutesController@create')->withInput();
        }

        if (Institutes::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Name already exists');
            return redirect()->action('Admin\InstitutesController@create')->withInput();
        }

        if (Institutes::where(['reference_code'=>$request->reference_code])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Reference Code  already exists');
            return redirect()->action('Admin\InstitutesController@create')->withInput();
        }

        $institute = new Institutes();
        $institute->name = $request->name;
        $institute->type = $request->type;
        $institute->reference_code = $request->reference_code;
        $institute->status = $request->status;

        $institute->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\InstitutesController@index');

    }

    public function show($id)
    {
        $institute=Institutes::select('settings.institutes.*')
            ->find($id);
        return view('admin.settings.institutes.show',['institute'=>$institute]);
    }

    public function edit($id)
    {
        $institute = Institutes::find($id);

        $module_name = 'Institute';
        $title = 'Institute Edit';
        $breadcrumb = explode('/',$_SERVER['REQUEST_URI']);
        $submit_value = 'Update';

        return view('admin.settings.institute_edit',['institute'=>$institute,'title'=>$title , 'breadcrumb'=>$breadcrumb,'module_name'=>$module_name,'submit_value'=>$submit_value]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'type' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\InstitutesController@edit',[$id])->withInput();
        }

        $institute = Institutes::find($id);

        $institute->name = $request->name;
        $institute->type = $request->type;
        $institute->reference_code = $request->reference_code;
        $institute->status = $request->status;

        $institute->push();

        Session::flash('message', 'Record has been updated successfully');

        return back();

    }

    public function destroy($id)
    {
        // $institute = Institutes::find($id);
        // $institute->deleted_by=Auth::id();
        // $institute->push();

        if( Institutes::find( $id ) ) {
            Institutes::where( 'id', $id )->update( [ 'deleted_by' => Auth::id() ]);
            Institutes::where( 'id', $id )->delete( ); //1 way
        }

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\InstitutesController@index');
    }

}
