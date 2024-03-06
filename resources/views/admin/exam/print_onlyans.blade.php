<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Print Exam</title>

    <style>
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
            font: 12pt "Tahoma";
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .subpage {
            padding: 1cm;
            border: 5px red solid;
            height: 257mm;
            outline: 2cm #FFEAEA solid;
        }

        @page {
            size: A4;
            margin-left: 40px;
            margin-top:20px;
            margin-right:20px;
        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
            }
            .page {
                margin: 0px;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }

    </style>
</head>

<body>

<div style="width:1000px; overflow:auto; border-bottom:5px #000000 solid; margin:0 auto; padding-top:5px; border-radius:10px;">

    <div style="float:left; width:200px; border:0px #000000 solid; text-align:center; padding-top:100px;">
        <span style="font-family:Verdana; font-size:15px; font-weight:bold;">Total Mark : {{ $question_type_info->full_mark }}</span><br>
        <span style="font-family:Verdana; font-size:15px; font-weight:bold;">@if ($question_type_info->pass_mark) {{ "Pass Mark : ".$question_type_info->pass_mark }} @endif</span>
    </div>
    <div style="float:left; width:600px; border:0px #000000 solid; text-align:center; height:160px; line-height:170%;">
        <span style="font-family:Cooper; font-size:30px;">GENESIS</span><br>
        <span style="font-family:Verdana; font-size:16px;">(Post Graduation Medical orientation Centre)<br><b>Exam : {{ $exam_info->name }}</b><br></span>
        <span style="font-family:Verdana; font-size:16px;">

            Class/Chapter :
            <?php
                $temp_name = \App\Exam_topic::select('*')->where('exam_id', $exam_info->id)->get();
                foreach ($temp_name as $topic_ids){
                    $temp_name = \App\Topics::select('*')->where('id', $topic_ids->topic_id)->get();

                    foreach ($temp_name as $topics){

                        echo $topics->name.", ";
                    }
                }
                ?>
        </span><br>
        <span style="font-family:Verdana; font-size:16px;">
            @php if ($question_type_info->sba_number) {$qn=$question_type_info->mcq_number+1; echo "Question ". $qn ." to End is Based on Single Answers";} @endphp
        </span><br>
    </div>
    <div style="float:left; width:200px; border:0px #000000 solid; text-align:center; padding-top:100px;">
        <span style="font-family:Verdana; font-size:15px; font-weight:bold;">Time : {{ $question_type_info->duration }} Min</span><br>
        <span style="font-family:Verdana; font-size:15px; font-weight:bold;">Date : {{ $exam_info->exam_date }}</span>
    </div>

</div>

<div style="width:1000px; overflow:auto; border:0px #000000 solid; margin:0 auto; margin-top:0px; ">

    <?php
        foreach ($questions as $key=>$vlaue) {
    ?>
        <?php   foreach ($vlaue as $new_key=>$new_vlaue) {   ?>
            <div style="float:left; width:1000px; border:0px #000000 solid; text-align:left; padding-right:10px; overflow:auto;">

                <span style="font-family:Verdana; font-size:14px; font-weight:normal;">
                    <b>{{ $new_key+1 }}.</b>
                    @php
                        $question = explode(',',$new_vlaue->question_id);
                        $question_name = array();
                        foreach ($question as $individual_question){
                            $temp_name = \App\Question::select('question_title', 'correct_ans', 'discussion', 'reference')->where('id',$individual_question)->get()->toArray();
                            foreach ($temp_name as $value){
                                //echo "<b>".$question_title[] = strip_tags($value['question_title'])."</b>";
                            }

                            $temp_name = \App\Question_ans::select('answer', 'sl_no', 'correct_ans')->where('question_id',$individual_question)->get()->toArray();
                            foreach ($temp_name as $value2){
                                $answer[] = $value2['answer'];
                                //echo "<br>".strtolower($value2['sl_no'])."). ".strip_tags($value2['answer']);
                            }


                            foreach ($temp_name as $value3){
                                echo "<b>".$value3['correct_ans']."</b>";
                            }
                            echo "<br>";
                            echo "<b>".$correct_ans[] = $value['correct_ans']."</b>";
                            echo "<b>Discussion:</b>"."<b>";
                            echo $discussion[] = ($value['discussion']);
                            echo $reference[] = ($value['reference']);
                            echo "<br>";
                            echo "<br>";
                        }
                    @endphp
            </div>
        <?php } ?>
    <?php } ?>
</div>

@php //echo $questions; @endphp

</body>
</html>
