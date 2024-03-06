<?php

namespace App\Http\Controllers\Admin;

use App\Doctors;
use App\Exports\SubscriptionPaymentExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SubscriptionOrder;
use App\SubscriptionOrderPayment;
use Maatwebsite\Excel\Facades\Excel;

class SubscriptionController extends Controller
{
    public function index()
    {
        if(!request()->isXmlHttpRequest()) {
            return view('tailwind.admin.subscriptions.index');
        }
    
        $orders = SubscriptionOrder::query();

        $this->search($orders);
        $this->filter($orders);

        $orders = $orders->latest()->paginate();

        $payments = SubscriptionOrderPayment::query()
            ->whereHas('subscription_order', function ($query) {
                $this->search($query);
                $this->filter($query);
                return $query;
            });

        $total_amount = $payments->sum('amount') ?? 0;
        $total_amount_by_app = $payments->where('trans_id', 'like', "SIF_SUBSCRIPTION_%")->sum('amount') ?? 0;

        return view('tailwind.admin.subscriptions.data', compact('orders', 'total_amount', 'total_amount_by_app'))->render();
    }

    public function download(Request $request, $type = 'excel')
    {
        // return
        $doctors = Doctors::query()
            ->with([
                'subscription_orders:id,doctor_id',
                'subscription_orders.payments:id,subscription_order_id,amount',
            ])
            ->has('subscription_orders.payments')
            ->get([
                'id',
                'name',
                'bmdc_no',
                'mobile_number'
            ]);

        $array = [];

        foreach($doctors as $doctor) {
            $total = 0;

            foreach($doctor->subscription_orders as $subscription_order) {
                $total += $subscription_order->payments->sum('amount');
            }

            $array[] = [
                'bmdc'  => $doctor->bmdc_no,
                'name'  => $doctor->name,
                'phone' => $doctor->phone,
                'order' => $doctor->subscription_orders->count(),
                'total' => $total,
            ];
        }

        return Excel::download(new SubscriptionPaymentExport($array), "subscription_payments_{$request->from}_{$request->to}.xlsx");
    }

    protected function search(&$query)
    {
        $query
            ->when(request('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query
                        ->where('id', 'like', "%{$search}%")
                        ->orWhereHas('doctor', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('mobile_number', 'like', "%{$search}%")
                                ->orWhere('bmdc_no', 'like', "%{$search}%");
                        });
                });
            });
    }

    protected function filter(&$query)
    {
        $query
            ->when(isset(request()->payment_status), function ($query) {
                return $query->where('payment_status', request('payment_status'));
            })
            ->when(isset(request()->from), function ($query) {
                return $query->whereDate('updated_at', '>=', request('from'));
            })
            ->when(isset(request()->to), function ($query) {
                return $query->whereDate('updated_at', '<=', request('to'));
            });
    }
}
