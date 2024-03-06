<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\StoreRolesRequest;
use App\Http\Requests\Admin\UpdateRolesRequest;
use Illuminate\Support\Facades\DB;
use Session;
use App\User;
use Auth;

class RolesController extends Controller
{
    /**
     * Display a listing of Role.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $title = 'GENESIS Admin : Roles List';
        $user=User::find(Auth::id());

        if(!$user->hasRole('Administrator')){
            return abort(404);
        }

        $roles = Role::all();

        return view('admin.roles.list', (['roles'=>$roles,'title'=>$title]));
    }

    /**
     * Show the form for creating new Role.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user=User::find(Auth::id());

        if(!$user->hasRole('Administrator')){
            return abort(404);
        }

        $permissions = Permission::where('parent_id',0)->get();

        foreach($permissions as $row){
            $row->child = Permission::where('parent_id',$row->id)->get();
        }
        $title = 'GENESIS Admin : Role Create';

        return view('admin.roles.create', compact('permissions','title'));
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param  \App\Http\Requests\StoreRolesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRolesRequest $request)
    {

        if (Role::where('name',$request->name)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Role already exists');
            return redirect()->action('Admin\RolesController@create')->withInput();
        }

        $role = Role::create($request->except('permission'));
        $permissions = $request->input('permission') ? $request->input('permission') : [];

        $role->givePermissionTo($permissions);
        Session::flash('message', 'Record has been created successfully');
        //return back();
        return redirect()->route('roles.index');
    }


    /**
     * Show the form for editing Role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $user=User::find(Auth::id());

        if(!$user->hasRole('Administrator')){
            return abort(404);
        }

        $permissions = Permission::get()->pluck('name', 'name');

        $role = Role::findOrFail($id);

        $role_permissions = DB::table('role_has_permissions')->select('permission_id')->where('role_id',$id)->get();
        $exist_permission = array();
        foreach($role_permissions as $i) {
            $exist_permission[] = $i->permission_id;
        }


        $permissions = Permission::where('parent_id',0)->get();

        foreach($permissions as $row){
            $row->child = Permission::where('parent_id',$row->id)->get();
        }

        $title = 'GENESIS Admin : Role Edit';
        return view('admin.roles.edit', (['permissions'=>$permissions,'role'=>$role,'exist_permission'=>$exist_permission,'title'=>$title]));
    }

    /**
     * Update Role in storage.
     *
     * @param  \App\Http\Requests\UpdateRolesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRolesRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->except('permission'));
        $permissions = $request->input('permission') ? $request->input('permission') : [];
        $role->syncPermissions($permissions);
        Session::flash('message', 'Record has been updated successfully');
        return back();
        //return redirect()->route('roles.index');
    }


    /**
     * Remove Role from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::find(Auth::id());

        if(!$user->hasRole('Administrator')){
            return abort(404);
        }

        $hasuser = DB::table('appAdmin.panelModelRoles')->where('role_id',$id)->first();

        if($hasuser){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'This role cannot be deleted. It has been assigned to some UserTripLogControlle. ');
            return redirect()->route('roles.index');
        }


        $role = Role::findOrFail($id);
        $role->delete();

        Session::flash('message', 'Record has been deleted successfully');

        return redirect()->route('roles.index');
    }

    /**
     * Delete all selected Role at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if ($request->input('ids')) {
            $entries = Role::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
