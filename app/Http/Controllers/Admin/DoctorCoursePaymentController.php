<?php

namespace App\Http\Controllers\Admin;

use App\BatchPaymentOption;
use App\DoctorCoursePaymentOption;
use App\DoctorsCourses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DoctorCoursePaymentController extends Controller
{
    public function option($doctor_course_id)
    {
        $doctor_course = DoctorsCourses::query()
            ->with([
                'batch',
            ])
            ->findOrFail($doctor_course_id, [
                '*'
            ]);

        $course_payable_total = $doctor_course->course_price;
        $doctor_paid_total = $doctor_course->paid_amount();

        $doctor_paid_percent = round(($doctor_paid_total / $course_payable_total) * 100, 2);

        if($doctor_course->batch->payment_times < 2)
        {
            return redirect()
                ->route('doctors-courses.show', $doctor_course->id)
                ->with('message', 'Batch payment times not allow');
        }

        return view('admin.doctors_courses.payments.option', compact('doctor_course', 'doctor_paid_percent'));
    }

    public function optionStore(DoctorsCourses $doctor_course, Request $request)
    {
        if($doctor_course->payment_option != $request->payment_option || $doctor_course->payment_times != $request->payment_times)
        {
            if($request->payment_option == 'default')
            {
                $doctor_course->payment_option = $request->payment_option;
                $doctor_course->payment_times = "";
                $doctor_course->payment_count = "";
            }
            else if($request->payment_option == 'custom')
            {
                $doctor_course->payment_option = $request->payment_option;
                $doctor_course->payment_times = $request->payment_times;
                $doctor_course->payment_count = '';

                $payment_options = DoctorCoursePaymentOption::query()
                    ->where('doctor_course_id', $doctor_course->id)
                    ->get();

                if(!$payment_options->count()) {
                    $payment_options = BatchPaymentOption::query()
                        ->where('batch_id', $doctor_course->batch_id)
                        ->get();
                }

                $data = [];

                foreach($payment_options as $option) {
                    $data[] = [
                        'doctor_course_id'  => $doctor_course->id,
                        'payment_date'      => $option->payment_date,
                        'amount'            => $option->amount,
                    ];
                }

                DoctorCoursePaymentOption::where('doctor_course_id', $doctor_course->id)->delete();

                DoctorCoursePaymentOption::insert($data);
            }
            else if($request->payment_option == 'single' || $request->payment_option == '')
            {
                $doctor_course->payment_option = $request->payment_option;
                $doctor_course->payment_times = "";
                $doctor_course->payment_count = "";
            }

            $doctor_course->updated_by = Auth::id();
            $doctor_course->push();
        }

        $doctor_course->set_payment_status();

        return redirect()
            ->route('doctor_courses.payment.option', $doctor_course->id)
            ->with('message', 'Doctor payment option Changed successfully');
    }
}
