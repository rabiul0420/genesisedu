<?php

namespace App\Http\Controllers\Admin;
use App\ChainCode;
use App\hotelProperties;
use App\Http\Controllers\Controller;

use App\UserHotelChainPreferences;
use App\UserHotelPreferences;
use Illuminate\Http\Request;

use App\User;
use App\Organization;
use App\UserOrganization;
use App\TravelQuestion;
use App\TravelUserQAnswer;
use App\UserAirRatting;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Session;
use Auth;
use View;
use Yajra\Datatables\Datatables;

use DB;


class UsersController extends Controller
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
        if (! Gate::allows('Individual')) {
            return abort(404);
        }

        $title = 'GENESIS Admin : Individual List';


        $data = new DB;
        $data->User = (new User())->getTable();
        $data->Organization = (new Organization())->getTable();

        $data->appOrganization = 'appOrganization';
        $data->appUser = 'appUser';


        return view('admin.users.list',['title'=>$title,'data'=>$data]);
    }

    public function metabase_users_list()
    {

        $data['title'] = 'GENESIS Admin : Metabase Dashboard';

        $metabaseSiteUrl = 'https://metabase.tempore.com';
        $metabaseSecretKey = '3fad1fd13d030b99d84c01278f2faf2c346f228ff8391cd5407d9edacc07da09';

        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $token = (new \Lcobucci\JWT\Builder())
            ->set('resource', ['dashboard' => 2])
            ->set('params', ['id' => 692])
            ->sign($signer, $metabaseSecretKey)
            ->getToken();

        $data['iframeUrl'] = "$metabaseSiteUrl/embed/dashboard/$token#bordered=true&titled=true";


        return view('admin.users.metabase_users_list',$data);
    }


    public function metabase_users_list_q()
    {

        $data['title'] = 'GENESIS Admin : Individual List';

        $metabaseSiteUrl = 'https://metabase.tempore.com';
        $metabaseSecretKey = '3fad1fd13d030b99d84c01278f2faf2c346f228ff8391cd5407d9edacc07da09';

        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $token = (new \Lcobucci\JWT\Builder())
            ->set('resource', ['question' => 7])
            ->set('params', '')
            ->sign($signer, $metabaseSecretKey)
            ->getToken();

        $data['iframeUrl'] = "$metabaseSiteUrl/embed/question/$token#bordered=true&titled=true";


        return view('admin.users.metabase_users_list_q',$data);
    }








    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function users_list()
    {

        $UserOrganization = (new UserOrganization())->getTable();
        $Organization = (new Organization())->getTable();
        $User = (new User())->getTable();

        $appOrganization = 'appOrganization';
        $appUser = 'appUser';


        /* $users_list = User::where('type',1)
             ->select($User.'.*')
             ->leftjoin($UserOrganization, $User.'.id', '=', $UserOrganization.'.user_id')
             ->leftjoin($Organization, $Organization.'.id', '=', $UserOrganization.'.org_id');*/

        $users_list = DB::table($appUser.'.'.$User.' as d1')
            ->leftjoin($appUser.'.'.$UserOrganization.' as d2', 'd1.id', '=','d2.user_id')
            ->leftjoin($appOrganization.'.'.$Organization.' as d3', 'd3.id', '=', 'd2.org_id')
            ->where('d1.type',1)
            ->select('d1.*','d3.name as organization');


        return Datatables::of($users_list)
            ->addColumn('action', function ($users_list) {
                return view('admin.users.ajax_list',(['users_list'=>$users_list]));
            })

            ->addColumn('status', function ($users_list) {
                return ($users_list->status==1)?'Active':'InActive' ;
            })

            ->make(true);
    }

    public function organization_users($id)
    {
        $organization_users = UserOrganization::where('org_id',$id)
            ->get();

        $title = 'GENESIS Admin : Users of Organization';

        return view('admin.users.organization_users',['organization_users'=>$organization_users,'title'=>$title]);
    }





    public function create()
    {
        $roles = Role::get()->pluck('name', 'name');

        $title = 'GENESIS Admin : Individual Create';

        return view('admin.users.create',(['roles'=>$roles,'title'=>$title]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (User::where('email',$request->email)->exists()){
            session()->flash('error','* User Already Exist');
            return redirect()->action('Admin\UsersController@create')->withInput();
        }

        else{
            $allData=$request->all();
            $allData['password']=bcrypt($request->password);

            //$request->password

            $user=User::create($allData);

            $roles = $request->input('roles') ? $request->input('roles') : [];
            $user->assignRole($roles);


            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\UsersController@index');
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

        $data['user']=User::find($id);

        if (! Gate::allows('Individual View')) {
            return abort(404);
        }

        $data['travel_question'] = TravelQuestion::where('status',1)->where('dependency_id',0)->get();
        $data['user_travel_answer'] = TravelUserQAnswer::select('answer_id','a_value','user_id')->where('user_id',$id)->get();
        $exist_user_travel_answer = array();
        foreach($data['user_travel_answer'] as $i) {
            $exist_user_travel_answer[] = $i->answer_id;
        }
        $data['exist_user_travel_answer'] = $exist_user_travel_answer;
        //dd($exist_user_travel_answer_dependency);

        $userData = User::find($id);

        $userailneprogram = array();

        $userailneprogramrating = array();
        foreach ($userData->userairlineloyaltyprogramsRatting as $u) {
            $userailneprogram[] = $u->alp_id;
            $userailneprogramrating[] = $u->ratting;
        }

        $data['userailneprogram'] = $userailneprogram;

        $data['userailneprogramrating'] = $userailneprogramrating;

        $data['airLprglist'] = $this->userinfoService->getAirLoyaltyProgram($withimageurl=1);

        $userairrating = UserAirRatting::where('user_id',$id)->get();

        $user_question = array();
        $travel_user_question_count =  TravelUserQAnswer::where('user_id', $id)->count();
        if($travel_user_question_count){
            $travel_user_question = TravelUserQAnswer::where('user_id', $id)->get();
            $data['travel_user_question'] = $travel_user_question;
            foreach ($travel_user_question as $key) {
                $user_question[] = $key->answer_id;
            }
        }

        $data['user_question'] = $user_question;

        $data['hotelLchainCodelist'] = [];

        $hotelProperties = (new hotelProperties())->getTable();
        $UserHotelPreferences = (new UserHotelPreferences())->getTable();
        $ChainCode = (new ChainCode())->getTable();
        $UserHotelChainPreferences = (new UserHotelChainPreferences())->getTable();


        $appLookup = 'appLookup';
        $ppTravelerPreferences = 'appTravelerPreferences';

        $data['user_cities'] = DB::table($appLookup.'.'.$hotelProperties.' as d1')
            ->join($ppTravelerPreferences.'.'.$UserHotelPreferences.' as d2', 'd1.HotelCode', '=','d2.HotelCode')
            ->distinct()
            ->where('user_id',$id)
            ->get(['ZipCode','City','State']);
        $data['user_hotels'] = DB::table($appLookup.'.'.$hotelProperties.' as d1')
            ->join($ppTravelerPreferences.'.'.$UserHotelPreferences.' as d2', 'd1.HotelCode', '=','d2.HotelCode')
            ->where('user_id',$id)
            ->select('d1.City as City','d1.HotelName as HotelName','d2.rating as rating','d2.HotelCode as HotelCode')
            ->get();

        $data['userHotelChainPreferences'] = DB::table($appLookup.'.'.$ChainCode.' as d1')
            ->join($ppTravelerPreferences.'.'.$UserHotelChainPreferences.' as d2', 'd1.ChainCode', '=','d2.ChainCode')
            ->where('user_id',$id)
            ->select('d1.ChainName as ChainName','d1.ChainCode as ChainCode','d2.rating as rating')
            ->get();

        $data['title'] = 'GENESIS Admin : Individual Detail';


        return view('admin.users.show',$data);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user=User::find($id);
        $organizations = Organization::pluck('name','id');


        return view('admin.users.edit',['user'=>$user,'organizations'=>$organizations]);
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
        $user=User::find($id);
        $user->email=$request->email;
        $user->phone_number=$request->phone_number;
        $user->status=$request->status;
        if($request->status){
            $user->errorCount=0;
            $user->denyCount=0;
            $user->login_attempt=0;
        }
        if($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->push();

        if(UserOrganization::where('user_id',$id)->exists()){
            $userorganization = UserOrganization::where('user_id',$id)->first();
        }else{
            $userorganization =  new UserOrganization;
            $userorganization->user_id = $id;
        }
        $userorganization->org_id = $request->org_id;
        $userorganization->save();


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
        if (! Gate::allows('Individual Delete')) {
            return abort(404);
        }
        User::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\UsersController@index');
    }













}