<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\MentorTopic;
use App\QuestionChapter;
use App\QuestionSubject;
use App\QuestionTopic;
use Illuminate\Http\Request;
use App\User;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;
use App\Role;

class AdministratorController extends Controller
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
        $user=User::find(Auth::id());

      /*  if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $users = User::with('roles')->where('type',2)
                    // ->where('id', '!=' , Auth::id())
                    ->get();

        $title = 'GENESIS Admin : Administrator List';

        return view('admin.administrator.list',['users'=>$users,'title'=>$title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user=User::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $title = 'GENESIS Admin : Administrator Create';

        return view('admin.administrator.create',(['title'=>$title]));
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
            'email' => ['required'],
            'status' => ['required'],
            'password' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data!!!");
            return redirect()->action('Admin\AdministratorController@create')->withInput();
        }



        if (User::where('email',$request->email)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This email  already exists');
            return redirect()->action('Admin\AdministratorController@create')->withInput();
        }

        else{
            $allData=$request->all();
            $allData['type']=2;
            $allData['password']=bcrypt($request->password);

            $user=User::create($allData);


            Session::flash('message', 'Record has been added successfully');

            //return back();

            return redirect()->action('Admin\AdministratorController@index');
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
        $user = User::with('roles', 'logs')->findOrFail($id);

        return view('admin.administrator.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find( $id );

        $roles = Role::get()->pluck('name', 'name');

        $title = 'GENESIS Admin : Administrator Edit';

        return view('admin.administrator.edit', compact('user', 'title', 'roles'));

        return view('admin.administrator.edit',
            compact( [ 'user', 'question_topics','question_subjects','question_chapters', 'title', 'roles', 'selected_topics', 'selected_subjects', 'selected_chapters' ] ) );
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
            'email' => ['required'],
            'name' => ['required'],
            'status' => ['required'],
            'phone_number' => [
                'required','string', 'min:1', 'regex:/^[0-9]*$/i'
            ]
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid data!!!");
            return back()->withInput();
        }

        $user=User::find($id);

        if($request->email != $user->email){
            if (User::where('email',$request->email)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This email already exists');
                return redirect()->back()->withInput();
            }
        }

        if($request->phone_prefix.$request->phone_number != $user->phone_number){
            if (User::where('phone_number',$request->phone_prefix.$request->phone_number)->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This phone number already exists');
                return redirect()->back()->withInput();
            }
        }

        $user->email=$request->email;
        $user->name=$request->name;
        $user->phone_number = $request->phone_prefix.$request->phone_number;
        $user->status=$request->status;

        if ( $request->password ) {
            $user->password = bcrypt($request->password);
            $user->security=$request->password;
        }

        $user->push();
        $roles = $request->input('roles') ? $request->input('roles') : [];
        $user->syncRoles($roles);

        Session::flash('message', 'Record has been updated successfully');

        return back();

    }


    function updateMentorTopic( $data, &$mentorInsertData = [] ){

        $mentor = MentorTopic::withTrashed()->where( $data );

        if( $mentor->exists() ) {
            $mentor = $mentor->first( );
            $mentor->deleted_at = NULL;
            $mentor->deleted_by = NULL;
            $mentor->push();
        } else {
            $mentorInsertData[] = $data;
        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::find(Auth::id());

       /* if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        User::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\AdministratorController@index');
    }





}
