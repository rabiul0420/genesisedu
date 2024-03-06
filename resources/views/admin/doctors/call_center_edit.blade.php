@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Doctor Edit</li>
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
                        <i class="fa fa-reorder"></i>Doctor Edit
                    </div>
                </div>
                <div>
                    <?php
                    //echo '<pre>';
                    //print_r($doctor);
                    ?>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['url'=>'admin/update-by-call-center','method'=>'POST','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <input type="hidden" name="id"  value="{{ $doctor->id }}">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Basic Information</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-1 control-label">BMDC No (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <input type="text" name="bmdc_no" required  value="{{ $doctor->bmdc_no }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Mobile No (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <input type="text" name="mobile_number" required value="{{ $doctor->mobile_number }}" maxlength="11" class="form-control">
                                        </div>
                                    </div>
                                {{-- </div> --}}
                                <label class="col-md-1 control-label">Email (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-2">
                                    <div class="input-icon right">
                                        <input type="email" name="email" required value="{{ $doctor->email }}" class="form-control">
                                    </div>
                                </div>

                                    <label class="col-md-1 control-label"><b>PASSWORD</b></label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <input type="text" <?php //pattern="[0-9]*" ?> name="password"  value="{{ $doctor->main_password }}" class="form-control" placeholder="Use only numbers ( i.e : 12345678 )">
                                        </div>
                                    </div>

                                </div>


                            </div>
                        </div>
                        
                        
                    <div class="form-actions">
                        <div class="form-group">
                            <label class="col-md-1 control-label"></label>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">Update</button>
                                <a href="{{ url('admin/doctors') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>

        </div>
    </div>



@endsection

@section('js')

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd',
                startDate: '1900-01-01',
                endDate: '2020-12-30',
            }).on('changeDate', function(e){
                $(this).datepicker('hide');
            });

            $("body").on( "change", "[name='permanent_division_id']", function() {
                var permanent_division_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/permanent-division-district',
                    dataType: 'HTML',
                    data: {permanent_division_id: permanent_division_id},
                    success: function( data ) {
                        $('.permanent_district').html(data);
                        $('.permanent_upazila').html('');
                    }
                });
            });

            $("body").on( "change", "[name='permanent_district_id']", function() {
                var permanent_district_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/permanent-district-upazila',
                    dataType: 'HTML',
                    data: {permanent_district_id: permanent_district_id},
                    success: function( data ) {
                        $('.permanent_upazila').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='present_division_id']", function() {
                var present_division_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/present-division-district',
                    dataType: 'HTML',
                    data: {present_division_id: present_division_id},
                    success: function( data ) {
                        $('.present_district').html(data);
                        $('.present_upazila').html('');
                    }
                });
            });







            $("body").on( "change", "[name='present_district_id']", function() {
                var present_district_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/present-district-upazila',
                    dataType: 'HTML',
                    data: {present_district_id: present_district_id},
                    success: function( data ) {
                        $('.present_upazila').html(data);
                    }
                });
            });
        });


    </script>


@endsection