<?php

namespace App\Http\Controllers\ManualPayment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SubscriptionOrder;
use Illuminate\Support\Facades\Auth;

class SubscriptionOrderController extends Controller
{
    public function create(SubscriptionOrder $subscription_order, $amount = null)
    {
        if(is_null($amount)) {
            $amount = $subscription_order->payable_amount ?? 0;
        }

        $doctor = Auth::guard('doctor')->user();

        $subscription_order->load('manual_payments');

        $request_amount = $subscription_order->manual_payments()->sum('amount') ?? 0;

        // $amount = $amount > $request_amount
        //     ?  $amount - $request_amount
        //     : 0;

        return view('manual-payments.subscription_order', compact('subscription_order', 'amount', 'doctor'));
    }

    public function store(Request $request, SubscriptionOrder $subscription_order, $amount = null)
    {
        // return
        $fields = $request->validate([
            'trans_id'      => 'required|string',
            'account_no'    => 'required|string',
        ]);
        
        if(is_null($amount)) {
            $amount = $subscription_order->payable_amount ?? 0;
        }

        $doctor = Auth::guard('doctor')->user();

        $subscription_order->manual_payments()->updateOrCreate(
            [
                'doctor_id'     => $doctor->id,
            ],
            [
                'trx_id'        => $fields['trans_id'],
                'amount'        => $amount,
                'account_no'    => $fields['account_no'],
                'deleted_at'    => null,
            ]
        );

        return back();
    }
}
