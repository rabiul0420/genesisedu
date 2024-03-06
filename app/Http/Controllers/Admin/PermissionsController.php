<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\StorePermissionsRequest;
use App\Http\Requests\Admin\UpdatePermissionsRequest;
use Session;

class PermissionsController extends Controller
{
    /**
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();

        foreach($permissions as $row){
            $row->parent = Permission::where('id',$row->parent_id)->value('name');
        }

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating new Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $parent = Permission::where('parent_id',0)->orderBy('id', 'DESC')->pluck('name', 'id');
        return view('admin.permissions.create',['parent'=>$parent]);
    }

    /**
     * Store a newly created Permission in storage.
     *
     * @param  \App\Http\Requests\StorePermissionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePermissionsRequest $request)
    {

        Permission::create($request->all());
        
        Session::flash('message', 'Record created successfully');

        return back();

        //return redirect()->action('Admin\PermissionsController@index');
    }


    /**
     * Show the form for editing Permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $permission = Permission::findOrFail($id);

        $parent = Permission::where('parent_id',0)->orderBy('id', 'DESC')->pluck('name', 'id');

        return view('admin.permissions.edit', (['permission'=>$permission,'parent'=>$parent]));
    }

    /**
     * Update Permission in storage.
     *
     * @param  \App\Http\Requests\UpdatePermissionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePermissionsRequest $request, $id)
    {

        $permission = Permission::findOrFail($id);
        $permission->update($request->all());

        Session::flash('message', 'Record has been updated successfully');

        return back();
        //return redirect()->action('Admin\PermissionsController@index');

    }


    /**
     * Remove Permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index');
    }

    /**
     * Delete all selected Permission at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

        if ($request->input('ids')) {
            $entries = Permission::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
