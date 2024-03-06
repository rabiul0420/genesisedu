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
                        <i class="fa fa-reorder"></i>{{ $module_name }} Edit
                    </div>
                </div>

                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\RoomController@update',$room->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ $room->name }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="branch">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Branch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php $branches->prepend('Select Branch', ''); @endphp
                                        {!! Form::select('branch_id',$branches, $room->branch_id, ['class'=>'form-control','required'=>'required','id'=>'branch_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="location">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Location (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php $locations->prepend('Select Location', ''); @endphp
                                        {!! Form::select('location_id',$locations, $room->location_id, ['class'=>'form-control','required'=>'required','id'=>'location_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="floor">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Floor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="number" name="floor" required value="{{ $room->floor }}" step="1" min="0" max="30" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="capacity">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Capacity (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="number" name="capacity" required value="{{ $room->capacity }}" step="1" min="0" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $room->status,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>                        

                    </div>                    

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/room') }}" class="btn btn-default">Cancel</a>
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $("body").on( "change", "[name='branch_id']", function() {
                var branch_id = $(this).val();
                var view_name = "room_branch_location";
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/branch-change-in-room',
                    dataType: 'HTML',
                    data: {branch_id : branch_id, view_name:view_name},
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
                var view_name = "room_location_floor";
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/location-change-in-room',
                    dataType: 'HTML',
                    data: {location_id : location_id, view_name:view_name},
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

