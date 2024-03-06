@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Coursewise Online Exam Links</h3></div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        <div class="col-md-12 col-md-offset-0" style="">
                            <hr><h4><b>Coursewise Online Exam Links</b></h4>
                        </div>

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Reg. No.</th>
                                            <th><b>Online Exam Links</b></th>
                                            <th><b>Online Results & Answer Details</b></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php $i=1; @endphp
                                        @foreach ($online_exam_links as $key=>$online_exam_link)
                                            @php $j=0 @endphp
                                            @foreach($online_exam_link as $k=>$link)
                                            <tr>
                                                @if($j==0)
                                                <td rowspan="{{ count($online_exam_link) }}">{{ $i++ }}</td>
                                                <td rowspan="{{ count($online_exam_link) }}">{{ $key }}</td>
                                                @endif                                                    
                                                        
                                                <td rowspan="1"><a href="{{ 'https://banglamedexam.com/user-login-sif?'.'name='.$doc_info->name.'&email='.$key.'&password='.$doc_info->main_password.'&bmdc='.$doc_info->bmdc_no.'&phone='.$doc_info->mobile_number.'&exam_comm_code='.$link.'&topic_code='."0" }}" target="_blank" class="btn btn-sm btn-primary">{{ $link }}</a></td>
                                                <!-- <td rowspan="1"><a href="{{ 'https://banglamedexam.com/ans_details_sif.php?'.'reg_no='.$key.'&exam_comm_code='.$link }}" target="_blank" class="btn btn-sm btn-warning">{{ 'Ans Details - '.$link }}</a></td>
                                                    -->
                                                <!-- <br> -->
                                                    
                                                @if($j==0)
                                                <td  rowspan="{{ count($online_exam_link) }}">
                                                    <a href="{{ "https://banglamedexam.com/history_sif.php?reg_no=".$key }}" target="_blank" class="btn btn-sm btn-success">Online Results & Answer Details</a>
                                                </td>
                                                @endif

                                            </tr>
                                            @php $j++ @endphp
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
