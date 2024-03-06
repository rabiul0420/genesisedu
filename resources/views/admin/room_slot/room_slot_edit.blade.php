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

    <style>
        
        input[type=checkbox][disabled] {
            outline: 5px solid #31b0d5;
            outline-offset: -20px;
        }

        .title-content
        {
            padding: 20px;
            font-size: 27px;
            text-align: center;
            font-weight:700;
            color:blueviolet;
        }

    </style>


    <div class="row title-content">
        {{ $room_slot->room->name??'' }} Slot Edit
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{ $module_name }} Edit
                    </div>
                </div>

                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\RoomSlotController@room_slot_update'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        
                        <div class="form-group">
                            <label class="col-md-1 control-label">Room (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <input type="hidden" name="room_slot_id" required value="{{ $room_slot->id ?? '' }}" class="form-control">
                                    <label class="control-label"><b>{{ $room_slot->room->name ?? '' }}</b></label>
                                    <input type="hidden" name="room_id" required value="{{ $room_slot->room->id ?? '' }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input type="text" name="date" required value="{{ $room_slot->custom_date() ?? '' }}" class="form-control date" autocomplete="off">
                                </div>
                            </div>
                            <label class="col-md-1 control-label">Start Time (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input type="hidden" name="start_time" required value="{{ $room_slot->start_time }}" autocomplete="off">
                                    <input type="text" name="start_times" required value="{{ $room_slot->custom_start_time() ?? '' }}" class="form-control timepicker" autocomplete="off">
                                </div>
                            </div>
                            <label class="col-md-1 control-label">End Time(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input type="hidden" name="end_time" required value="{{ $room_slot->end_time }}" autocomplete="off">
                                    <input type="text" name="end_times" required value="{{ $room_slot->custom_end_time() ?? '' }}" class="form-control timepicker" autocomplete="off">
                                </div>
                            </div>
                        </div>

                    </div>                    

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/room-slot-list/'.$room_slot->room->id) }}" class="btn btn-default">Cancel</a>
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

        function custom_date_time(date,time)
        {
            var custom_date = "";
            var custom_time = "";

            if(canSplit(date,'-'))
            {
                var splited = date.split('-');
                
                var custom_date = splited[2]+'-'+splited[1]+'-'+splited[0];
            }

            if(canSplit(time,':'))
            {
                var splited_time = time.split(':');
                var second_part = splited_time[1];
                if(second_part.indexOf('PM') !== -1)
                {
                    if(splited_time[0] == 12)
                    {
                        splited_time[0] = +splited_time[0];
                    }
                    else
                    {
                        splited_time[0] = +splited_time[0] + 12;
                    }
                    
                }

                splited_time[1] = splited_time[1].replace('AM', '');
                splited_time[1] = splited_time[1].replace('PM', '');

                if(splited_time[0] < 10)
                {
                    splited_time[0] = '0'+splited_time[0];
                }
                
                custom_time = splited_time[0]+'-'+splited_time[1].trim();

            }
            
            return custom_date+'-'+custom_time;

        }

        var canSplit = function(str, token){
            return (str || '').split(token).length > 1;         
        }

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $("body").on( "focus", ".timepicker", function() {
                $(this).datetimepicker({
                    //format: 'YYYY-MM-DD-HH-mm',
                    format: 'LT',
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

            $("body").on( "focusout", "[name='start_times']", function(e) {
                var custom_date_t = custom_date_time($("[name='date']").val(),$(this).val());
                $("[name='start_time']").val(custom_date_t);
                console.log($("[name='start_time']").val());
            });

            $("body").on( "focusout", "[name='end_times']", function(e) {
                var custom_date_t = custom_date_time($("[name='date']").val(),$(this).val());
                $("[name='end_time']").val(custom_date_t);
                console.log($("[name='end_time']").val());
            });

            $("body").on( "focus", ".date", function(e) {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    startDate: '',
                    endDate: '',
                }).on('changeDate', function(e){
                    $(this).datepicker('hide');
                    var date = $("[name='date']").val();
                    var c_start_date_time = custom_date_time($("[name='date']").val(),$("[name='start_times']").val());
                    var c_end_date_time = custom_date_time($("[name='date']").val(),$("[name='end_times']").val());
                    $("[name='start_time']").val(c_start_date_time);
                    $("[name='end_time']").val(c_end_date_time);
                    console.log($("[name='start_time']").val());
                });
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

