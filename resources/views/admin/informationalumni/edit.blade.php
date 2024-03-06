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
            Alumni Edit
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
                    <i class="fa fa-reorder"></i> Alumni Edit
                </div>
            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                {{-- {!!
                Form::open(['action'=>'Admin\InformationalumniController@update', $informationalumni->id],'files'=>true,'class'=>'form-horizontal'])
                !!} --}}
                {!! Form::open(['action'=>['Admin\InformationalumniController@update', $informationalumni->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                <div class="form-body">



                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Doctor_id(<i class="fa fa-asterisk ipd-star"
                                    style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <select name="doctor_id"   class="form-control doctor2" required>
                                            <option value="{{$informationalumni->doctor->id}}">{{$informationalumni->doctor->name}}</option>
                                    </select>

                                </div>
                            </div>

                        </div>

                      
                    </div>




                    <div class="panel-body">
                        <div class="institutes">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Courses(<i class="fa fa-asterisk ipd-star"
                                        style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        
                                        @php $courses->prepend('Select Courses', ''); @endphp
                                        {!! Form::select('course_id',$courses,$informationalumni->course_id  
                                        ,['class'=>'form-control' ,'required'=>'required','id'=>'Course_id','onChange'=>'candidateTypeForm()'])
                                        !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>


                   <div class="form-group">
                        <label class="col-md-3 control-label">Result</label>
                        <div class="col-md-3" id="Result">
                            <label class="radio-inline">
                                <input type="radio" name="result" value="pass" {{$informationalumni->result === "pass" ? "checked" : '' }}> Pass
                            </label>
                            
                            <label class="radio-inline">
                                <input type="radio" name="result" value="failed" {{ $informationalumni->result === "failed" ? "checked" : '' }}> Failed
                            </label>
                        </div>

                    </div>

                

                    <div class="form-group">
                        <label class="col-md-3 control-label">Email
                            (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>)
                        </label>
                        <div class="col-md-3">
                            <div class="input-icon right">
                                <input type="email" name="email"
                                    value= "{{ $informationalumni->email }}" class="form-control" required>

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Phone
                            (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>)
                        </label>
                        <div class="col-md-3">
                            <div class="input-icon right">
                                <input type="phone" name="phone" value="{{ $informationalumni->phone }}"
                                    class="form-control">
                            </div>
                        </div>
                    </div>



                </div>
                <div class="form-actions">
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-info">Update</button>
                            <a href="{{ url('admin/information-alumni') }}" class="btn btn-default">Cancel</a>
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

<script>
    function candidateTypeForm() {
        const instituteValue = document.getElementById('institute_id').value
        const candidateType = document.getElementById('candidateType')
        if (instituteValue == 6) {
            candidateType.style.display = 'block'
        } else {
            candidateType.style.display = 'none'
        }
    }
</script>

<script type="text/javascript">
    $(document).ready(function () {

        $('.payment_status').click(function (e) {
            e.preventDefault();
        });


        $('.doctor2').select2({
            minimumInputLength: 3,
            placeholder: "Please type doctor's name or bmdc no",
            escapeMarkup: function (markup) {
                return markup;
            },
            language: {
                noResults: function () {
                    return "No Doctors found, for add new doctor please <a target='_blank' href='{{ url('admin/doctors/create') }}'>Click here</a>";
                }
            },
            ajax: {
                url: '/admin/search-doctors',
                dataType: 'json',
                type: "GET",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term
                    };
                },

                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            let title = item.name + " - " + (item.bmdc_no || "") + " - " + (item.phone || "");
                            return { id:item.id , text: title };
                        })
                    };
                }
            }
        });

        $("body").on("change", "[name='Courses_id']", function () {
            var institute_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/Courses',
                dataType: 'HTML',
                data: {
                    Courses_id: Courses_id
                },
                success: function (data) {
                    $('.courses').html('');
                    $('.faculties').html('');
                    $('.subjects').html('');
                    $('.batches').html('');
                    $('.batch_details').html('');
                    $('.delivery_status').html('');
                    $('.courier_division').html('');
                    $('.courier_district').html('');
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                    $('.lecture_sheets').html('');
                    $('.reg_no').html('');
                    $('#reg_no_first_part').text('');
                    $('[name="reg_no_first_part"]').val('');
                    $('[name="reg_no_last_part"]').val('');
                    $('#range').html('');
                    $('#message').html('');
                    $('.session-faculty').html('');
                    $('.session-subject').html('');
                    $('.courses').html(data);
                }
            });
        });

        $("body").on("change", "[name='course_id']", function () {
            var course_id = $("[name='course_id']").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/course-changed',
                dataType: 'HTML',
                data: {
                    course_id: course_id
                },
                success: function (data) {
                    $('.faculties').html('');
                    $('.subjects').html('');
                    $('.delivery_status').html('');
                    $('.courier_division').html('');
                    $('.courier_district').html('');
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                    $('.lecture_sheets').html('');
                    $('.reg_no').html('');
                    $('#reg_no_first_part').text('');
                    $('[name="reg_no_first_part"]').val('');
                    $('[name="reg_no_last_part"]').val('');
                    $('#range').html('');
                    $('#message').html('');
                    $('.session-faculty').html('');
                    $('.session-subject').html('');
                    $('.session-faculty').html(data);

                }
            });
        });

        $("body").on("change", "[name='faculty_id']", function () {
            var faculty_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/faculty-subjects-in-admission',
                dataType: 'HTML',
                data: {
                    faculty_id: faculty_id
                },
                success: function (data) {
                    $('.subjects').html(data);
                }
            });
        });

        $("body").on("change", "[name='branch_id'],[name='course_id']", function () {

            var institute_id = $("[name='institute_id']").val();
            var course_id = $("[name='course_id']").val();
            var branch_id = $("[name='branch_id']").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/courses-branches-batches',
                dataType: 'HTML',
                data: {
                    institute_id: institute_id,
                    course_id: course_id,
                    branch_id: branch_id
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    $('.batch_details').html('');
                    $('.lecture_sheets').html('');
                    $('.batches').html(data['batches']);
                }
            });

        });

        $("body").on("change", "[name='batch_id'],[name='session_id']", function () {


            var session_id = $('#session_id').val();
            var course_id = $('#course_id').val();
            var batch_id = $('#batch_id').val();
            var batch_id = $('#batch_id').val();
            var second_ajax_call = true;

            if ( /* year && */ session_id && course_id && batch_id) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/registration-no',
                    dataType: 'HTML',
                    data: {
                        /*year: year, */
                        session_id: session_id,
                        course_id: course_id,
                        batch_id: batch_id
                    },
                    success: function (data) {
                        var data = JSON.parse(data);

                        console.log('DDD', data['lecture_sheets']);


                        $('.delivery_status').html('');
                        $('.courier_division').html('');
                        $('.courier_district').html('');
                        $('.courier_upazila').html('');
                        $('.courier_address').html('');

                        // $('.lecture_sheets').html('');
                        // $('.lecture_sheets').html(data['lecture_sheets']);
                        $('.batch_details').html('');
                        $('.batch_details').html(data['batch_details']);
                        $('#range').html('');
                        $('#message').html('');
                        $('#reg_no_first_part').text(data['reg_no_first_part']);
                        $('[name="reg_no_first_part"]').val(data['reg_no_first_part']);
                        $('[name="reg_no_last_part"]').val(data['reg_no_last_part']);
                        $('[name="t_id"]').val(data['t_id']);
                        $('#range').html(data['range']);
                        $('#message').html(data['message']);
                        $('#submit').prop("disabled", false);
                        if (data['message'] !== null && data['message'] !== '') {
                            $('#submit').prop("disabled", true);
                        }

                        if (data['is_lecture_sheet'] == 'Yes') {
                            $('#lecture_sheet').css({
                                display: "block"
                            });
                            $('[name="include_lecture_sheet"]').attr("required", true);
                        }
                        if (data['is_lecture_sheet'] == 'No') {
                            $('#lecture_sheet').css({
                                display: "none"
                            });
                            $('[name="include_lecture_sheet"]').attr("required", false);
                        }

                    }
                });

            }

            if (second_ajax_call && course_id && batch_id) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/batch-details',
                    dataType: 'HTML',
                    data: {
                        course_id: course_id,
                        batch_id: batch_id
                    },
                    success: function (data) {
                        var data = JSON.parse(data);
                        $('.batch_details').html('');
                        $('.batch_details').html(data['batch_details']);

                    }
                });
            }
        });

        $("body").on("change", "[name='include_lecture_sheet']", function () {
            var include_lecture_sheet = $(this).val();
            if (include_lecture_sheet == '1') {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/change-include-lecture-sheet',
                    dataType: 'HTML',
                    data: {
                        include_lecture_sheet: include_lecture_sheet
                    },
                    success: function (data) {

                        $('.delivery_status').html(data);
                    }
                });

            } else {
                $('.delivery_status').html('');
                $('.courier_division').html('');
                $('.courier_district').html('');
                $('.courier_upazila').html('');
                $('.courier_address').html('');
            }

        });

        $("body").on("change", "[name='delivery_status']", function () {
            var delivery_status = $(this).val();
            if (delivery_status == '1') {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/change-lecture-sheet-collection',
                    dataType: 'HTML',
                    data: {
                        delivery_status: delivery_status
                    },
                    success: function (data) {
                        $('.courier_division').html(data);
                        $('.courier_district').html('');
                        $('.courier_upazila').html('');
                        $('.courier_address').html('');
                    }
                });

            } else {
                $('.courier_division').html('');
                $('.courier_district').html('');
                $('.courier_upazila').html('');
                $('.courier_address').html('');
            }

        });


        $("body").on("change", "[name='courier_division_id']", function () {
            var courier_division_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/courier-division-district',
                dataType: 'HTML',
                data: {
                    courier_division_id: courier_division_id
                },
                success: function (data) {
                    $('.courier_district').html(data);
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                }
            });
        });

        $("body").on("change", "[name='courier_district_id']", function () {
            var courier_district_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/courier-district-upazila',
                dataType: 'HTML',
                data: {
                    courier_district_id: courier_district_id
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    $('.courier_upazila').html(data['upazilas']);
                    $('.courier_address').html(data['courier_address']);
                }
            });
        });
    })

    $("#discount_code").on("input", function (e) {
        e.preventDefault();
        var batch_id = $('#batch_id').val();
        var discount_code = $(this).val();
        var doctor_id = $('.select2-selection__rendered').data('id');
        if (batch_id && discount_code.length >= 6) {
            console.log({
                batch_id,
                discount_code
            });
            $("#discount_code").removeClass('is-invalid')
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/apply-discount-code',
                dataType: 'JSON',
                data: {
                    batch_id,
                    discount_code,
                    doctor_id
                },
                success: function (data) {
                    console.log(data);
                    if (data.valid == true) {
                        console.log('ok', data);
                        var amount = $('.amount_1').val();
                        discount_amount = data.amount;
                        $('.discount_price').val(data.amount);
                        $(".error_msg").addClass('text-success mt-2 fw-bold');
                        $(".error_msg").text('Your worth discount: ' + data.amount + ' .TK');
                    } else {
                        console.log('eror', data);
                        $(".error_msg").addClass('text-danger mt-2 fw-bold');
                        $(".error_msg").text('Your code is invalid');
                    }
                },
                error: function () {

                }
            });
        } else {
            $("#discount_code").addClass('is-invalid')
        }
    });

    // $("body").on( "change", "[name='batch_id']", function() {
    //     $('.batch-details').css({ display: "block" });
    // });
</script>


@endsection