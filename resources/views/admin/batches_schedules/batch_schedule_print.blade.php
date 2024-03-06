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

<table class="result-data" border="0" width="900px" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td width="35%" align="left"><div style="border:1px #000000 solid; padding-left:5px; font-family:Verdana; font-size:30px; font-weight:bold;">{{ (isset($shcedule_details->batch->name))?$shcedule_details->batch->name:'' }} Batch</div></td>
        <td width="35%" align="center"><span style="font-family:Verdana; font-size:50px; font-weight:bold;"><?= strtoupper($company['name']); ?></span></td>
        <td width="30%" align="right"><span style="border:1px #000000 solid; padding:5px; font-family:Verdana; font-weight:bold;">Updated : <?php echo date('Y-m-d',time()); ?> </span></td>
    </tr>
    <tr>
        <td width="35%" align="left"><span style="border:0px #000000 solid; padding:5px; font-family:Verdana; font-size:15px; font-weight:bold;"></span></td>
        <td width="35%" align="center"><span style="font-family:Verdana; font-size:15px; font-weight:bold;"><u><b>Post Graduation Medical Orientation Centre</b></u></span></td>
        <td width="30%" align="right"><span style="border:0px #000000 solid; padding:5px; font-family:Verdana; font-weight:bold;"></span></td>
    </tr>
    <tr>
        <td width="35%" align="left"><span style="border:0px #000000 solid; padding:5px; font-family:Verdana; font-size:15px; font-weight:bold;"></span></td>
        <td width="35%" align="center"><span style="font-family:Verdana; font-size:15px; font-weight:bold;"><b><?php echo $shcedule_details->name; ?></b></span></td>
        <td width="30%" align="right"><span style="border:0px #000000 solid; padding:5px; font-family:Verdana; font-weight:bold;"></span></td>

    </tr>
    <tr>
        <td width="35%" align="left"><span style="border:0px #000000 solid; padding:5px; font-family:Verdana; font-size:15px; font-weight:bold;"></span></td>
        <td width="35%" align="center"><span style="font-family:Verdana; font-size:15px; font-weight:bold;"><b><?php echo $shcedule_details->tag_line; ?></b></span></td>
        <td width="30%" align="right"><span style="border:0px #000000 solid; padding:5px; font-family:Verdana; font-weight:bold;"></span></td>
    </tr>

</table>
<table class="result-data" width="900px;"  align="center" cellpadding="3" cellspacing="0"  border="1" style="border-collapse: collapse; font-size: 15px;">
    <thead></thead>
    <tr align="center" bgcolor="#d2d2d2">
        <th width="14%">Date</th>
        <?php   foreach ($shcedule_slot_details as $vlaue) {
            if($vlaue->slot_type == '3')echo '<th width="14%" rowspan="'.($count_rows+1).'">'.$vlaue->slot->slot_name.'<br>'.$vlaue->start_time.' - '.$vlaue->end_time.'</th>';
            else echo '<th width="14%">'.$vlaue->slot->slot_name.'<br>'.$vlaue->start_time.' - '.$vlaue->end_time.'</th>';
        } ?>
    </tr>



    <?php

    foreach ($lecture_exams as $key=>$vlaue) {


    ?>
    <tr align="center">
        <td align="center">
            <?php
            echo date('d-m-Y',strtotime($key));?>
            <br>
            <?php echo date('l',strtotime($key));
            ?></td>
        <?php   foreach ($vlaue as $new_key=>$new_vlaue) {   ?>

        <td align="left">

            @php
                $topics = explode(',',$new_vlaue->topic_id);
                $topics_name = array();
                foreach ($topics as $individual_topic){
                    $temp_name = \App\Topics::select('name')->where('id',$individual_topic)->get()->toArray();
                    foreach ($temp_name as $value){
                        $topics_name[] = $value['name'];
                    }
                }
                echo implode(' ; ',$topics_name);
            @endphp

        </td>

        <?php  } ?>

    </tr>
    <?php
    }
    ?>


    <tbody></tbody>

</table>

<table class="result-data" border="0" width="900px" align="center" cellpadding="2" cellspacing="0">
    <tr>
        <td colspan="2">
            <font face="Verdana" size="2">N.B: Schedule can be changed in any emergency/unavoidable reason.</font><br>
            <a href="http://genesisedu.info/" style="font-size:20px;">For Result Please Visit: www.genesisedu.info</a>
        </td>
    </tr>
    <tr>
        <td>
            <div style="border:3px #000000 solid; padding:10px; text-align:center; font-size:15px;font-family:Verdana; border-radius:10px;"><?php if($shcedule_details->address){ ?><p style="margin: 0;padding: 0"><b>Address :</b> <?php echo $shcedule_details->address;?></p><?php } if($shcedule_details->room->room_name){ ?><p style="margin: 0;padding: 0"><b>Room :</b> <?php echo $shcedule_details->room->room_name;?></p><?php } if($shcedule_details->contact_details){ ?><p style="margin: 0;padding: 0"><b>Contact :</b><?php echo $shcedule_details->contact_details;?></p><?php } ?></div>
        </td>
        <td width="25%" align="center">Coordinator<br>GENESIS</td>
    </tr>
</table>


</body>
</html>

