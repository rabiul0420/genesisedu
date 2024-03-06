<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Branches;
use App\Location;
use Session;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;


class LocationController extends Controller
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
        $data['branches'] = Branches::where('status','1')->get();
        $data['module_name'] = 'Location';
        $data['title'] = 'Location List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);

        return view('admin.location.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function location_list(Request $request)
    {        
        $location_list = DB::table('location as d1')->whereNull('d1.deleted_at')->join('branches as d2','d1.branch_id','d2.id');
        $location_list = $location_list->select('d1.*','d2.name as branch_name');
                
        return Datatables::of($location_list)
            ->editColumn('status', function ($location_list) {
                return $location_list->status == '1' ? 'active' : 'inactive'; // human readable format
            })
            ->addColumn('action', function ($location_list) {
                $data['location_list'] = $location_list;               
                return view('admin.location.location_ajax_list', $data);
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // $user=Subjects::find(Auth::id());
        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');
        
        $data['module_name'] = 'Location';
        $data['title'] = 'Location Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.location.create',$data);
        //echo "Location create";
    }

    public function validate_request($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required'],
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = $this->validate_request($request);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\LocationController@create')->withInput();
        }        

        if (Location::where(['name'=>$request->name])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Location name already exists');
            return redirect()->action('Admin\LocationController@create')->withInput();
        }
        else
        {

            $location = new Location();
            $location->name = $request->name;
            $location->branch_id = $request->branch_id;
            $location->address = $request->address;
            $location->status = $request->status;
            $location->created_by = Auth::id();
            $location->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\LocationController@index');
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
        $data['location']=Location::find($id);
        return view('admin.location.show',$data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['location'] = Location::find($id);
        $data['branches'] = Branches::where(['status'=>'1'])->pluck('name','id');

        $data['module_name'] = 'Location';
        $data['title'] = 'Location Edit';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.location.edit', $data);
        
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
        $validator = $this->validate_request($request);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $location = Location::find($id);

        if($location->name != $request->name) {

            if (Location::where(['name'=>$request->name])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Location name already exists');
                return redirect()->action('Admin\LocationController@edit',[$id])->withInput();
            }

        }

        
        $location->name = $request->name;
        $location->branch_id = $request->branch_id;
        $location->address = $request->address;
        $location->status = $request->status;
        $location->updated_by=Auth::id();
        $location->push();

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
        /*$user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        Location::destroy($id); // 1 way

        Room::where(['location_id'=>$id])->update(['deleted_by'=>Auth::id()]); // 1 way
        Room::where(['location_id'=>$id])->delete(); 
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\LocationController@index');
    }


    
}  