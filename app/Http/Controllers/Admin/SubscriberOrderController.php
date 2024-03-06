<?php

namespace App\Http\Controllers\Admin;

use App\Doctors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SubscriptionOrder;
use App\SubscriptionOrderPayment;
use Carbon\Carbon;

class SubscriberOrderController extends Controller
{
    public function index(Doctors $subscriber)
    {
        $subscriber->load([
            'subscription_orders'
        ]);

        return view('tailwind.admin.subscribers.orders.index', compact('subscriber'));
    }

    public function show(Doctors $subscriber, SubscriptionOrder $order)
    {
        $order->load([
            'doctor',
            'subscriptions.subscriptionable',
            'payments',
            'manual_payments',
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

        return view('tailwind.admin.subscribers.orders.show', compact('subscriber', 'order'));
    }
}
