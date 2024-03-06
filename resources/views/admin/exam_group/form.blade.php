@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'"> '.$value.' </a></li>';
            }
            ?>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Create
                    </div>
                </div>

                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open([ 'action' => $form_action, 'files'=>true, 'class'=>'form-horizontal', 'method' => $form_method ?? 'POST' ]) !!}
                    <div class="form-body">

                        <div class="years">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        {!! Form::select('year',$years, old( 'year', $exam_group->year ?? '' ) ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="groups">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Group (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        {!! Form::select('group_id', $groups, old( 'group_id', $exam_group->group_id ?? '' ) ,
                                        ['class'=>'form-control select2','required'=>'required','id'=>'group_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="online_exams">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Exams (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right exam-list">
                                        @include( 'admin.exam_group.exam_list' )
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>                    

                    <div class="form-group">
                        <label class="col-md-3 control-label">Select Status</label>
                        <div class="col-md-3">
                            {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status', $exam_group->status ?? '' ),['class'=>'form-control']) !!}<i></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ route( 'exam-group.index' ) }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->

        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $("body").on( "change", "[name='group_id']", function() {

                var group_id = $('#group_id').val();

                if( group_id ) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "GET",
                        url: '/admin/exam-group-exams',
                        dataType: 'HTML',
                        data: { group_id },
                        success: function (data) {
                            $('#reg_no_first_part').text(data);
                            $('[name="reg_no_first_part"]').val(data);
                        }
                    });
                }
            });

            $('.select2').select2({
                //'placeholder':"Select Topic"
            });

        })
    </script>


@endsection