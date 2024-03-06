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
        <td style="text-align:center;"><h3>GENESIS PG Orientation Centre</h3></td>
    </tr>        
</table>
<br><br>
<table class="" width="900px;"  align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 15px;">
    <tr>
        <td style="text-align:right;"><strong>Date : {{ date('d-m-Y') }}</strong></td>
    </tr>        
</table>
<br><br>
<table class="" width="900px;"  align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 15px;">
    <tr>
        <td width="76px">Course</td><td class="print-hd"> : {{ $course->name }}</td>
    </tr>
    @if(isset($faculty))
    <tr>
        <td width="76px">Faculty</td><td class="print-hd"> : {{ $faculty->name ?? "" }}</td>
    </tr>
    @endif    
    <tr>
        <td width="76px">Discipline</td><td class="print-hd"> : {{ $discipline }}</td>
    </tr>
    <tr>
        <td width="76px">Exam</td><td class="print-hd"> : {{ $exam->name }}</td>
    </tr>
        
</table>
<br><br>

<table class="result-table" width="900px;"  align="center" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 15px;">
    <tr>
        <td class="print-td print-hd" rowspan="3" >Allocated Institute</td><td colspan="8" class="print-td print-hd">Candidate types & marks</td>
    </tr>
    <tr>
        <td colspan="2" class="print-td print-hd">Private</td><td colspan="2"  class="print-td print-hd">Govt.</td><td colspan="2"  class="print-td print-hd">BSMMU</td><td colspan="2" class="print-td print-hd">Defence</td>
    </tr>
    <tr>
        <td class="print-td">Roll</td><td  class="print-td">Marks</td><td class="print-td">Roll</td><td class="print-td">Marks</td><td class="print-td">Roll</td><td class="print-td">Marks</td><td class="print-td">Roll</td><td class="print-td">Marks</td>
    </tr>
    @if(isset($allocated_institutes))
    @foreach($allocated_institutes as $k=>$allocated_institute)
    <tr>
        <td class="print-td">{{ $k }}</td>
        <td class="print-td">
        @if(isset($allocated_institute['private']))
            <table class="print-table">
            @foreach($allocated_institute['private'] as $private)
                <tr><td>{{ $private['registration_no'] }}</td></tr>                
            @endforeach
            </table>
        @endif
        </td>
        <td class="print-td">
        @if(isset($allocated_institute['private']))
            <table class="print-table">
            @foreach($allocated_institute['private'] as $private)
                <tr><td>{{ $private['obtained_mark'] }}</td></tr>                
            @endforeach
            </table>
        @endif
        </td>

        <td class="print-td">
        @if(isset($allocated_institute['govt']))
            <table class="print-table">
            @foreach($allocated_institute['govt'] as $govt)
                <tr><td>{{ $govt['registration_no'] }}</td></tr>                
            @endforeach
            </table>
        @endif
        </td>
        <td class="print-td">
        @if(isset($allocated_institute['govt']))
            <table class="print-table">
            @foreach($allocated_institute['govt'] as $govt)
                <tr><td>{{ $govt['obtained_mark'] }}</td></tr>                
            @endforeach
            </table>
        @endif
        </td>

        <td class="print-td">
        @if(isset($allocated_institute['bsmmu']))
            <table class="print-table">
            @foreach($allocated_institute['bsmmu'] as $bsmmu)
                <tr><td>{{ $bsmmu['registration_no'] }}</td></tr>                
            @endforeach
            </table>
        @endif
        </td>
        <td class="print-td">
        @if(isset($allocated_institute['bsmmu']))
            <table class="print-table">
            @foreach($allocated_institute['bsmmu'] as $bsmmu)
                <tr><td>{{ $bsmmu['obtained_mark'] }}</td></tr>                
            @endforeach
            </table>
        @endif
        </td>

        <td class="print-td">
        @if(isset($allocated_institute['armed_forces']))
            <table class="print-table">
            @foreach($allocated_institute['armed_forces'] as $armed_forces)
                <tr><td>{{ $armed_forces['registration_no'] }}</td></tr>                
            @endforeach
            </table>
        @endif
        </td>
        <td class="print-td">
        @if(isset($allocated_institute['armed_forces']))
            <table class="print-table">
            @foreach($allocated_institute['armed_forces'] as $armed_forces)
                <tr><td>{{ $armed_forces['obtained_mark'] }}</td></tr>                
            @endforeach
            </table>
        @endif
        </td>
    </tr>
    @php $row++ @endphp
    
    @endforeach
    @endif
    
</table>
<br><br>

</body>
</html>

