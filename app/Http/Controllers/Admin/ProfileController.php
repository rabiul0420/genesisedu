<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\User;
use Session;
use Auth;


class ProfileController extends Controller
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user=User::find(Auth::id());
        return view('admin.profile.show',['user'=>$user]);
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
        return view('admin.profile.edit',['user'=>$user]);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile_edit()
    {
        $user=User::find(Auth::id());
        return view('admin.profile.profile_edit',['user'=>$user]);
    }


    public function profile_update(Request $request)
    {
        if($request->email != Auth::user()->email){
            if (User::where('email',$request->email)->exists()){
                session()->flash('error','* User Already Exist');
                Session::flash('message', 'This Email Already Exist');
                return redirect()->back()->withInput();
            }
        }

        $id = Auth::id();
        $user=User::find($id);
        $user->email=$request->email;
        $user->two_factor=$request->two_factor;
        $user->phone_number=$request->phone_number;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->push();
        Session::flash('message', 'Profile has been updated successfully');

        return back();
        // return redirect()->to('admin/profile/'.$id);


    }














}
