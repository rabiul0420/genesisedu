<?php

namespace App\Http\Controllers\Admin;

use App\DoctorAskReply;
use App\Http\Controllers\Controller;
use App\SmsLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SmsLogController extends Controller
{


    public function index(){
        {
            $sms_events = SmsLog::getSmsLogEvents();

            $start_date = date('Y-m-d', strtotime(SmsLog::min('created_at')));

            $end_date = date('Y-m-d', strtotime(SmsLog::max('created_at')));
            
            return view('admin.sms_log.list', compact('sms_events', 'start_date', 'end_date'));
        }

    }

    public function sms_log_ajax_list()
    {
        $sms_log_ajax_list = DB::table('sms_logs as sms_log' )
        ->leftjoin('doctors as d1', 'sms_log.doctor_id', '=','d1.id')
        ->leftjoin('users as u', 'sms_log.admin_id', '=','u.id')
        ->select(
            'sms_log.*','d1.name as doctor_name'
            ,'d1.bmdc_no as doctor_bmdc_no',
            DB::raw(' if( LENGTH(`sms_log`.`mobile_no`) > 0, `sms_log`.`mobile_no`, `d1`.`mobile_number` ) as doctor_mobile_number ')
            ,'d1.is_verified as is_verified'
            ,'u.name as admin_name'
        );

        $sms_log_ajax_list
            ->when(request()->date_from, function ($query, $date_from) {
                $query->whereDate('sms_log.created_at', '>=', $date_from);
            })
            ->when(request()->date_to, function ($query, $date_to) {
                $query->whereDate('sms_log.created_at', '<=', $date_to);
            })
            ->when(request()->sms_event, function ($query, $sms_event) {
                $query->where('sms_log.event', $sms_event);
            });

        return DataTables::of($sms_log_ajax_list)
            ->addColumn('delivery_status', function ($sms_log_ajax_list) {
                return '<div class="badge badge-success">'.  $sms_log_ajax_list->delivery_status .'</div>';
            })
            ->addColumn('created_at', function ($sms_log_ajax_list) {
                return date('d M Y h:i a',strtotime($sms_log_ajax_list->created_at));
            })
            
            ->addColumn('action', function ($sms_log_ajax_list) {
                return view('admin.sms_log.ajax_list',['sms_log_ajax_list'=>$sms_log_ajax_list]);
            })
            ->rawColumns(['action', 'delivery_status'])
            ->make(true);
    }

    public function update_status(Request $request)
    {
        $data = ['log' => [],'success' => false];
        $sms=SmsLog::find($request->id);
        if($sms){
            $sms->job_id = $request->job_id;
            if($sms->job_id){
                $sms->save();
                $data['log'] = $sms;
                $data['success'] = true;
            }
        }
       return $data;
    }
    

}
