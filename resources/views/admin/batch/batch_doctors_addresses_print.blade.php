<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GENESIS : SIF</title>
    <link href="{{ asset('assets/css/print/print.css') }}" rel="stylesheet">
</head>
<body>


<table cellpadding="3" style="vertical-align: middle;">
    <tr class="print">
        <td align="right" colspan="3">
            <button type="button" onclick="window.print()">
                <img src=" {{ asset('print.png') }} " width="20" height="20" alt="" title="Print">
            </button>
        </td>
    </tr>

</table>
<style>
@media print {
  #page-break { 
    page-break-after: always;
    height: 0;
    display: block;  // optional. must be sure it is block item in document flow
    /*clear: both;   // optional. use only if you are using float */   
  }
}

</style>
@php $row=0; @endphp
<table class="" width="900px;"  align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 15px;">
    @foreach($doctors_courses as $doctor_course)
    <tr>
        <td>
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>{{ (isset($doctor_course[1]->doctor->name)) ? $doctor_course[1]->doctor->name :'' }}</td>
                </tr>
                <tr>
                    <td>{{ (isset($doctor_course[1]->doctor->present_address)) ? $doctor_course[1]->doctor->present_address :'' }}</td>
                </tr>
                <tr>
                    <td>{{ (isset($doctor_course[1]->doctor->present_upazila)) ? $doctor_course[1]->doctor->present_upazila->name :'' }}</td>
                </tr>
                <tr>
                    <td>{{ (isset($doctor_course[1]->doctor->present_district)) ? $doctor_course[1]->doctor->present_district->name :'' }}</td>
                </tr>
                <tr>
                    <td>{{ (isset($doctor_course[1]->doctor->present_division)) ? $doctor_course[1]->doctor->present_division->name :'' }}</td>
                </tr>
            </table>    
        </td>
        <td>
            <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                    <td>{{ (isset($doctor_course[2]->doctor->name)) ? $doctor_course[2]->doctor->name :'' }}</td>
                </tr>
                <tr>
                    <td>{{ (isset($doctor_course[2]->doctor->present_address)) ? $doctor_course[2]->doctor->present_address :'' }}</td>
                </tr>
                <tr>
                    <td>{{ (isset($doctor_course[2]->doctor->present_upazila)) ? $doctor_course[2]->doctor->present_upazila->name :'' }}</td>
                </tr>
                <tr>
                    <td>{{ (isset($doctor_course[2]->doctor->present_district)) ? $doctor_course[2]->doctor->present_district->name :'' }}</td>
                </tr>
                <tr>
                    <td>{{ (isset($doctor_course[2]->doctor->present_division)) ? $doctor_course[2]->doctor->present_division->name :'' }}</td>
                </tr>
            </table>
        </td>      
    </tr><br><br> 
    <div id="page-break"></div>    
    @endforeach
</table>

</body>
</html>

