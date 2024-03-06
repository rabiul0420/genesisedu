<?php

namespace App\Http\Controllers\Admin;

use App\DoctorsActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DoctorsActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.doctors_login_activity.index');
    }

    public function doctors_activity_list(Request $request) {
        $doctors_activity_list = DoctorsActivity::pluck('name','id')->toArray();
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $doctors_activity_list = DB::table('oauth_access_tokens as oat')
        ->join('doctors as d', 'oat.user_id', 'd.id');
        
        if($start_date && $end_date) {
            $doctors_activity_list = $doctors_activity_list->whereBetween('oat.created_at', [$start_date, $end_date]);
        }

        $doctors_activity_list->select(
            DB::raw('COUNT( remember_token ) as total_usage'),
            'oat.name as action',
            'oat.created_at as time',
            'd.id as doctor_id',
            'd.name as doctor_name',
            'd.bmdc_no as bmdc_no',
            'd.mobile_number as mobile_number',
            'd.email as email',
        );
        
        $doctors_activity_list->groupBy("oat.name", "d.name");
        $doctors_activity_list->orderBy("oat.created_at","DESC");
        

        return DataTables::of($doctors_activity_list)
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DoctorsActivity  $doctorsActivity
     * @return \Illuminate\Http\Response
     */
    public function show(DoctorsActivity $doctorsActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DoctorsActivity  $doctorsActivity
     * @return \Illuminate\Http\Response
     */
    public function edit(DoctorsActivity $doctorsActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DoctorsActivity  $doctorsActivity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DoctorsActivity $doctorsActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DoctorsActivity  $doctorsActivity
     * @return \Illuminate\Http\Response
     */
    public function destroy(DoctorsActivity $doctorsActivity)
    {
        //
    }
}
