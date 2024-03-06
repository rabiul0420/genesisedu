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
            <p> {!! Session::get('message') !!}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{ $module_name }} Create
                    </div>
                </div>

                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\RoomSlotController@room_slot_save'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-1 control-label">Room (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <label class="control-label"><b>{{ $room->name ?? ''}}</b></label>
                                    <input type="hidden" name="room_id" required value="{{ $room->id ?? '' }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="property">
                            <style>
                                .auto-width
                                {
                                    width: 100%;                                    
                                    text-align: left;
                                }
                                .vertical-center
                                {
                                    margin: 0;
                                    position: absolute;
                                    top: 50%;
                                    -ms-transform: translateY(-50%);
                                    transform: translateY(-50%);
                                }
                                .span-minus
                                {
                                    padding-left: 5px;
                                    text-align: left;
                                    cursor: pointer;
                                }
                                .span-plus
                                {
                                    padding-left: 5px;
                                    text-align: left;
                                    cursor: pointer;
                                }
                            </style>

                            <table class="auto-width">
                                <tr id="1">                                    
                                    <td>
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">
                                                <span class="control-label span-minus remove"><i class="fa fa-minus-circle"> </i></span>
                                                <span class="control-label span-plus add"><i class="fa fa-plus-circle"></i></span>
                                            </label>
                                            
                                            <label class="col-md-1 control-label">Start Time (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-2">
                                                <div class="input-icon right">
                                                    <input type="text" name="start_time[]" required value="{{ old('start_time') }}" class="form-control timepicker" autocomplete="off">
                                                </div>
                                            </div>
                                            <label class="col-md-1 control-label">End Time(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-2">
                                                <div class="input-icon right">
                                                    <input type="text" name="end_time[]" required value="{{ old('end_time') }}" class="form-control timepicker" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>                                    
                        </div>
                    </div>                    

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/room-slot-list/'.$room->id) }}" class="btn btn-default">Cancel</a>
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

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            // $('.timepicker').datetimepicker({
            //     format: 'LT',
            // });

            $("body").on( "focus", ".timepicker", function() {
                $(this).datetimepicker({
                    format: 'YYYY-MM-DD-HH-mm',
                    // Your Icons
                    // as Bootstrap 4 is not using Glyphicons anymore
                    icons: {
                        time: 'fa fa-clock-o',
                        date: 'fa fa-calendar',
                        up: 'fa fa-chevron-up',
                        down: 'fa fa-chevron-down',
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right',
                        today: 'fa fa-check',
                        clear: 'fa fa-trash',
                        close: 'fa fa-times'
                    }
                });
            });

            $("body").on( "focus", ".date", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    startDate: '',
                    endDate: '',
                }).on('changeDate', function(e){
                    $(this).datepicker('hide');
                });
            });

            $("body").on( "click", ".remove", function() {
                $("#"+$(this).closest('tr').prop('id')).remove();
            });

            $("body").on( "click", ".add", function() {
                var row = $(this).closest('tr').clone().insertAfter($(this).closest('tr'));
                row.attr("id",$(this).closest('table').find("tr").last().attr('id')+1);
            });

            $("body").on( "change", "[name='branch_id']", function() {
                var branch_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/branch-change-in-room',
                    dataType: 'HTML',
                    data: {branch_id : branch_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.location').html('');
                        $('.floor').html('');
                        $('.capacity').html('');
                        $('.location').html(data['location']);
                    }
                });
            });

            $("body").on( "change", "[name='location_id']", function() {
                var location_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/location-change-in-room',
                    dataType: 'HTML',
                    data: {location_id : location_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.floor').html('');
                        $('.capacity').html('');
                        $('.floor').html(data['floor']);
                        $('.capacity').html(data['capacity']);
                    }
                });
            });

        })
    </script>


@endsection

