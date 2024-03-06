<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GENESIS : Print Courier Address</title>
    <link href="{{ asset('assets/css/print/print.css') }}" rel="stylesheet">
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


<table class="result-data" border="0" width="900px" align="center" cellpadding="0" cellspacing="0">

</table><br>
<div align="left"><h1><b>Doctor Details</b></h1></div>
<div><h5><b>Doctor Information</b></h5></div>
<table class="result-data" width="900px;"  align="center" cellpadding="3" cellspacing="0"  border="0" style="border-collapse: collapse; font-size: 15px;">
    <thead></thead>
    <tr> <td width="100px">Name</td><td>:</td><td>{{ isset($doctor_course->doctor->name)?$doctor_course->doctor->name:'' }}</td></tr>
    <tr> <td>Mobile</td><td>:</td><td>{{ isset($doctor_course->doctor->mobile_number)?$doctor_course->doctor->mobile_number:'' }}</td></tr>
    <tr> <td>Batch</td><td>:</td><td>{{ isset($doctor_course->batch->name)?$doctor_course->batch->name:'' }}</td></tr>
    <tr> <td>Course</td><td>:</td><td>{{ isset($doctor_course->course->name)?$doctor_course->course->name:'' }}</td></tr>
    <tr> <td>Faculty</td><td>:</td><td>{{ isset($doctor_course->faculty->name)?$doctor_course->faculty->name:'' }}</td></tr>
    <tr> <td>Discipline</td><td>:</td><td>{{ isset($doctor_course->subject->name)?$doctor_course->subject->name:'' }}</td></tr>
    <tr> <td>Reg No</td><td>:</td><td>{{ isset($doctor_course->reg_no)?$doctor_course->reg_no:'' }}</td></tr>

    <tbody></tbody>

</table>
<br><br>
<div><h5><b>Courier Address</b></h5></div>

<table class="result-data" width="900px;"  align="center" cellpadding="3" cellspacing="0"  border="0" style="border-collapse: collapse; font-size: 15px;">
    <thead></thead>
    <tr> <td width="100px;">Address</td><td>:</td><td>{{ $doctor_course->courier_address }} </td></tr>
    <tr> <td>Upazila</td><td>:</td><td>{{ isset($doctor_course->courier_upazila->name)?$doctor_course->courier_upazila->name:'' }} </td></tr>
    <tr> <td>District</td><td>:</td><td>{{ isset($doctor_course->courier_district->name)?$doctor_course->courier_district->name:'' }} </td></tr>
    <tr> <td>Division</td><td>:</td><td>{{ isset($doctor_course->courier_division->name)?$doctor_course->courier_division->name:'' }} </td></tr>

    <tbody></tbody>

</table>


</body>
</html>

