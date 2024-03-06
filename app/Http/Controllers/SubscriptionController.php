<?php

namespace App\Http\Controllers;

use App\Courses;
use App\Doctors;
use App\Faculty;
use App\Sessions;
use App\Subjects;
use App\SubscriptionOrder;
use App\SubscriptionOrderPayment;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $status = $this->checkSubscriberAbility();

        $token = $this->getDoctorAccessToken();

        $this->checkPaymentUnpaidOrder();

        return view('subscriptions.index', compact('status', 'token'));
    }

    public function groupsShow($group_id)
    {
        $token = $this->getDoctorAccessToken();

        return view('subscriptions.video', compact('group_id', 'token'));
    }

    public function addSubscription()
    {
        $token = $this->getDoctorAccessToken();

        return view('subscriptions.add-subscription', compact('token'));
    }

    public function addSubscriptionNext(Request $request)
    {
        $year = $request->year;
        $course_id = $request->course_id;
        $session_id = $request->session_id;
        $faculty_id = $request->faculty_id;
        $discipline_id = $request->discipline_id;

        session()->forget("items_{$year}_{$course_id}_{$session_id}_{$faculty_id}_{$discipline_id}");
        session()->forget("total_{$year}_{$course_id}_{$session_id}_{$faculty_id}_{$discipline_id}");

        return redirect()->route('my.subscriptions.available', [
            $year,
            $course_id,
            $session_id,
            'faculty_id' => $faculty_id,
            'discipline_id' => $discipline_id,
        ]);
    }

    public function available($year, $course_id, $session_id)
    {
        $request = request();

        $token = $this->getDoctorAccessToken();

        $course = Courses::where('id', $course_id)->first(['id', 'name']);
        $session = Sessions::where('id', $session_id)->first(['id', 'name']);

        $faculty_id = $request->faculty_id ?? '';
        $discipline_id = $request->discipline_id ?? '';

        $faculty = Faculty::where('id', $faculty_id)->first(['id', 'name']);
        $discipline = Subjects::where('id', $discipline_id)->first(['id', 'name']);

        $items = (array) session()->get("items_{$year}_{$course_id}_{$session_id}_{$faculty_id}_{$discipline_id}");
        $items_count = count($items) ?? 0;
        $total_amount = (double) session()->get("total_{$year}_{$course_id}_{$session_id}_{$faculty_id}_{$discipline_id}");

        return view('subscriptions.available', compact('token', 'year', 'course', 'session', 'faculty', 'discipline', 'items', 'items_count', 'total_amount'));
    }

    public function availableNext(Request $request, $year, $course_id, $session_id)
    {
        $faculty_id = $request->faculty_id ?? '';
        $discipline_id = $request->discipline_id ?? '';

        // return $request;

        session()->put("items_{$year}_{$course_id}_{$session_id}_{$faculty_id}_{$discipline_id}", $request->items);
        session()->put("total_{$year}_{$course_id}_{$session_id}_{$faculty_id}_{$discipline_id}", $request->total);

        return redirect()
            ->route('my.subscriptions.order-confirm', [
                $year,
                $course_id,
                $session_id,
                'faculty_id' => $request->faculty_id,
                'discipline_id' => $request->discipline_id,
            ]);
    }

    public function orderConfirm($year, $course_id, $session_id)
    {
        $request = request();

        $faculty_id = $request->faculty_id ?? '';
        $discipline_id = $request->discipline_id ?? '';

        $faculty = Faculty::where('id', $faculty_id)->first(['id', 'name']);
        $discipline = Subjects::where('id', $discipline_id)->first(['id', 'name']);

        $items = (array) session()->get("items_{$year}_{$course_id}_{$session_id}_{$faculty_id}_{$discipline_id}");
        $total_price = (double) session()->get("total_{$year}_{$course_id}_{$session_id}_{$faculty_id}_{$discipline_id}");

        $items_count = count($items);

        $token = $this->getDoctorAccessToken();

        $course = Courses::where('id', $course_id)->first(['id', 'name']);
        $session = Sessions::where('id', $session_id)->first(['id', 'name']);

        return view('subscriptions.order-confirm', compact('token', 'year', 'course', 'session', 'faculty', 'discipline', 'items', 'items_count', 'total_price'));
    }

    public function orderIndex()
    {
        $token = $this->getDoctorAccessToken();

        $this->checkPaymentUnpaidOrder();

        return view('subscriptions.orders.index', compact('token'));
    }

    public function orderShow($order_id)
    {
        $token = $this->getDoctorAccessToken();

        $subscription_payment_link = $this->getSubscriptionPaymentLink();

        return view('subscriptions.orders.show', compact('token', 'order_id', 'subscription_payment_link'));
    }

    public function orderPayment(SubscriptionOrder $order)
    {
        $subscription_order_id = $order->id;
        $total = $order->payable_amount;     
        $link = 'https://banglamedexam.com/sif-subscription-payment/'.$subscription_order_id.'/'.$total;
        return redirect($link);

    }

    protected function checkPaymentUnpaidOrder()
    {
        $orders = SubscriptionOrder::query()
            ->where([
                'doctor_id'         => Auth::guard('doctor')->id(),
                'payment_status'    => 0,
            ])
            ->get();

        foreach($orders as $order) {
            $total_amount = SubscriptionOrderPayment::query()
                ->where('subscription_order_id', $order->id)
                ->sum('amount');

            if($order->payable_amount <= $total_amount && !$order->payment_status) {
                $order->update([
                    'payment_status'    => 1,
                    'expired_at'        => Carbon::now()->addDay($order->duration_in_day),
                ]);
            }
        }
    }

}
