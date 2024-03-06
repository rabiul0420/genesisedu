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
                        <i class="fa fa-reorder"></i>{{ $module_name }} Create
                    </div>
                </div>

                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\ModuleScheduleController@module_schedule_save'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Module (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <label class="control-label"><b>{{ $module->name ?? ''}}</b></label>
                                    <input type="hidden" name="module_id" required value="{{ $module->id ?? '' }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Schedule Sub Line (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <input type="text" name="schedule_info" required value="{{ old('schedule_info') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Contact Details (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <input type="text" name="contact_details" required value="{{ old('contact_details') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Address (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <input type="text" name="address" required value="{{ old('address') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Terms & Conditions (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <textarea id="terms_and_conditions" name="terms_and_conditions" class="form-control">{{ old('terms_and_conditions') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>                        

                    </div>                    

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/module-schedule-list/'.$module->id) }}" class="btn btn-default">Cancel</a>
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

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            CKEDITOR.replace( 'terms_and_conditions' );

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

