<?php

namespace App\Http\Controllers\Admin;

use App\ComplainRelated;
use App\CourseComplainType;
use App\Courses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use App\User;
use App\UserComplainAssign;
use Illuminate\Support\Facades\DB;
use Validator;
use Session;

class UserComplainAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_complain_assigns = UserComplainAssign::with('user')->where('status', '1')->get();
        $courseComplainIds = [];
        $user_complain_assigns->each(function ($assign) use (&$courseComplainIds) {
            $ids = json_decode($assign->course_complain_type_id, true);
            $courseComplainIds = is_array($ids) ? array_merge($courseComplainIds, $ids) : $ids;
        });

        $course_complain_types = CourseComplainType::with('complain_type', 'course')->whereIn('id', $courseComplainIds)->get();
        return view('admin.user_complain_assign.list', compact('user_complain_assigns', 'course_complain_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $executive_role = Setting::where('name','executive_role_id')->first();
        $users = DB::table('users')
            ->join('model_has_roles','model_has_roles.model_id','users.id')
            ->where('model_has_roles.role_id',$executive_role->value)
            ->select('users.*')
            ->get();

        $course_complains = CourseComplainType::with('course', 'complain_type')->get();
        // return  $course_complains;
        return view('admin.user_complain_assign.create', compact('users', 'course_complains','executive_role'));
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
            'user_id' => ['required'],
            'status' => ['required'],
            'course_complain_type_ids' => ['required'],
        ]);

        if ($validator->fails()) {
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\UserComplainAssignController@create')->withInput();
        }

        $course_complain_types = [];

        $course_complain_type_ids =  $request->course_complain_type_ids;

        foreach ($course_complain_type_ids as  $k => $course_complain_type_id) {
            if ($course_complain_type_id != null) {
                $course_complain_types[] = $course_complain_type_ids[$k];
            }
        }
        $user_complain_assign = new UserComplainAssign();
        $user_complain_assign->user_id = $request->user_id;
        $user_complain_assign->course_complain_type_id = json_encode($course_complain_types);
        $user_complain_assign->status = $request->status;
        $user_complain_assign->save();


        Session::flash('class', 'alert-success');
        session()->flash('message', 'User Course Complain Assign Successfully');
        return redirect()->action('Admin\UserComplainAssignController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user_complain_assign = UserComplainAssign::with('user')->find($id);
        $course_complain_type_ids = json_decode($user_complain_assign->course_complain_type_id);
        $course_complain_types = CourseComplainType::with('complain_type', 'course')->whereIn('id', $course_complain_type_ids )->pluck('id')->toArray();
        $course_complain_types_all = CourseComplainType::with('complain_type', 'course')->get();

        return view('admin.user_complain_assign.edit', compact('user_complain_assign', 'course_complain_types_all','course_complain_types'));
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
            'user_id' => ['required'],
            'status' => ['required'],
            'course_complain_type_ids' => ['required'],
        ]);

        if ($validator->fails()) {
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\UserComplainAssignController@create')->withInput();
        }

        $course_complain_types = [];

        $course_complain_type_ids =  $request->course_complain_type_ids;

        foreach ($course_complain_type_ids as  $k => $course_complain_type_id) {
            if ($course_complain_type_id != null) {
                $course_complain_types[] = $course_complain_type_ids[$k];
            }
        }

        $user_complain_assign = UserComplainAssign::find($id);
        $user_complain_assign->course_complain_type_id = []; 
        $user_complain_assign->user_id = $request->user_id;
        $user_complain_assign->course_complain_type_id = json_encode($course_complain_types);
        $user_complain_assign->status = $request->status;
        $user_complain_assign->push();


        Session::flash('class', 'alert-success');
        session()->flash('message', 'User Course Complain Assign Successfully');
        return redirect()->action('Admin\UserComplainAssignController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
