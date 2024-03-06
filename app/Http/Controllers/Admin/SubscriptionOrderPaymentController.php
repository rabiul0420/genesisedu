<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SubscriptionOrder;
use App\SubscriptionOrderPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SubscriptionOrderPaymentController extends Controller
{
    public function store(Request $request, SubscriptionOrder $order)
    {
        $request->validate([
            'trans_id'  => 'required',
            'amount'    => 'required|numeric',
            'note_box'  => '',
        ]);

        SubscriptionOrderPayment::create([
            'subscription_order_id' => $order->id,
            'trans_id'              => $request->trans_id,
            'amount'                => $request->amount,
            'note_box'              => $request->note_box,
            'verified_by'           => Auth::id(),
        ]);

        $total_amount = SubscriptionOrderPayment::query()
            ->where('subscription_order_id', $order->id)
            ->sum('amount');

        if($order->payable_amount <= $total_amount && !$order->payment_status) {
            $order->update([
                'payment_status'    => 1,
                'expired_at'        => Carbon::now()->addDay($order->duration_in_day),
            ]);
        }

        return back();
    }
}
