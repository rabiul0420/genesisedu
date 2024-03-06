<?php

namespace App\Http\Controllers;

use App\Batches;
use App\Discount;
use App\DoctorsCourses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Promo_Code;
use Illuminate\Support\Facades\Auth;

class CodeController extends Controller
{
    //
        // use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function apply_discount_code(Request $request)
    {
        return $request->discount_code;
        $d = Discount::where([ 'discount_code'=> $request->discount_code, 'batch_id' => $request->batch_id, 'doctor_id' => Auth::guard('doctor')->id() ] );
// dd( $d->toSql() ,  $request->discount_code ,  $request->batch_id, Auth::guard('doctor')->id() );
        if( $d->exists( ) ) {
            
            $doctorCourse = DoctorsCourses::where( [ 'batch_id' => $request->batch_id, 'doctor_id' => Auth::guard('doctor')->id() ])->get( );

            // dd( $doctorCourse );


        }

        // return response( [ 'a' => $request->batch_id, 'coupon_code' => $request->coupon_code ] );
    }



}




