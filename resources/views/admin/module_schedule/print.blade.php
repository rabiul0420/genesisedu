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
    display: block;  /* // optional. must be sure it is block item in document flow */
    /*clear: both;   // optional. use only if you are using float */   
  }
}
.print-table{
    text-align : "center";
    /*border: 1 solid black;*/
    border-collapse: collapse;
    font-size: 15px;
}
.print-td{
    border: 1px solid black;
    text-align: center;
}
.print-hd{
    font-size: 15px;
    font-weight:700;
}

</style>
@php $row=0; @endphp
<table class="" width="900px;"  align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 15px;">
    <tr>
        <td style="text-align:center;"><h3>GENESIS</h3></td>
    </tr>
    <tr>
        <td style="text-align:center;"><strong>Post Graduation Medical Orientation Centre</strong></td>
    </tr>        
</table>
<table class="" width="900px;"  align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 15px;">
    <tr>
        <td style="text-align:center;"><strong>{{ $module_schedule->name }}</strong></td>
    </tr>
    <tr>
        <td style="text-align:center;"><strong>{{ $module_schedule->schedule_info }}</strong></td>
    </tr>        
</table>
<table class="" width="900px;"  align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 15px;">
    <tr>
        <td width="50px">Module</td><td> <b>: {{ $module_schedule->module->name }}</b></td>
    </tr>        
</table>
<br><br>

<table class="result-table" width="900px;"  align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 15px;text-align:center;">
    <style>
        .result-table td, th
        {
            margin: 7px;
            padding: 7px;
            border: 1px solid black;
        }
    </style>

    <thead>
        <td style="text-align:center;font-weight:700;">DATE</td>
        @for($i = $module_schedule->max_slots(); $i>0;$i--)
        <td style="text-align:center;"><span>ROOM</span><br><span><b>START TIME - END TIME</b></span><br><span>Program</span></td>
        @endfor
    </thead>
    <tbody>
        
        @foreach($module_schedule->array_custom_slot_list() as $k=>$array_slot)
            @php $m = 0; $date = explode("-",$k) ;@endphp
            <tr>
                <td><b>{{ $date[2].'/'.$date[1].'/'.$date[0] }}</b></td>
                @foreach($array_slot as $l=>$slot)
                <td>
                <span>{{ $slot->room_name() }}</span><br><span><b>{{ $slot->time_span() }}</b></span><br><br>
                <span>{{ $slot->program->name ?? '' }}</span><br><br>    
                </td>
                @php $m++ @endphp
                @endforeach

                @if($m < $module_schedule->max_slots() )
                @for($m;$m < $module_schedule->max_slots();$m++)
                <td></td>
                @endfor
                @endif
            </tr>
        @endforeach

    </tbody>
                      
</table>
<br><br>
<table class="result-data" border="0" width="900px" align="center" cellpadding="2" cellspacing="0">
    <tr>
        <td colspan="2">
            <font face="Verdana" size="2">N.B: Schedule can be changed in any emergency/unavoidable reason.</font><br>
            <a href="http://genesisedu.info/" style="font-size:20px;">For Result Please Visit: www.genesisedu.info</a>
        </td>
    </tr>
    <tr>
        <td>
            <div style="border:3px #000000 solid; padding:10px; text-align:center; font-size:15px;font-family:Verdana; border-radius:10px;"><?php if($module_schedule->address){ ?><p style="margin: 0;padding: 0"><b>Address :</b> <?php echo $module_schedule->address;?></p><?php } if($module_schedule->contact_details){ ?><p style="margin: 0;padding: 0"><b>Contact :</b><?php echo $module_schedule->contact_details;?></p><?php } ?></div>
        </td>
        <td width="25%" align="center">Coordinator<br>GENESIS</td>
    </tr>
</table>
<br><br>

</body>
</html>

