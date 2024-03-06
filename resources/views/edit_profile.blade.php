@extends('layouts.app')

@section('content')
    <div class="container">


        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">
                <div class="panel panel-default pt-2">
                    <div class="panel_box w-100 bg-white rounded shadow-sm">
                        <div class="header text-center py-3">
                            <h2 class="h2 brand_color">{{ 'Edit Profile' }}</h2>
                        </div>
                    </div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="col-md-12 py-3">

                            <div class="portlet-body form">
                                {!! Form::open(['url' => ['update-profile'], 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) !!}


                                <div class="form-body">

                                    <div class="form-group">
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Doctor Name :</label>
                                         
                                            <div class="col-sm-5 mt-3">
                                                <input type="text" name="doc_name" value="{{ $doc_info->name }}"
                                                class="form-control" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3 ">BMDC No :</label> 
                                            <div class="col-sm-5 mt-3">
                                                <div class="position-relative w-100 prefix">
                                                    <span class="position-absolute border-bottom text-dark border-info bg-warning ;" style="padding:9px 20px; left: 0px;background-color:#08c;color:aliceblue;">A</span>
                                                </div>
                                                <input type="text" style="padding-left: 65px;" name="bmdc_no" value="{{ $doc_info->bmdc_no }}"
                                                        class="form-control " id="bmdc_no" placeholder="Digits Only"
                                                       
                                                        readonly> &nbsp;<span id="errmsg" ></span>
                                            </div>
                                        </div>
                                       
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-0.5">Father's Name :</label>
                                            <div class="col-sm-5 ">
                                                <input type="text" name="father_name" value="{{ $doc_info->father_name }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Mother's Name :</label>
                                            <div class="col-sm-5 mt-3">
                                                <input type="text" name="mother_name" value="{{ $doc_info->mother_name }}"
                                                class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Mobile :</label>
                                            <div class="col-sm-5 mt-3">
                                                <input type="text" name="mobile_number"
                                                value="{{ $doc_info->mobile_number }}" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Email :</label>
                                            <div class="col-sm-5 mt-3">
                                                <input type="text" name="email" value="{{ $doc_info->email }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Facebook ID :</label>
                                            <div class="col-sm-5 mt-3">
                                                <input type="text" name="facebook_id" value="{{ $doc_info->facebook_id }}"
                                                class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Date of Birth :</label>
                                            <div class="col-sm-5 mt-3">
                                                <input type="text" name="date_of_birth"
                                                value="{{ $doc_info->date_of_birth }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Job Description :</label>
                                            <div class="col-sm-5 mt-3">
                                                <input type="text" name="job_description"
                                                    value="{{ $doc_info->job_description }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">NID :</label>
                                            <div class="col-sm-5 mt-3">
                                                <input type="text" name="nid" value="{{ $doc_info->nid }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Passport :</label>
                                            <div class="col-sm-5 mt-3">
                                                <input type="password" name="passport" value="{{ $doc_info->passport }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-o.5">
                                            <label for="doc_info" class="col-sm-2 col-form-label mt-3">Password :</label>
                                            <div class="col-sm-5 mt-3">
                                                <div class="input-icon right">
                                                    Password<br>
                                                    <div class="input-group">
                                                        <input type="password" name="main_password"
                                                            value="{{ $doc_info->main_password }}"
                                                            class="form-control password-field"
                                                            style="position: absolute;width: 100%;" required>
                                                        <span><i toggle=".password-field"
                                                                class="fa fa-fw fa-eye field-icon toggle-password"
                                                                style="position: relative; margin: 10px;right: -340px;"></i></span>
                                                    </div>
                                                </div>
                                            </div>            
                                        </div>

                                    <div class="mt-3">Present Address
                                   </div>
                                    <div class="form-group row">
                                        <label for="doc_info" class="col-sm-2 col-form-label mt-3">Division :</label>
                                        <div class="col-sm-5 mt-3">
                                            @php  $divisions->prepend('Select Division', ''); @endphp
                                                    {!! Form::select('present_division_id', $divisions, $doc_info->present_division_id ? $doc_info->present_division_id : '', ['class' => 'form-control']) !!}<i></i>
                                        </div>
                                    </div>

                                    <div class="present_district">
                                        @if ($doc_info->present_district_id)
                                    <div class="form-group row">
                                        <label for="doc_info" class="col-sm-2 col-form-label mt-3">District :</label>
                                        <div class="col-sm-5 mt-3">
                                            @php  $present_districts->prepend('Select District', ''); @endphp
                                                                {!! Form::select('present_district_id', $present_districts, $doc_info->present_district_id ? $doc_info->present_district_id : '', ['class' => 'form-control']) !!}<i></i>
                                        </div>
                                    </div>
                                    @endif
                                    </div>
                                  
                                    <div class="present_upazila">
                                    @if ($doc_info->present_upazila)
                                    <div class="form-group row">   
                                        <label for="doc_info" class="col-sm-2 col-form-label mt-3">Upazila :</label>
                                        <div class="col-sm-5 mt-3">
                                            @php  $present_upazilas->prepend('Select District', ''); @endphp
                                            {!! Form::select('present_upazila_id', $present_upazilas, $doc_info->present_upazila_id ? $doc_info->present_upazila_id : '', ['class' => 'form-control']) !!}<i></i>
                                        </div>
                                    </div>
                                    @endif
                                    </div>

                                    <div class="form-group row">
                                        <label for="doc_info" class="col-sm-2 col-form-label mt-3">Address :</label>
                                        <div class="col-sm-5 mt-3">
                                            <textarea class="form-control" rows="3"
                                            name="present_address">{{ $doc_info->present_address ? $doc_info->present_address : '' }}</textarea>
                                        </div>
                                    </div>


                                    <div class="form-group row mt-2 mb-2">
                                        <label for="doc_info" class="col-sm-2 col-form-label mt-0.5">Update Photo:</label>
                                        <div class="col-sm-5 ">
                                            <input class="input-field" type="file" id="photo" name="photo" value="">
                                            <img src=" {{ asset($doc_info->photo) }}" width="100px;"
                                                height="100px" alt="{{ $doc_info->photo }}">
                                        </div>
                                    </div>
 
                                    <div class="form-actions">
                                        <div class="row mx-0">
                                            <div class="col-md-offset-0 col-md-9">
                                                <button type="submit" class="btn btn-info">Save Profile</button>
                                                <a href="{{ url('my-profile') }}"
                                                    class="btn btn-outline-info btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!} 
                                    <! END FORM>
                                   
                            </div>
                        </div>
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
                }).on('changeDate', function(e) {
                    $(this).datepicker('hide');
                });

                $("body").on("change", "[name='permanent_division_id']", function() {
                    var permanent_division_id = $(this).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/permanent-division-district',
                        dataType: 'HTML',
                        data: {
                            permanent_division_id: permanent_division_id
                        },
                        success: function(data) {
                            $('.permanent_district').html(data);
                            $('.permanent_upazila').html('');
                        }
                    });
                });

                $("body").on("change", "[name='permanent_district_id']", function() {
                    var permanent_district_id = $(this).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/permanent-district-upazila',
                        dataType: 'HTML',
                        data: {
                            permanent_district_id: permanent_district_id
                        },
                        success: function(data) {
                            $('.permanent_upazila').html(data);
                        }
                    });
                });

                $("body").on("change", "[name='present_division_id']", function() {
                    var present_division_id = $(this).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/present-division-district',
                        dataType: 'HTML',
                        data: {
                            present_division_id: present_division_id
                        },
                        success: function(data) {
                            $('.present_district').html(data);
                            $('.present_upazila').html('');
                        }
                    });
                });

                $("body").on("change", "[name='present_district_id']", function() {
                    var present_district_id = $(this).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/present-district-upazila',
                        dataType: 'HTML',
                        data: {
                            present_district_id: present_district_id
                        },
                        success: function(data) {
                            $('.present_upazila').html(data);
                        }
                    });
                });



                $("#bmdc_no").keypress(function(e) {
                    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                        $("#errmsg").html("Digits Only").show().fadeOut("slow");
                        return false;
                    }
                });

            });
        </script>


    @endsection
