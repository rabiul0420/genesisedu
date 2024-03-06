<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Branches;
use Session;
use Validator;


class BranchesController extends Controller
{  
    public function index()
    {
        $branches = Branches::get();       
       return view('admin.branches.list',['branches'=>  $branches]);   
    }
    public function create()
    {  
       return view('admin.branches.create');  
    }
    public function store(Request $request)
    {
        
         $validator = Validator::make($request->all(), [
             'name' => ['required'],
             'status' => ['required']
         ]);

         if ($validator->fails()){
             Session::flash('class', 'alert-danger');
             return redirect()->action('Admin\BranchesController@index')->withInput();
         }

         if (branches::where(['name'=>$request->name])->exists()){
            Sessieon::flash('class', 'alert-danger');
            session()->flash('message','This Name already exists');
            return redirect()->action('Admin\BranchesController@index')->withInput();
        }
        $branches = new Branches();
        $branches->name = $request->name;
        $branches->status = $request->status;
        $branches->save();
        return redirect()->action('Admin\BranchesController@index');
    }
    public function show($id)
    {
       
    }
    public function edit($id)
    {  
        $data['branches'] = Branches::find($id);
        return view('admin.branches.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $branches =  Branches::find($id);
        $branches->name = $request->name;
        $branches->status = $request->status;
        $branches->push();
        return redirect()->action('Admin\BranchesController@index');
    }
    public function destroy($id)
    {
        Branches::destroy($id); 
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\BranchesController@index');
    }
}
 