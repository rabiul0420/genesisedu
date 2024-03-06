<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GENESIS : Print Lecture Sheet Delivery</title>
    <link href="{{ asset('assets/css/print/print.css') }}" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
</head>
<body>


<table cellpadding="3" style="vertical-align: middle;">
    <tr class="print">
        <td align="right" colspan="3">
            <button type="button" onclick="window.print()" style="cursor: pointer;">
                <img src=" {{ asset('print.png') }} " width="20" height="20" alt="" title="Print">
            </button>
        </td>
    </tr>

</table>

<table>

    <table cellpadding="3" cellspacing="0"  border="0" style="text-align:justify;border-collapse: collapse; font-size: 15px; display: inline;">
        <thead></thead>
        <tr> <td width="50px"><b>From</b></td><td></td><td></td></tr>
        <tr> <td>Name</td><td>:</td><td>Banglamed Publication</td></tr>
        <tr> <td>Mobile</td><td>:</td><td>{{ isset($doctor_course->batch->lecture_sheet_mobile_no)?$doctor_course->batch->lecture_sheet_mobile_no:'' }}</td></tr>
        <tr> <td>Address</td><td>:</td><td>
            234/C(1st Floor), Sonargaon Road<br> North Side of Katabon Mor, Dhaka.<br>

                </td></tr>

        <tr> <td>Batch</td><td>:</td><td>{{ isset($doctor_course->batch->name)?$doctor_course->batch->name:'' }}</td></tr>
        <tr> <td>Course</td><td>:</td><td>{{ isset($doctor_course->course->name)?$doctor_course->course->name:'' }}</td></tr>
        @if(isset($doctor_course->faculty->name))<tr> <td>Faculty</td><td>:</td><td>{{ isset($doctor_course->faculty->name)?$doctor_course->faculty->name:'' }}</td></tr>@endif
        @if(isset($doctor_course->subject->name))<tr> <td>Discipline</td><td>:</td><td>{{ isset($doctor_course->subject->name)?$doctor_course->subject->name:'' }}</td></tr>@endif
        <tr> <td>Reg No</td><td>:</td><td>{{ isset($doctor_course->reg_no)?$doctor_course->reg_no:'' }}</td></tr>
        <tr> <td colspan="3"><b>Courier Name : </b>{{ isset($doctor_course->courier->name)?$doctor_course->courier->name:'' }}</td></tr>
        <tr> <td colspan="3"><b>Courier Memo No : </b>{{ isset($doctor_course->courier_memo_no)?$doctor_course->courier_memo_no:'' }}</td></tr>
        <tr> <td colspan="3"><b>Quantity : </b>{{ isset($doctor_course->lecture_sheet_packet)?$doctor_course->lecture_sheet_packet:'' }}</td></tr>





        <tbody></tbody>

    </table>

    <table cellpadding="3" cellspacing="0"  border="0" style="text-align:justify;border-collapse: collapse; font-size: 15px; margin-top:10px; display: inline;">
        <thead></thead>
        <tr> <td width="50px"><b>To</b></td><td></td><td></td></tr>
        <tr> <td>Name</td><td>:</td><td>{{ isset($doctor_course->doctor->name)?$doctor_course->doctor->name:'' }}</td></tr>
        <tr> <td>Mobile</td><td>:</td><td>{{ isset($doctor_course->doctor->mobile_number)?$doctor_course->doctor->mobile_number:'' }}</td></tr>
        <tr> <td>Address</td><td>:</td><td style="word-wrap: break-word;max-width: 1px;">{{ $doctor_course->courier_address }} </td></tr>
        <tr> <td>Upazila</td><td>:</td><td>{{ isset($doctor_course->courier_upazila->name)?$doctor_course->courier_upazila->name:'' }} </td></tr>
        <tr> <td>District</td><td>:</td><td>{{ isset($doctor_course->courier_district->name)?$doctor_course->courier_district->name:'' }} </td></tr>
        <tr> <td>Division</td><td>:</td><td>{{ isset($doctor_course->courier_division->name)?$doctor_course->courier_division->name:'' }} </td></tr>

        <tr> <td colspan="3" ><br><br>Purchase Your Necessary Medical Books <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @ <br>
                <span style="font-weight: 400;font-size:19px;">www.medicalbooksonline.net</span>
            </td></tr>


        <tbody></tbody>

    </table>


</table>

<br>
<br>
@if(isset($lecture_sheets))
<table style="display: inline-block;">

    <table  width="450px;"  align="center" cellpadding="3" cellspacing="0"  border="1" style=" font-size: 15px; border-collapse: collapse">
        <thead>
        <tr><th rowspan="2">Lecture Sheet</th><th colspan="2">Status</th></tr>
        <tr><th>Delivered</th><th>Pending</th></tr>
        </thead>

        @php $i = 0; @endphp
        @foreach($lecture_sheets as $lecture_sheet)
            @if(++$i > 15)
                @php break; @endphp
            @endif
            <tr>
                <td>{{ $lecture_sheet->name }}</td><td align="center"><i class=" fa {{  $lecture_sheet->doctor_delivered  ? 'fa-check' : '' }}"></i></td>
                <td align="center"><i class=" fa {{ !$lecture_sheet->doctor_delivered ? 'fa-times' : '' }}"></i></td>
            </tr>
        @endforeach



    </table>

</table>

@if(count($lecture_sheets) > 15)
<p align="right"><b>[ P.T.O ]</b></p>
<p style="page-break-after: always;"></p>
<table style="display: inline-block;">

    <table  width="450px;"  align="center" cellpadding="3" cellspacing="0"  border="1" style=" font-size: 15px; border-collapse: collapse">
        <thead>
            <tr>
                <th rowspan="2">Lecture Sheet</th>
                <th colspan="2">Status</th></tr>
            <tr>
                <th>Delivered</th>
                <th>Pending</th>
            </tr>
        </thead>

        @php $i = 0; @endphp

        @foreach($lecture_sheets as $lecture_sheet)
            @if(++$i <= 15)
                @php continue; @endphp
            @endif
            <tr>
                <td>{{ $lecture_sheet->name }}</td>
                <td align="center"><i class=" fa {{(boolean) $lecture_sheet->doctor_delivered  ? 'fa-check' : '' }}"></i></td>
                <td align="center"><i class=" fa {{ !$lecture_sheet->doctor_delivered ? 'fa-times' : '' }}"></i></td>
            </tr>
        @endforeach


    </table>
@endif

</table>
@endif



</body>
</html>

