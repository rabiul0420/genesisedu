<?php

namespace App\Http\Controllers;

use App\AppInfo;
use Dotenv\Validator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;

class AppInfoController extends Controller
{
    //AppVersionlist
    function index(){
        $datas= AppInfo::get();
        $module_name = 'App version';
        $title = 'Version List';
        $breadcrumb = explode('/',$_SERVER['REQUEST_URI']);
        // return $datas;
        return view('admin.AppVersionlist', compact('datas','module_name','breadcrumb'));
    }


    public function create()
    {

        // return 'ok';
        $data =  AppInfo::orderBy('id', 'DESC')->first();
        $module_name = 'App version';
        $title = 'Version Create';
        $breadcrumb = explode('/',$_SERVER['REQUEST_URI']);
        $submit_value = 'Submit';
        return view('admin.AppVersionlist_create',(['data'=>$data,'title'=>$title , 'breadcrumb'=>$breadcrumb,'module_name'=>$module_name,'submit_value'=>$submit_value]));
    }

    public function store(Request $request){

        // dd("OK");
        // return ($request);
        // $validator = Validator::make($request->all(), [
        //     'version' => ['required']
        // ]);
        $data = new AppInfo();
        $data->version = $request->version;
        $data->whats_new = $request->whats_new;
        $data->play_store_ref = $request->play_store_ref;
        $data->web_ref = $request->web_ref;
        $data->save();
        return redirect()->action('AppInfoController@index');

    }
    //compact('data', 'trash_category' )

    public function edit($id)
    {
        $data = AppInfo::find($id);

        $module_name = 'App version';
        $title = 'App version Edit';
        $breadcrumb = explode('/',$_SERVER['REQUEST_URI']);
        $submit_value = 'Update';

        return view('admin.AppVersionlist_edit',['data'=>$data,'title'=>$title , 'breadcrumb'=>$breadcrumb,'module_name'=>$module_name,'submit_value'=>$submit_value]);
    }

    public function update(Request $request, $id)
    {

        $data = AppInfo::find($id);
        $data->version = $request->version;
        $data->whats_new = $request->whats_new;
        $data->play_store_ref = $request->play_store_ref;
        $data->web_ref = $request->web_ref;
        $data->save();
        return redirect()->action('AppInfoController@index');



    }

    public function destroy($id)
    {
        // $institute = Institutes::find($id);
        // $institute->deleted_by=Auth::guard('doctor')->id();
        // $institute->push();
        $data= AppInfo::findOrFail($id);
        $data -> delete();
        // if( AppInfo::find( $id ) ) {
        //     AppInfo::where( 'id', $id )->update( [ 'deleted_by' => Auth::guard('doctor')->id() ]);
        //     AppInfo::where( 'id', $id )->delete( ); //1 way
        // }

        // Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('AppInfoController@index' );
    }

}
