@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">
            
            @if(Session::has('message'))
                <div  style="margin-top: 25px;" class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                    <p> {{ Session::get('message') }}</p>
                </div>
            @endif
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <!-- <i class="fa fa-reorder"></i>Doctor Course Create -->
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['url'=>['doctor-admission-submit'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}

                    <!-- <input type="hidden" name="include_lecture_sheet" value="0"> -->

                    <div class="form-body">

                        <div class="panel panel-default pt-2">
                            <div class="panel_box w-100 bg-white rounded shadow-sm">
                                <div class="header text-center py-3">
                                    <h2 class="h2 brand_color">{{ 'Admission form' }}</h2>
                                </div>
                            </div>
                            <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                                <div class="offset-md-1 py-4">
                                    <div class="institutes my-1">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    @php  $institutes->prepend('Select Institute', ''); @endphp
                                                    {!! Form::select('institute_id',$institutes, '' ,['class'=>'form-control','required'=>'required','id'=>'institute_id','onChange' => 'candidateTypeForm()']) !!}<i></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="courses my-3">
    
    
                                    </div>
    
                                    <div class="session-faculty my-3">
    
    
                                    </div>
    
                                    <div class="session-subject my-3">
    
    
                                    </div>
                                    
     
    
                                    <div id="candidateType" class="form-group my-3" style="display: none;">
                                        <label class="col-md-3 control-label">Candidate Type (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                        <div class="col-md-3">
                                            <select class="form-select" name="candidate_type">
                                                <option value="">Select Candidate Type</option>
                                                <option value="Autonomous/Private">Autonomous/Private</option>
                                                <option value="Government">Government</option>
                                                <option value="BSMMU">BSMMU</option>
                                                <option value="Armed Forces">Armed Forces</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>
                                    </div>
    
                                    <div class="form-group my-3">
                                        <label class="col-md-3 control-label">Service Point/Branch (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                        <div class="col-md-3">
                                            @php  $branches->prepend( 'Select Branch', '' ); @endphp
                                            {!! Form::select('branch_id', $branches, '',['class'=>'form-control','required'=>'required']) !!}<i></i>
                                        </div>
                                    </div>
                                    
                                    <div class="batches my-3">
    
                                    </div>
                
                                    <!-- <div class="lecture_sheet my-3">

                                    </div> -->
    
                                    <div class="reg_no"></div>
                                    <input type="hidden" name="reg_no_first_part" required value="">
                                    <input type="hidden" name="reg_no_last_part" required value="">
                                    <input type="hidden" name="t_id" value="">
                                    <div class="batch-details" >
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <span class="btn btn-xs btn-primary batch-details"  id='batch-details' data-toggle="modal" data-target="#batchDetails" style="display:none" style="line-height:32px;">Batch Details</span>     
                                            </div>        
                                        </div>
                                    </div>

                                    <div class="lecture_sheet my-3" id="lecture_sheet" style="display:none;">
                                        <div class="form-group ">
                                            <label class="col-md-3 control-label">Lecture Sheet (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                            <div class="col-md-3">
                                                <label class="radio-inline">
                                                    <input type="radio" name="include_lecture_sheet"  value="1"   > Yes
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="include_lecture_sheet"  value="0"  > No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="delivery_status my-3">

                                    </div>

                                    <div class="courier_division my-3">

                                    </div>

                                    <div class="courier_district my-3">

                                    </div>

                                    <div class="courier_upazila my-3">

                                    </div>

                                    <div class="courier_address my-3" style="border:white;background-color:inherit;">

                                    </div>

                                    {{-- <div class="form-group ">
                                        <label class="col-md-3 control-label">Coupon Code </label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="discount_code" value="{{ old('discount_code') }}">
                                        </div>
                                    </div> --}}

                                    <div class="pt-2">
                                        <input type="checkbox" name="terms_condition" id="terms_condition" required>
                                        <span class="pl-2">I agree to the</span> 
                                        <span style="cursor: pointer;" class="btn-link mx-1" data-toggle="modal" data-target="#exampleModalCenter_terms_and_conditions">Terms and Conditions</span>
                                        <span> & </span>
                                        <span style="cursor: pointer;" class="btn-link mx-1" data-toggle="modal" data-target="#exampleModalCenter_refund_policy">Refund & Shifting Policy.</span>
                                    </div>
                                    <!-- <div id="range"></div> -->
                                    
                                    <div id="message"></div>
                                </div>
                            </div>
                            <div class="form-actions my-3">
                                    <div class="form-group offset-md-1">
                                        <label class="col-md-3 control-label"></label>
                                        <div class="col-md-3">
                                            <button id="submit" type="submit" class="btn btn-info" >Submit</button>
                                            <a href="{{ url('my-profile') }}" class="btn btn-outline-info btn-default">Cancel</a>
                                        </div>
                                    </div>
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
    </div>
    <!-- END PAGE CONTENT-->

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter_terms_and_conditions" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle_terms_and_conditions" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle_terms_and_conditions">Terms and conditions</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <style>
                .terms_and_conditions p {
                    margin-bottom: 25px;
                }
                .terms_and_conditions b{
                    margin-top: 15px;
                }
            </style>
            <div class="modal-body terms_and_conditions">
                {{-- @include('terms_condition'); --}}
                {!! App\Setting::property('terms_conditions')->value('value') !!}
            </div>
        </div>
        </div>
    </div>

     <!-- Modal -->
     <div class="modal fade my-5" id="batchDetails" tabindex="-1" role="dialog" aria-labelledby="cashPament" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter_refund_policy" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle_refund_policy" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle_refund_policy">Refund & Shifting Policy.</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            
            <style>
                .refund_policy p {
                    padding: 5px 0;
                    line-height: 1.25;
                }
            </style>
            <div class="modal-body refund_policy">
                {!! App\Setting::property('refund_policy')->value('value') !!}
            </div>
        </div>
        </div>
    </div>


@endsection

@section('js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

 

    <script type="text/javascript">

        const combinedInstituteId = {{ \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID }};

        function candidateTypeForm(){
            const instituteValue = document.getElementById('institute_id').value
            const candidateType = document.getElementById('candidateType')
            if( instituteValue == 6 || instituteValue == combinedInstituteId ){
                candidateType.style.display = 'block'
            }else{
                candidateType.style.display = 'none'
            }
        }


        $(document).ready(function() {

           

            $("#promocode-link").on("click",function(e){
                e.preventDefault();
                $("#promocode").toggleClass( 'd-flex' );
            });

            $("#code_apply_button").on( "click", function(e){
                e.preventDefault( );
                // var batch_id = $(' [name="batch_id"] ').val();
                var batch_id = 158;
                var coupon_code = $(' [name="coupon_code"] ').val();
                if( batch_id && coupon_code ) {

                    //alert( batch_id + ' ' + coupon_code );

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/apply-discount-code',
                        dataType: 'JSON',
                        data: { batch_id, coupon_code },
                        success: function( data ) {
                            console.log( data );
                        },
                        error:function(){

                        }
                    });


                }

            });


            // $(function() { 
            //     $('html, body').animate({ scrollTop: $('.alert').offset().top }, '500');
            // });            

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/institute-courses',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.batches').html('');

                        $('.batch_details').html('');
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

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $("[name='course_id']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/course-changed',
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.faculties').html('');
                        $('.subjects').html('');
                        //$('.batches').html('');
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

            $("body").on( "change", "[name='faculty_id']", function() {
                var faculty_id = $(this).val();
                var is_combined = $('#is_combined').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/faculty-subjects-in-admission',
                    dataType: 'HTML',
                    data: {faculty_id: faculty_id, is_combined },
                    success: function( data ) {
                        $('.subjects').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='branch_id'],[name='course_id'],[name='faculty_id'],[name='subject_id'],[name='session_id']", function() {

                var institute_id = $("[name='institute_id']").val();
                var course_id = $("[name='course_id']").val();
                var branch_id = $("[name='branch_id']").val();
                var faculty_id = $("[name='faculty_id']").val();
                var subject_id = $("[name='subject_id']").val();
                var session_id = $("[name='session_id']").val();
                var is_combined = $('#is_combined').val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/courses-branches-batches',
                    dataType: 'HTML',
                    data: { 
                        institute_id : institute_id,
                        course_id : course_id ,
                        branch_id : branch_id,
                        faculty_id, subject_id,
                        session_id, session_id,
                        is_combined
                    },
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.batch_details').html('');
                        $('.batches').html(data['batches']);
                    }
                });             

            });

            $("body").on( "change", "[name='batch_id'],[name='session_id']", function() {

                var year = $('#year').val();
                var session_id = $('#session_id').val();
                var course_id = $('#course_id').val();
                var batch_id = $('#batch_id').val();
                var batch_id = $('#batch_id').val();
                var second_ajax_call = true;

                // if(year && session_id && course_id && batch_id) {
                if( session_id && course_id && batch_id) {
                    second_ajax_call = false;
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/reg-no',
                        dataType: 'HTML',
                        // data: {year: year, session_id: session_id, course_id: course_id, batch_id: batch_id},
                        data: {session_id: session_id, course_id: course_id, batch_id: batch_id},
                        success: function (data) {
                            var data = JSON.parse(data);
                            // $('.batch_details').html('');
                            // $('.batch_details').html(data['batch_details']);
                            // $('.lecture_sheet').html('');
                            // $('.lecture_sheet').html(data['lecture_sheet']);
                            // $('#range').html('');
                            // $('#message').html('');
                            // $('#reg_no_first_part').text(data['reg_no_first_part']);
                            // $('[name="reg_no_first_part"]').val(data['reg_no_first_part']);
                            // $('[name="reg_no_last_part"]').val(data['reg_no_last_part']);
                            // $('[name="t_id"]').val(data['t_id']);
                            // $('#range').html(data['range']);
                            // $('#message').html(data['message']);
                            // $('#submit').prop( "disabled", false );
                            // if(data['message'] !== null && data['message'] !== '')
                            // {
                            //     $('#submit').prop( "disabled", true );
                            // }
                            // if(data['is_lecture_sheet'] == 'Yes')
                            // {
                            //     $('#lecture_sheet').css({ display: "block" });
                            //     $('[name="include_lecture_sheet"]').attr("required", true);
                            // }
                            // if(data['is_lecture_sheet'] == 'No')
                            // {
                            //     $('#lecture_sheet').css({ display: "none" });
                            //     $('[name="include_lecture_sheet"]').attr("required", false);
                            // }

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
                            $('#submit').prop( "disabled", false );
                            if(data['message'] !== null && data['message'] !== '')
                            {
                                $('#submit').prop( "disabled", true );
                            }

                            if(data['is_lecture_sheet'] == 'Yes')
                            {
                                $('#lecture_sheet').css({ display: "block" });
                                $('[name="include_lecture_sheet"]').attr("required", true);
                            }
                            if(data['is_lecture_sheet'] == 'No')
                            {
                                $('#lecture_sheet').css({ display: "none" });
                                $('[name="include_lecture_sheet"]').attr("required", false);
                            }
                        }
                    });

                }

                // if(second_ajax_call && course_id && batch_id) {
                //     $.ajax({
                //         headers: {
                //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //         },
                //         type: "POST",
                //         url: '/batch-details',
                //         dataType: 'HTML',
                //         data: {course_id: course_id, batch_id: batch_id},
                //         success: function (data) {
                //             var data = JSON.parse(data);
                //             $('.batch_details').html('');
                //             $('.batch_details').html(data['batch_details']);
                //             $('.lecture_sheet').html('');
                //             $('.lecture_sheet').html(data['lecture_sheet']);

                //         }
                //     });
                // }
 
            });


            $("body").on( "change", "[name='batch_id']", function() {
                $('.batch-details').css({ display: "block" });
            });

            $("body").on( "click", ".batch-details", function() {
                var batch_id = $("[name='batch_id']").val();
                $('.modal-body').load('/batch-details-modal',{batch_id: batch_id,_token: '{{csrf_token()}}'},function(){
                    $('.batch-details').modal({show:true});
                });
            });

            $("body").on("change", "[name='include_lecture_sheet']", function () {
                var include_lecture_sheet = $(this).val();
                if(include_lecture_sheet == '1')
                {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/change-include-lecture-sheet',
                        dataType: 'HTML',
                        data: {include_lecture_sheet: include_lecture_sheet},
                        success: function( data ) {

                            $('.delivery_status').html(data);
                        }
                    });

                }
                else
                {
                    $('.delivery_status').html('');
                    $('.courier_division').html('');
                    $('.courier_district').html('');
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                }

            });

            $("body").on("change", "[name='delivery_status']", function () {
                var delivery_status = $(this).val();
                if(delivery_status == '1')
                {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/change-lecture-sheet-collection',
                        dataType: 'HTML',
                        data: {delivery_status: delivery_status},
                        success: function( data ) {
                            $('.courier_division').html(data);
                            $('.courier_district').html('');
                            $('.courier_upazila').html('');
                            $('.courier_address').html('');
                        }
                    });

                }
                else
                {
                    $('.courier_division').html('');
                    $('.courier_district').html('');
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                }

            });

            $("body").on( "change", "[name='courier_division_id']", function() {
                var courier_division_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/courier-division-district',
                    dataType: 'HTML',
                    data: {courier_division_id: courier_division_id},
                    success: function( data ) {
                        $('.courier_district').html(data);
                        $('.courier_upazila').html('');
                        $('.courier_address').html('');
                    }
                });
            });

            $("body").on( "change", "[name='courier_district_id']", function() {
                var courier_district_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/courier-district-upazila',
                    dataType: 'HTML',
                    data: {courier_district_id: courier_district_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.courier_upazila').html(data['upazilas']);
                        $('.courier_address').html(data['courier_address']);
                    }
                });
            });

           

/*

            $("body").on( "change", "[name='year']", function() {

                var year = $('#year').val();
                var session_id = $('#session_id').val();
                var course_id = $('#course_id').val();
                var batch_id = $('#batch_id').val();
                var batch_id = $('#batch_id').val();
                var second_ajax_call = true;

                if(year && session_id && course_id && batch_id) {
                    second_ajax_call = false;
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/reg-no',
                        dataType: 'HTML',
                        data: {year: year, session_id: session_id, course_id: course_id, batch_id: batch_id},
                        success: function (data) {
                            var data = JSON.parse(data);
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
                            $('#submit').prop( "disabled", false );
                            if(data['message'] !== null && data['message'] !== '')
                            {
                                $('#submit').prop( "disabled", true );
                            }

                        }
                    });

                }

                if(second_ajax_call && course_id && batch_id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/batch-details',
                        dataType: 'HTML',
                        data: {course_id: course_id, batch_id: batch_id},
                        success: function (data) {
                            var data = JSON.parse(data);
                            $('.batch_details').html('');
                            $('.batch_details').html(data['batch_details']);


                        }
                    });
                }
            });

*/
        })
    </script>


@endsection
