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
                    {!! Form::open(['action'=>['Admin\DoctorsController@update',$doctor->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Basic Information</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-1 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="name" required value="{{ $doctor->name }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Date Of Birth </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="date_of_birth" autocomplete="off" value="{{ $doctor->date_of_birth }}" class="form-control" id="datepicker">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Gender</label>
                                    <div class="col-md-3" id="id_div_doctors_gender">
                                        <label class="radio-inline">
                                            <input type="radio" name="gender" value="Male" {{  $doctor->gender === "Male" ? "checked" : '' }} > Male
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="gender" value="Female" {{  $doctor->gender === "Female" ? "checked" : '' }} > Female
                                        </label>

                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">BMDC No (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="bmdc_no" required value="{{ $doctor->bmdc_no }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Mobile No (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="mobile_number" required value="{{ $doctor->mobile_number }}" maxlength="11" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Email (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="email" name="email" required value="{{ $doctor->email }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Father Name</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="father_name" value="{{ $doctor->father_name }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Mother Name</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="mother_name" value="{{ $doctor->mother_name }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Spouse Name</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="spouse_name"  value="{{ $doctor->spouse_name }}" class="form-control">
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Medical College </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $medical_colleges->prepend('Select Medical College', ''); @endphp
                                            {!! Form::select('medical_college_id',$medical_colleges, $doctor->medical_college_id?$doctor->medical_college_id:'' ,['class'=>'form-control']) !!}<i></i>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Blood Group</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <select name="blood_group"  class="form-control">
                                                <option <?php if ($doctor->blood_group == '' ) echo 'selected' ; ?> value="">Select Blood Group</option>
                                                <option <?php if ($doctor->blood_group == 'A+' ) echo 'selected' ; ?> value="A+">A+</option>
                                                <option <?php if ($doctor->blood_group == 'A-' ) echo 'selected' ; ?> value="A-">A-</option>
                                                <option <?php if ($doctor->blood_group == 'B+' ) echo 'selected' ; ?> value="B+">B+</option>
                                                <option <?php if ($doctor->blood_group == 'B-' ) echo 'selected' ; ?> value="B-">B-</option>
                                                <option <?php if ($doctor->blood_group == 'AB+' ) echo 'selected' ; ?> value="AB+">AB+</option>
                                                <option <?php if ($doctor->blood_group == 'AB-' ) echo 'selected' ; ?> value="AB-">AB-</option>
                                                <option <?php if ($doctor->blood_group == 'O+' ) echo 'selected' ; ?> value="O+">O+</option>
                                                <option <?php if ($doctor->blood_group == 'O-' ) echo 'selected' ; ?> value="O-">O-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Facebook Id</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="facebook_id"  value="{{ $doctor->facebook_id }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="col-md-1 control-label">Job Description</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="job_description"  value="{{ $doctor->job_description }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">NID</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="nid" value="{{ $doctor->nid }}" class="form-control">
                                        </div>
                                    </div>
                                    <label class="col-md-1 control-label">Passport</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="passport"  value="{{ $doctor->passport }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="col-md-1 control-label">Passport</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="passport"  value="{{ $doctor->passport }}" class="form-control">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label"><b>PASSWORD</b></label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" <?php //pattern="[0-9]*" ?> name="password"  value="{{ $doctor->main_password }}" class="form-control" placeholder="Use only numbers ( i.e : 12345678 )">
                                        </div>
                                        <!-- <span style="color:red;font-weight:700;">Use only numbers ( i.e : 12345678 )</span> -->
                                    </div>

                                </div>


                            </div>
                        </div>



                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Permanent Address</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-1 control-label">Division</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $permanent_divisions->prepend('Select Division', ''); @endphp
                                            {!! Form::select('permanent_division_id',$permanent_divisions, $doctor->permanent_division_id?$doctor->permanent_division_id:'' ,['class'=>'form-control']) !!}<i></i>
                                        </div>
                                    </div>

                                    <div class="permanent_district">
                                        <label class="col-md-1 control-label">District</label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                @php  $permanent_districts->prepend('Select District', ''); @endphp
                                                {!! Form::select('permanent_district_id',$permanent_districts, $doctor->permanent_district_id?$doctor->permanent_district_id:'' ,['class'=>'form-control']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="permanent_upazila">

                                        <label class="col-md-1 control-label">Upazila</label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                @php  $permanent_upazilas->prepend('Select District', ''); @endphp
                                                {!! Form::select('permanent_upazila_id',$permanent_upazilas, $doctor->permanent_upazila_id?$doctor->permanent_upazila_id:'' ,['class'=>'form-control']) !!}<i></i>
                                            </div>
                                        </div>

                                    </div>


                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Address</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <textarea class="form-control" rows="3" name="permanent_address" >{{ $doctor->permanent_address?$doctor->permanent_address:'' }}</textarea>
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
                                        <div class="input-icon right">
                                            @php  $present_divisions->prepend('Select Division', ''); @endphp
                                            {!! Form::select('present_division_id',$present_divisions, $doctor->present_division_id?$doctor->present_division_id:'' ,['class'=>'form-control']) !!}<i></i>
                                        </div>
                                    </div>

                                    <div class="present_district">

                                        <label class="col-md-1 control-label">District</label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                @php  $present_districts->prepend('Select District', ''); @endphp
                                                {!! Form::select('present_district_id',$present_districts, $doctor->present_district_id?$doctor->present_district_id:'' ,['class'=>'form-control']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="present_upazila">

                                        <label class="col-md-1 control-label">Upazila</label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                @php  $present_upazilas->prepend('Select District', ''); @endphp
                                                {!! Form::select('present_upazila_id',$present_upazilas, $doctor->present_upazila_id?$doctor->present_upazila_id:'' ,['class'=>'form-control']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Address</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <textarea class="form-control" rows="3" name="present_address" >{{ $doctor->present_address?$doctor->present_address:'' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Images</div>
                            <div class="panel-body">
                                <div class="form-group" >
                                    <label class="col-md-1 control-label">Select Photo</label>

                                    <div class="col-md-5">
                                        <div class="col-md-4">
                                            <input class="form-control" type="file" name="photo" value="{{ $doctor->photo?$doctor->photo:'' }}">
                                        </div>
                                        <div class="col-md-1">
                                            <img src=" {{ asset($doctor->photo) }}" width="100px;" height="100px" alt="{{$doctor->photo}}">
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Select Sign</label>
                                    <div class="col-md-5">
                                        <div class="col-md-4">
                                            <input class="form-control" type="file" name="sign" value="{{ $doctor->sign?$doctor->sign:'' }}">
                                        </div>
                                        <div class="col-md-1">
                                            <img src=" {{ asset($doctor->sign) }}" width="100px;" height="100px" alt="{{$doctor->sign}}">
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">

                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $doctor->status,['class'=>'form-control','required'=>'required']) !!}<i></i>
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