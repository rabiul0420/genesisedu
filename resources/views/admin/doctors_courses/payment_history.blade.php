@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>{{$title}}</li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <style>
        
    </style>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{$title}} 
                        @if($doctor_course->batch->payment_times > 1 && $doctor_course->payment_status != "Completed")
                            <a href="{{ url('admin/doctor-course/payment-option/'.$doctor_course->id) }}" class="btn btn-xs btn-info">Payment Option</a>
                        @endif
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    
                    <div class="form-body">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Doctor Course Payment Details</div>
                            <div class="panel-body">
                                <div>
                                    <table class="table table-striped table-bordered table-hover userstable datatable dataTable no-footer">
                                        <tr>
                                            <th>Name : </th><th colspan="3">{{ ($doctor_course->doctor->name.' - '.$doctor_course->reg_no) ?? "" }}</th>
                                        </tr>
                                        <tr>
                                            <th>Batch : </th><th colspan="3">{{ ($doctor_course->batch->name) ?? "" }}</th>
                                        </tr>
                                        <tr>
                                            <th></th><th colspan="3"> </th>
                                        </tr>
                                        <tr>
                                            <th>Serial No</th><th>Details</th><th>Amount</th><th>CURRENCY</th>
                                        </tr>
                                        @php $k=0; @endphp
                                        <tr>
                                            <td>{{ ++$k }}</td><td>Admission Fee</td><td>{{ $doctor_course->actual_course_fee() ?? '0'}}</td><td>BDT</td>
                                        </tr>

                                        <tr>
                                            <td>{{ ++$k }}</td><td>Lecture Sheet Fee</td><td>{{ $doctor_course->lecture_sheet_fee() ?? '0'  }}</td><td>BDT</td>
                                        </tr>

                                        <tr>
                                            <td>{{ ++$k }}</td><td>Lecture Sheet Courier Charge</td><td>{{ $doctor_course->courier_charge() ?? '0' }}</td><td>BDT</td>
                                        </tr>

                                        @if(isset($doctor_course->batch_shift_from) && $doctor_course->batch_shift_from->count())
                                        <tr>
                                            <td>{{ ++$k }}</td>
                                            <td>
                                                Batch Shift Fee
                                            </td>
                                            <td>{{ $doctor_course->batch_shift_from->shift_fee ?? 0 }}</td>
                                            <td>BDT</td>
                                        </tr>
                                        <tr>
                                            <td>{{ ++$k }}</td>
                                            <td>
                                                Maintenance Charge
                                            </td>
                                            <td>{{ $doctor_course->batch_shift_from->service_charge ?? 0 }}</td>
                                            <td>BDT</td>
                                        </tr>
                                        <tr>
                                            <td>{{ ++$k }}</td>
                                            <td>
                                                Payment Adjustment
                                            </td>
                                            <td>{{ $doctor_course->batch_shift_from->payment_adjustment ?? 0 }}</td>
                                            <td>BDT</td>
                                        </tr>
                                        @php $from_doctor_course = $doctor_course->batch_shift_from->from_doctor_course; @endphp
                                        <tr>
                                            <td>{{ ++$k }}</td>
                                            <td>
                                                <a target="_blank" href="{{ route('doctors-courses.edit', $doctor_course->batch_shift_from->from_doctor_course_id) }}">
                                                    (<b>{{ $from_doctor_course->reg_no }}</b>)
                                                </a>
                                                Previous Batch Paid Amount
                                            </td>
                                            <td>
                                                {{ $from_doctor_course->paid_amount() - $from_doctor_course->lecture_sheet_fee() - $from_doctor_course->courier_charge() }}
                                            </td>
                                            <td>BDT</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td>{{ ++$k }}</td><td>Discount From Previous Admission</td><td>{{ $doctor_course->discount_from_prev_admission() ?? '0'}}</td><td>BDT</td>
                                        </tr>

                                        @if($doctor_course->batch->payment_times > 1 && $doctor_course->payment_option == "single")
                                        <tr>
                                            <td>{{ ++$k }}</td><td>Full Payment Waiver</td><td>{{ $doctor_course->batch->full_payment_waiver ?? '0'}}</td><td>BDT</td>
                                        </tr>
                                        @endif

                                        @if( ( $doctor_course->batch->payment_times == 1 ||  $doctor_course->payment_option == "single" )  && $doctor_course->batch->service_point_discount == "yes")
                                        <tr>
                                            <td>{{ ++$k }}</td><td>Branch Discount</td><td>{{ $doctor_course->service_point_discount() ?? '0'}}</td><td>BDT</td>
                                        </tr>
                                        @endif
                                        @endif


                                        @if($doctor_course->discount_codes() !== null)
                                        @foreach($doctor_course->discount_codes() as $discount_code)
                                        <tr>
                                            <td>{{ ++$k }}</td><td>Coupon Discount ( {{ $discount_code->discount_code }} )</td><td>{{ $discount_code->amount ?? '0'}}</td><td>BDT</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                        <style>
                                            .cbold
                                            {
                                                font-size: 15px;
                                                font-weight: 700;
                                            }
            
                                        </style>
                                        <tr class="cbold">
                                            <td colspan="2" style="text-align:right;">Total Course Fee</td><td>{{ $doctor_course->course_price ?? '0' }}</td><td>BDT</td>
                                        </tr>

                                        <tr class="cbold">
                                            <td colspan="2" style="text-align:right;">Paid</td><td>{{ $doctor_course->paid_amount() ?? '0' }}</td><td>BDT</td>
                                        </tr>

                                        <tr class="cbold">
                                            <td colspan="2" style="text-align:right;">Due</td><td>{{ ($doctor_course->course_price - $doctor_course->paid_amount()) > 0 ? ($doctor_course->course_price - $doctor_course->paid_amount()) : '0' }}</td><td>BDT</td>
                                        </tr>
                                        
                                    </table>
                                </div>
                            </div>
                        </div>
                        @if($doctor_course->installments() !== null)
                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Doctor Course Installment Options :</div>
                            <div class="panel-body">

                                <div>
                                    <table class="table table-striped table-bordered table-hover userstable datatable dataTable no-footer">
                                        <tr>
                                            <th>Installment No</th><th>Installment Last Date</th><th>Installment Target Amount (%)</th><th>Installment Target Amount</th><th>Installment Amount</th><th>CURRENCY</th>
                                        </tr>
                                        @php $m=0; @endphp
                                        @foreach($doctor_course->installments() as $k=>$installment)
                                        @if($k==0)
                                        <tr>
                                            <td>{{ ++$m }}</td><td>{{ Date("jS F - Y",date_create_from_format('Y-m-d',$installment->payment_date)->getTimestamp()) }}</td><td>{{ $installment->amount }} %</td><td>{{ ($installment->amount * $doctor_course->course_price)/100 }}</td><td>{{ ($installment->amount * $doctor_course->course_price)/100 }}</td><td>BDT</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td>{{ ++$m }}</td><td>{{ Date("jS F - Y",date_create_from_format('Y-m-d',$installment->payment_date)->getTimestamp()) }}</td><td>{{ $installment->amount }} %</td><td>{{ ($installment->amount * $doctor_course->course_price)/100 }}</td><td>{{ $doctor_course->installment_gap($k-1,$k) }}</td><td>BDT</td>
                                        </tr>
                                        @endif
                                        @endforeach                              
                                    </table>
                                </div>

                            </div>
                        </div>
                        @endif
                    </div>                    
                    <!-- END FORM-->                    
                </div>

                @if($doctor_course->payments() !== null)
                <div class="panel panel-primary"  style="border:10px;border-color: #eee; ">
                    <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Payment History :</div>
                    <div class="panel-body">
                                                
                        <div>
                            <table class="table table-striped table-bordered table-hover userstable datatable dataTable no-footer">
                                <tr>
                                    <th>Serial No</th><th>Date</th><th>Trx ID</th><th>Note</th><th>Amount</th><th>CURRENCY</th>
                                </tr>
                                @foreach($doctor_course->payments() as $k=>$payment)
                                <tr>
                                    <td>{{ ++$k }}</td><td>{{ Date("jS F - Y, g:i a",$payment->created_at->timestamp) }}</td><td>{{ $payment->trans_id }}</td><td>{{ $payment->note }}</td><td>{{ $payment->amount }}</td><td>BDT</td>
                                </tr>
                                @endforeach                              
                            </table>
                        </div>                        
                        
                    </div>
                </div>
                @endif    
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->

@endsection

@section('js')

@endsection