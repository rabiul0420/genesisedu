@extends('admin.layouts.app')
@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                Doctor Create
            </li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Doctor Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>'Admin\DoctorsController@store','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Basic Information</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-1 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="name" required value="{{ old('name')?old('name'):'' }}" class="form-control">
                                            
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Date Of Birth </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="date_of_birth" autocomplete="off" value="{{ old('date_of_birth') }}" class="form-control input-append date" id="datepicker">
                                            {{--<input type="date" name="date_of_birth" required value="{{ old('date_of_birth')?old('date_of_birth'):'' }}" class="form-control">--}}
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Gender</label>
                                    <div class="col-md-3" id="id_div_doctors_gender">
                                        <label class="radio-inline">
                                            <input type="radio" name="gender" value="Male" {{  old('gender') === "Male" ? "checked" : '' }} > Male
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="gender" value="Female" {{  old('gender') === "Female" ? "checked" : '' }}> Female
                                        </label>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">BMDC No (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <input type="text" name="bmdc_no" required value="{{ old('bmdc_no')?old('bmdc_no'):'' }}" maxlength="7" class="form-control" onkeyup="this.value = this.value.toUpperCase()"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                            <span class="bmdc_no" style="color:Red;"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <a href="https://www.bmdc.org.bd/search-doctor" target="_blank">Check<br>BMDC</a>
                                    </div>

                                    <label class="col-md-1 control-label">Mobile No (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input name="mobile_number" required value="{{ old('mobile_number')?old('mobile_number'):'' }}" maxlength="11"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="form-control">
                                            <span class="mobile_number" style="color:Red;"></span>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Email (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="email" name="email" required value="{{ old('email')?old('email'):'' }}" class="form-control">
                                            <span class="email_address" style="color:Red;"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Father Name</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="father_name" value="{{ old('father_name')?old('father_name'):'' }}" class="form-control">
                                            
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Mother Name</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="mother_name" value="{{ old('mother_name')?old('mother_name'):'' }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Spouse Name</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="spouse_name"  value="{{ old('spouse_name')?old('spouse_name'):'' }}" class="form-control">
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Medical College</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right" style="text-align:center">
                                            @php  $medical_colleges->prepend('--Select Medical College--', ''); @endphp
                                            {!! Form::select('medical_college_id',$medical_colleges, old('medical_college_id')?old('medical_college_id'):'' ,['class'=>'form-control select2']) !!}<i></i>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Blood Group</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <select name="blood_group"  class="form-control">
                                                <option value="">Select Blood Group</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Facebook Id</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="facebook_id"  value="{{ old('facebook_id')?old('facebook_id'):'' }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="col-md-1 control-label">Job Description</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="job_description"  value="{{ old('job_description')?old('job_description'):'' }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">NID</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="nid" value="{{ old('nid')?old('nid'):'' }}" class="form-control">
                                        </div>
                                    </div>
                                    <label class="col-md-1 control-label">Passport</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="passport"  value="{{ old('passport')?old('passport'):'' }}" class="form-control">
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>


                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Permanent Address</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-1 control-label">Division</label>
                                    <div class="col-md-3" style="text-align:center">
                                            @php  $divisions->prepend('--Select Division--', ''); @endphp
                                            {!! Form::select('permanent_division_id',$divisions, old('permanent_division_id')?old('permanent_division_id'):'' ,['class'=>'form-control select2']) !!}<i></i>
                                    </div>

                                    <div class="permanent_district">

                                    </div>

                                    <div class="permanent_upazila">

                                    </div>

                                </div>



                                <div class="form-group">
                                    <label class="col-md-1 control-label">Address</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <textarea class="form-control" rows="3" name="permanent_address" >{{ old('permanent_address')?old('permanent_address'):'' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Present Address</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-1 control-label">Division</label>
                                    <div class="col-md-3">
                                            @php  $divisions->prepend('Select Division', ''); @endphp
                                            {!! Form::select('present_division_id',$divisions, old('present_division_id')?old('present_division_id'):'' ,['class'=>'form-control select2']) !!}<i></i>
                                    </div>

                                    <div class="present_district">

                                    </div>

                                    <div class="present_upazila">

                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Address</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <textarea class="form-control" rows="3" name="present_address" >{{ old('present_address')?old('present_address'):'' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Images </div>
                            <div class="panel-body">
                                <div class="form-group" >
                                    <label class="col-md-1 control-label">Select Photo</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input class="form-control" type="file" name="photo" value="{{ old('photo')?old('photo'):'' }}">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Select Sign</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input class="form-control" type="file" name="sign" value="{{ old('sign')?old('sign'):'' }}">
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
    
    
    
                            <label class="col-md-1 control-label">Verified BMDC</label>
                            <div class="col-md-3" id="is-verified">
                                <label class="radio-inline">
                                    <input type="radio" name="is_verified" value="yes"> Yes
                                </label>
                                
                                <label class="radio-inline">
                                    <input type="radio" name="is_verified" value="no" checked> No
                                </label>
                            </div>
                        </div> 



                    </div>
                    <div class="form-actions">
                        <div class="form-group">
                            <label class="col-md-1 control-label"></label>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/doctors') }}" class="btn btn-default">Cancel</a>
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

    <script type="text/javascript">
        $(document).ready(function() {

            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd',
                startDate: '1900-01-01',
                endDate: '2020-12-30',
            }).on('changeDate', function(e){
                $(this).datepicker('hide');
            });

            $('.select2').select2();


            $("body").on("keyup", "[name='bmdc_no']", function() {
                var bmdc_no = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/check-bmdc-no',
                    dataType: 'HTML',
                    data: {bmdc_no:bmdc_no},
                    success: function( data ) {
                        $('.bmdc_no').html(data);
                    } 
                });
            });

            $("body").on("keyup", "[name='mobile_number']", function() {
                var mobile_number = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/check-phone-no',
                    dataType: 'HTML',
                    data: {mobile_number:mobile_number},
                    success: function( data ) {
                        $('.mobile_number').html(data);
                    } 
                });
            });

            $("body").on("keyup", "[name='email']", function() {
                var email = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/check-email',
                    dataType: 'HTML',
                    data: {email:email},
                    success: function( data ) {
                        $('.email_address').html(data);
                    } 
                });
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