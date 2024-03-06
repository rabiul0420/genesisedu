<?php

namespace App\Http\Controllers\Admin;

use App\Batches;
use App\BatchShift;
use App\DoctorsCourses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SendSms;
use Illuminate\Support\Facades\Auth;

class BatchShiftController extends Controller
{
    use SendSms;

    public function index()
    {
        $search = request()->search ?? '';

        // return $batch_shifts = $this->batchShiftQuery()->paginate(8);
            
        if(request()->isXmlHttpRequest()) {
            $batch_shifts = $this->batchShiftQuery()->paginate(8);

            return  view('tailwind.admin.batch-shifts.data', compact('batch_shifts', 'search'))->render();
        }

        $to_doctor_course_ids = BatchShift::pluck('to_doctor_course_id');

        // return
        $to_batches = Batches::query()
            ->whereHas('doctor_courses', function($query) use ($to_doctor_course_ids) {
                $query->whereIn('id', $to_doctor_course_ids);
            })
            ->latest()
            ->get([
                'id',
                'name',
            ]);

        return view('tailwind.admin.batch-shifts.index', compact('to_batches'));
    }

    public function update(Request $request, BatchShift $batch_shift)
    {
        if($this->checkBatchShiftChange($batch_shift, $request)) {
            $histories = $batch_shift->histories ?? [];

            array_push($histories, [
                "to_doctor_course_id"   => $batch_shift->to_doctor_course_id,
                "shift_fee"             => $batch_shift->shift_fee,
                "service_charge"        => $batch_shift->service_charge,
                "payment_adjustment"    => $batch_shift->payment_adjustment,
                "shifted_at"            => $batch_shift->shifted_at ? $batch_shift->shifted_at->format('d M Y') : '',
                "note"                  => $batch_shift->note,
                "shifted_by"            => $batch_shift->shifted_by,
            ]);

            $batch_shift->update([
                "to_doctor_course_id"   => $request->to_doctor_course_id,
                "shift_fee"             => $request->shift_fee,
                "service_charge"        => $request->service_charge,
                "payment_adjustment"    => $request->payment_adjustment,
                "shifted_at"            => $request->shifted_at,
                "note"                  => $request->note,
                "shifted_by"            => Auth::id(),
                "histories"             => $histories,
            ]);
        }

        return back();
    }

    private function checkBatchShiftChange($batch_shift, $request)
    {
        $status = false;

        if($batch_shift->to_doctor_course_id != $request->to_doctor_course_id) {
            $status = true;
        }

        if($batch_shift->shift_fee != $request->shift_fee) {
            $status = true;
        }

        if($batch_shift->service_charge != $request->service_charge) {
            $status = true;
        }

        if($batch_shift->payment_adjustment != $request->payment_adjustment) {
            $status = true;
        }

        if(($batch_shift->shifted_at ? $batch_shift->shifted_at->format('Y-m-d') : '') != $request->shifted_at) {
            $status = true;
        }

        if($batch_shift->note != $request->note) {
            $status = true;
        }

        return $status;
    }

    protected function batchShiftQuery()
    {
        $search = trim(request()->search);

        return BatchShift::query()
            ->with([
                'from_doctor_course:id,doctor_id,batch_id,reg_no,year',
                'from_doctor_course.doctor:id,name,mobile_number,bmdc_no',
                'from_doctor_course.doctor.doctor_courses' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'doctor_id',
                            'reg_no',
                            'batch_id',
                        ])
                        ->has('batch_shift_history', '<', 1)
                        ->has('batch_shift_from', '<', 1)
                        ;
                },
                'from_doctor_course.doctor.doctor_courses.batch:id,name',
                'to_doctor_course:id,doctor_id,batch_id,reg_no,year',
                'to_doctor_course.doctor.doctor_courses.batch:id,name',
                'from_doctor_course.batch:id,name',
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('from_doctor_course_id', $search)
                        ->orWhere('to_doctor_course_id', $search)
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('from_doctor_course', function ($query) use ($search) {
                            $query->where('reg_no', 'like', "%{$search}%");
                        })
                        ->orWhereHas('to_doctor_course', function ($query) use ($search) {
                            $query->where('reg_no', 'like', "%{$search}%");
                        })
                        ->orWhereHas('from_doctor_course.doctor', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('mobile_number', 'like', "%{$search}%")
                                ->orWhere('bmdc_no', 'like', "%{$search}%");
                        });
                });
            })
            ->when(request()->date_from, function($query, $date_from) {
                $query->whereDate("shifted_at", ">=", $date_from);
            })
            ->when(request()->date_to, function($query, $date_to) {
                $query->whereDate("shifted_at", "<=", $date_to);
            })
            ->when(request()->from_batch, function($query, $batch_id) {
                $query->whereHas("from_doctor_course.batch", function ($query) use ($batch_id) {
                    $query->where('batch_id', $batch_id);
                });
            })
            ->when(request()->to_batch, function($query, $batch_id) {
                $query->whereHas("to_doctor_course.batch", function ($query) use ($batch_id) {
                    $query->where('batch_id', $batch_id);
                });
            })
            ->latest('id');
    }
}
