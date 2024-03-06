<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Course Details Download</title>
    <style>
        body {
            width: 100%;
            margin: 0;
            padding: 0;
            color: #222676;
            background-color: #ffffff;
            font: 12pt "montserrat";
            font-style: normal;
            font-weight: normal
        }

        .container {
            width: 1000px;
            padding-top: 5px;
            border-radius: 10px;
            height: auto;
            display: block;
            margin: auto;
        }

        table {
            border-collapse: collapse;
        }

        .header th img {
            width: 100px;
        }

        .color-brand {
            color: #222676;
        }

        .font-r {
            font-family: "montserrat";
            font-style: normal;
            font-weight: normal
        }

        .font-b {
            font-family: "montserrat";
            font-weight: bold
        }

        .font-sb {
            font-family: "montserrat-semibold";
            font-weight: normal;
            font-style: normal;
        }

        .font-m {
            font-family: "montserrat-medium";
            font-weight: normal;
            font-style: normal;
        }

        .invoice-text {
            color: #707070;
            font-size: 45px;
            font-weight: bold
        }

        .invoice-no {}

        .invoice-no .label {}

        .invoice-date {}

        .margin-top {
            margin: "margin_top";
        }

        /* 
        .service_point{
            width: 80%;
        } */

        body table tr td table thead tr th {
            width: 20%;
            padding: 15px 5px;
            font-size: 20px;
            color: white;
            text-align: left;
            font-family: "montserrat";
            font-weight: bold;

        }


        .doctor-name {
            font-size: 40px;
        }

        body table tr td table tbody tr td {
            padding: 15px 5px;
            font-family: "montserrat";
            font-style: normal;
            font-weight: normal;
            font-size: 22px;

        }

        

        .amount {
            font-size: 22px;
        }

        .payment {
            font-size: 15px;
        }

    </style>
</head>


<body>
    <table class="container" style="height: 500mm">

        <tr class="header">
            <td style="padding: 1cm 0px">
                <div style="display: block">
                    <img style="display: inline-block;margin-right: 10mm;" src="{{ public_path('images/logo-big.png') }}" width="19mm">
                    <img style="display: inline-block" src="{{ public_path('images/invoice/genesis.svg') }}"
                        width="76mm">
                </div>
            </td>
            <td style="text-align:right;">
                <h3 class='font-b invoice-text'>INVOICE</h3>
                <p class="">
                    <span class="font-r" style="font-size: 22px;">Invoice No.:</span>
                    <span class="number font-sb" style="font-size: 22px;">{{ $doctor_course->id ?? '' }}</span>
                </p>
                <p>
                    <span class="font-r" style="font-size: 22px;">Date:</span>
                    <span class="font-sb invoice-date" style="font-size: 22px;">{{ date('d-M-Y') }}</span>
                </p>
            </td>
        </tr>

        <tr >
            <td colspan="3" style="padding: 0.5cm 0cm">
                <hr class="color-brand" style="height: 2px">
            </td>
        </tr>

        <tr >
            <td style="text-align:left; padding-top: 30px;">
                <h1 class="font-b doctor-name">{{ $doctor_course->doctor->name ?? ' '}}</h1>
                <br>
                <p class="font-r" style="font-size:22px;">BMDC Number : <span class="font-sb" style="">{{ $doctor_course->doctor->bmdc_no ?? '' }}</span></p>
                <br>
                <p class="font-r" style="font-size:22px;">Mobile Number : <span class="font-sb" style=""> {{ $doctor_course->doctor->mobile_number ?? '' }}</span></p>
                <br>
                <p class="font-r" style="font-size:22px;">Address : <span class="font-sb" style="">{{ $doctor_course->doctor->present_address ?? 'N/A' }}</span></p>
            </td>

            <td style="text-align:right; padding-top: -2cm">
                <img style="display: inline-block;" src="{{ public_path('images/invoice/design1.svg') }}"
                    width="50mm">
            </td>
        </tr>

        <tr >
            <td style="display: block; text-align: center; padding:30px 0px;" colspan="3">
                <p class="font-r" style="font-size: 37px;">Thanks for your payment.</p>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table class="container" cellspacing="0">
                    <thead>
                        <tr style="width:100%; background: #222676" class="header">
                            <th>Batch Name</th>
                            <th>Course Name</th>
                            <th>Reg No</th>
                            <th>Year</th>
                            <th>Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="width:100%;" class="header">
                            <td>{{ $doctor_course->batch->name ?? '' }}</td>
                            <td>{{ $doctor_course->course->name ?? '' }}</td>
                            <td>{{ $doctor_course->reg_no ?? '' }}</td>
                            <td>{{ $doctor_course->batch->year ?? '' }}</td>
                            <td>{{ $doctor_course->session->name ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <hr class="color-brand" style="height: 2px">
            </td>
        </tr>

        <tr>
            <td class="payment">
                <P class="font-r" style="font-size: 22px;">Lecture Sheet Included :<span class="font-sb">{{ $doctor_course->include_lecture_sheet == 1 ? 'Yes' : 'No' }}</span></P>
                <br><br>
                <h2 class="font-b" style="font-size: 25px;">Payment Information</h2>
                {{-- <br> --}}
                {{-- <p class="font-r">Payment Method : Bkash</p> --}}
                <br>
                <p class="font-r" style="font-size: 22px;">Transaction ID : <span class="font-sb"></span></p>
                {{-- <br> --}}
                {{-- <p class="font-r">Account Number : Bkas</p> --}}
                <br>
                    @if( $last_transaction )
                        <p class="font-r" style="font-size: 22px;">Transaction Date : <span class="font-sb">{{ $last_transaction ?  $last_transaction->created_at->format('d-M-Y'):'' }}</span>
                    @endif
                </p>
                <br>
            </td>
            <td style="text-align: right; padding-right: 0.5cm; padding-top: 2cm" >
                <style>
                    .custom-table th, .custom-table tr, .custom-table td
                    {
                        /*-webkit-text-size-adjust: 100%;*/
                        -webkit-tap-highlight-color: rgba(0,0,0,0);
                        --input-padding-x: 0.75rem;
                        --input-padding-y: 0.75rem;
                        line-height: 1.42857143;
                        font-family: "montserrat", 'Open Sans', sans-serif;
                        font-size: 17px;
                        direction: ltr;
                        color: #222676;
                        box-sizing: border-box;
                        border-spacing: 0;
                        background-color: transparent;
                        border-top: 2px solid #555;
                        clear: both;
                        border-collapse: separate !important;
                        padding: 5px;
                    }
                    .custom-table .header-row, .custom-table .header-row th
                    {
                        background-color: #222676;
                        color:white !important;
                    }

                </style>
                 <!-- <table>
                     <tr>
                        <td class="font-sb" style="text-align: right; font-size: 25px; padding:10px 25px;">Amount:</td>
                        <td><span class="font-r" style="font-size: 30px; padding:10px 25px;"> {{ $course_price ?? 0 }} tk</span></td>
                     </tr>
                     <tr>
                        <td class="font-sb" style="text-align: right; font-size: 25px; padding:10px 25px;">Discount:</td>
                        <td><span class="font-r" style="font-size: 30px; padding:10px 25px;"> {{ $discount * -1 ?? 0 }} tk</span></td>
                     </tr>
                     <tr>
                        <td class="font-sb" style="text-align: right; font-size: 25px; padding:10px 25px;">Paid Amount:</td>
                        <td><span class="font-b" style="font-size: 30px; padding:10px 25px;"> {{ $paid_amount ?? 0}} tk</span></td>
                     </tr>
                 </table> -->
                <table class="table table-striped table-bordered custom-table">
                    <tr class="header-row">
                        <th>SL</th><th>Details</th><th>Amount</th><th>CURRENCY</th>
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
                    @php $from_doctor_course = $doctor_course->batch_shift_from->from_doctor_course; @endphp
                    <tr>
                        <td>{{ ++$k }}</td>
                        <td>
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
            </td>
        </tr>

       

        <tr>
            <td class="service_point" style="">
                <p class="font-sb" style="font-size: 25px;">Service Point :   <span class="font-r" style="font-size: 22px" > {{ $doctor_course->batch->branch->name ?? ''}}</span></p>
                {{-- <p class="font-sb" style="font-size: 25px">Help Line :   <span class="font-r" style="font-size: 22px">  0222345678910 , 02212345678</span></p> --}}
            </td>
            <td style="text-align: right; padding-top: 2.6cm">
                <div style="display: inline-block; float: right">
                    <img class="design2_logo"
                        src="{{ public_path('images/invoice/design2.svg') }}" height="40mm">
                </div>
            </td>
        </tr>

        <tr >
            <td colspan="3" style="padding: 5px 0">
                <div style="display: block;">
                    <img style="display: inline-block; margin-right:10px;" src="{{ public_path('images/invoice/check-icon.svg') }}"
                    width="5mm"><span class="font-m" style="font-size: 22px;" >I agree with the terms and conditions of this organizations.</span>
                </div>
            </td>
        </tr>


        <tr>
            <td colspan="3">
                <hr class="color-brand" style="height: 2px">
            </td>
        </tr>

        <tr >
            <td colspan="3" style="padding: 5px 0px;">
                <table style="width: 100%">
                    <tr>
                        <td class="font-sb" style="font-size:30px;">Payment accepted cordially</td>
                        <td class="font-r" style="font-size:20px; border-right: 2px solid #222676; padding-right: 20px; line-height: 5px;">Phone: 09643001010</td>
                        <td class="font-r" style="font-size:20px; border-left: 2px solid #222676; padding-left: 20px; line-height: 5px;">www.genesisedu.info</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3"
                style="height:15mm; background: #222676; vertical-align:middle; text-align:right; padding-right: 10mm">
                <img src="{{ public_path('images/invoice/design3.svg') }}" height="7mm">
            </td>
        </tr>
    </table>

    <div>
        <h3 class="font-b" style="text-align: center">Terms And Conditions</h3>
        <p class="font-r">
            {!! $terms_and_conditions->value !!}
        </p>
    </div>
</body>

</html>
