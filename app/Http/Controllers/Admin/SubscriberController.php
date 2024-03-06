<?php

namespace App\Http\Controllers\Admin;

use App\Doctors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Doctors::query()
            ->with('subscription_orders:id,doctor_id,payment_status')
            ->subscriber()
            ->paginate(100);


        return view('tailwind.admin.subscribers.index', compact('subscribers'));
    }

    public function show(Doctors $subscriber)
    {
        return view('tailwind.admin.subscribers.show', compact('subscriber'));
    }
}
