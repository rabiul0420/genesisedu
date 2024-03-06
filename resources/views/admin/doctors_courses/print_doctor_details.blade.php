<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GENESIS :Doctor Course Details</title>
    <link href="{{ asset('assets/css/print/print.css') }}" rel="stylesheet">
</head>
<body>
    <style>
        .text-center{
            text-align: center;
        }
        .text-1{
            width: 500px;
        }
        .text-2{
            width: 70px;
        }
        .text-3{
            width: 200px;
        }
        .table {
          font-family: Arial, Helvetica, sans-serif;
          border-collapse: collapse;
          width: 1000px;
        }
        
        .table td, .table th {
          border: 1px solid #ddd;
          padding: 8px;
        }
        
        .table tr:nth-child(even){background-color: #f2f2f2;}
        
        .table tr:hover {background-color:  #e6f2ff
    ;}
        
        .table th {
          padding-top: 12px;
          padding-bottom: 12px;
          text-align: left;
          background-color: #ddd;
          color: black;
        }

        
        </style>

<table cellpadding="3" style="vertical-align: middle;">
    <tr class="print">
        <td align="right" colspan="3">
            <button type="button" onclick="window.print()" style="cursor: pointer;">
                <img src=" {{ asset('print.png') }} " width="20" height="20" alt="" title="Print">
            </button>
        </td>
    </tr>

</table>


<table class="result-data" border="0" width="900px" align="center" cellpadding="0" cellspacing="0">

</table><br>

<div style="width:1000px; overflow:auto; border-bottom:5px #000000 solid; margin:0 auto; padding-top:5px; border-radius:10px;">
    <div style="float:left; width:950px; border:0px #000000 solid; text-align:center; height:120px; line-height:170%;">
        <span style="font-family:Cooper; font-size:30px;">GENESIS</span><br>
        <span style="font-family:Verdana; font-size:16px;">(Post Graduation Medical orientation Centre)</span>
        <p>www.genesisedu.info</p>
        <span style="font-family:Verdana; font-size:16px;">
    </div>
</div>

<div align="left"><h1 class="text-center" style="width:1000px;" ><b>Doctor Course Details</b></h1></div>
<div><h3><b>Doctor Information</b></h3></div>
<table class="result-data" width="900px;"  align="center" cellpadding="3" cellspacing="0"  border="0" style="border-collapse: collapse; font-size: 15px;">
    <thead></thead>
    <tr> <td width="200px">Name</td><td>:</td><td>{{ $doctor_course->doctor->name ??'' }}</td></tr>
    <tr> <td>Bmdc no</td><td>:</td><td>{{ $doctor_course->doctor->bmdc_no ??'' }}</td></tr>
    <tr> <td>Mobile</td><td>:</td><td>{{ $doctor_course->doctor->mobile_number ??'' }}</td></tr>
    <tr> <td>Reg No</td><td>:</td><td>{{ $doctor_course->reg_no ??'' }}</td></tr>
    <tr> <td>Institute</td><td>:</td><td>{{ $doctor_course->course->institute->name ??'' }}</td></tr>
    <tr> <td>Course</td><td>:</td><td>{{ $doctor_course->course->name ??'' }}</td></tr>
    <tr> <td>Faculty</td><td>:</td><td>{{ $doctor_course->faculty->name ??'' }}</td></tr>
    <tr> <td>Discipline</td><td>:</td><td>{{ $doctor_course->subject->name ??'' }}</td></tr>
    @if(isset($doctor_course->bcps_subject->name))
        <tr> <td>BCPS Discipline</td><td>:</td><td>{{ $doctor_course->bcps_subject->name }}</td></tr>
    @endif
    <tr> <td>Candidate Type</td><td>:</td><td>{{ $doctor_course->candidate_type ??'' }}</td></tr>
    <tr> <td>Batch</td><td>:</td><td>{{ $doctor_course->batch->name ??'' }}</td></tr>
    <tr> <td>Year</td><td>:</td><td>{{ $doctor_course->batch->year ??'' }}</td></tr>
    <tr> <td>Session</td><td>:</td><td>{{ $doctor_course->session->name ??'' }}</td></tr>
    <tr> <td>Branch</td><td>:</td><td>{{ $doctor_course->batch->branch->name ??'' }}</td></tr>
    <tr> <td>Admission Time</td><td>:</td><td>{{ $doctor_course->created_at->format('d M Y - g:i A')??'' }}</td></tr>
    <tr> <td>Payment Status</td><td>:</td><td>{{ $doctor_course->payment_status ??'' }}</td></tr>
    <tr> <td>Payment Completed By</td><td>:</td><td>
        @if($doctor_course->payment_status == "Completed" && $doctor_course->payment_completed_by_id)
                    {{ $doctor_course->payment_completed_by->name }} 
                @elseif($doctor_course->payment_status == "Completed")
                    Online Payment
                @else
                   {{ $doctor_course->payment_status }}
        @endif
    </td></tr>

    <tr> <td> <h3><b>Current Status</b> </h3> </td><td></td></tr> 
    <tr> <td>Doctor Course Status</td><td>:</td><td>{{ isset($doctor_course)? $doctor_course->course_inactive(): '' }}</td></tr>

    @if($doctor_course->batch_shifted =='1')
    <tr> <td>Batch Shifted</td><td>:</td><td>{{ isset($doctor_course->batch)? $doctor_course->batch_shifted(): '' }}</td></tr>
    <tr> <td>Batch Shifted Note</td><td>:</td><td>{{ $doctor_course->batch_shifted_info ??'' }}</td></tr>
    @endif

    <tr> <td>Batch Status</td><td>:</td><td>{{ isset($doctor_course->batch)? $doctor_course->batch->get_status(): '' }}</td></tr>
    <tbody></tbody>

</table>

<br> 
<div><h3><b>Discount</b></h3></div>
<div>
	<table class="table table-striped table-bordered table-hover datatable">
		<thead>
		<tr>
            <th>Got Discount</th>
			<th>Paid Amount</th>
			<th>Trx ID</th>
			<th>Payment Date</th>
		</tr>
		</thead>
		<tbody>
		@foreach($doctor_course_payments as $payemnt)
		<tr>
            <td>{{(isset($discounts) ? $discounts->amount .' tk' : '')}}</td>
			<td>{{$payemnt->amount ?? ''}} tk</td>
			<td>{{$payemnt->trans_id ?? ''}}</td>
            {{-- @if($doctor_course->payment_status == "Completed") --}}
			    <td>{{$payemnt->created_at->format('d M Y - g:i A') ??''}}</td>
            {{-- @else() --}}
			    {{-- <td>{{$payemnt->created_at->addHour(6)->format('d M Y - g:i A') ??''}}</td> --}}
            {{-- @endif --}}


		</tr>
		@endforeach
		</tbody>
	</table>
</div>
<br><br>
<h3> Lecture Sheet Collection Point : <b>{{ (!$doctor_course->include_lecture_sheet)?'Admitted Without Lecture Sheets':(($doctor_course->include_lecture_sheet && $doctor_course->delivery_status == '1') ? "Courier Address" : (($doctor_course->include_lecture_sheet && $doctor_course->delivery_status == '0') ? 'GENESIS Office Collection' : ''))}}</b> </h3>

<table class="result-data" width="900px;"  align="center" cellpadding="3" cellspacing="0"  border="0" style="border-collapse: collapse; font-size: 15px;">
    <thead></thead>
    @if ($doctor_course->delivery_status == '1')
    <tr> <td width="228px;">Address</td><td>:</td><td>{{ $doctor_course->courier_address }} </td></tr>
    <tr> <td>Upazila</td><td>:</td><td>{{ $doctor_course->courier_upazila->name ??'' }} </td></tr>
    <tr> <td>District</td><td>:</td><td>{{ $doctor_course->courier_district->name ??'' }} </td></tr>
    <tr> <td>Division</td><td>:</td><td>{{ $doctor_course->courier_division->name ??'' }} </td></tr>
    @endif
    <tbody></tbody>
</table>



</body>
</html>

