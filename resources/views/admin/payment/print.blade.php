<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GENESIS : Payment List Print </title>
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

<div><h4><b>Doctor Information</b></h4></div>
<table class="result-data" width="900px;"  align="center" cellpadding="3" cellspacing="0"  border="0" style="border-collapse: collapse; font-size: 15px;">
    <thead></thead>
    <tr> <td width="100px">Name</td><td>:</td><td>{{ $other_info->doctor->name ?? '' }}</td></tr>
    <tr> <td>Mobile</td><td>:</td><td>{{ $other_info->doctor->mobile_number ?? ''}}</td></tr>
    <tr> <td>Year</td><td>:</td><td>{{ $other_info->batch->year ?? ''}}</td></tr>
    <tr> <td>Batch</td><td>:</td><td>{{ $other_info->batch->name ?? ''}}</td></tr>
    <tr> <td>Course</td><td>:</td><td>{{ $other_info->course->name ?? '' }}</td></tr>
    <tr> <td>Session</td><td>:</td><td>{{$other_info->session->name ?? '' }}</td></tr>
    <tr> <td>Reg No</td><td>:</td><td>{{ $other_info->reg_no ?? '' }}</td></tr>
    <tbody></tbody>
</table>
<br><br>
<div><h4><b>payment verification</b></h4></div>

<table class="table">
    <thead>
    <tr>
        <th>Sl</th>
        <th>Note</th>
        <th>Verified</th>
        <th>Verified By</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
       @foreach ($payment_verification_note as $k=>$payment_verification)
            <tr>
                <td>{{++$k }}</td>
                <td class="text-center text-1" >{{ $payment_verification->note ?? ''}}</td>
                <td class="text-center text-2" >{{ $payment_verification->verified ?? ''}}</td>
                <td class="text-center text-3" >{{ $payment_verification->doctor->name ?? ''}}</td>
                <td class="text-center text-3" >{{ $payment_verification->created_at->format('d M Y - g:i A') ?? ''}}</td>
            </tr>
        @endforeach
    </tbody>
</table>


</body>
</html>

