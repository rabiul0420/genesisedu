@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>Payment Option</li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <style>
        
    </style>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Doctor Course Payment Option
                        @if($doctor_course->payment_status != "Completed")
                            <a href="{{ url('admin/doctor-course/payments/'.$doctor_course->id) }}" class="btn btn-xs btn-success"> Pay Now</a>
                        @endif
                    </div>
                    
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form method="POST" action="{{ route('doctor_courses.payment.option', $doctor_course->id) }}" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-6">
                                    <div class="input-icon right">
                                        <label class="control-label"  style="font-size: 15px;font-weight:700;">{{ $doctor_course->batch->name }}</label>
                                        <input type="hidden" name="batch_name" value="{{ $doctor_course->batch->name }}" >
                                    </div>
                                </div>                            
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Doctor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-6">
                                    <div class="input-icon right">
                                        <label class="control-label"  style="font-size: 15px;font-weight:700;">{{ $doctor_course->doctor->name .' - '. $doctor_course->reg_no }}</label>
                                        <input type="hidden" name="doctor_course_id" value="{{ $doctor_course->id }}" >
                                    </div>
                                </div>                            
                            </div>

                            <div class="" id="div_payment_option">
                                <div class="form-group ">
                                    <label class="col-md-3 control-label">Payment Option (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3" id="id_div_payment_option">
                                        <label class="radio-inline">
                                            <input type="radio" name="payment_option" required value="default" {{  $doctor_course->payment_option == "default" ? "checked" : '' }}  > Default
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="payment_option" required  value="custom" {{  $doctor_course->payment_option == "custom" ? "checked" : '' }} > Custom
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="payment_option" required  value="single" {{  ($doctor_course->payment_option == "single" || $doctor_course->payment_option == "") ? "checked" : '' }} > Single
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Payment times (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-6">
                                    <div class="input-icon right">
                                        <input type="number" name="payment_times" value="{{ ($doctor_course->payment_option == 'default')?$doctor_course->batch->payment_times:$doctor_course->payment_times }}" {{ ( $doctor_course->payment_option == 'default' || $doctor_course->payment_option == 'single' || $doctor_course->payment_option == '' )?'disabled':''}}>
                                    </div>
                                </div>
                            </div>          

                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                    <a href="{{ url('admin/doctors-courses') }}" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    @if($doctor_course->payment_option == 'custom')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Doctor Course Installment Options
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form
                        action="{{ action('Admin\DoctorsCoursesController@installment_option_save') }}"
                        class="form-horizontal"
                        onsubmit="return installmentOptionSubmit(event)"
                        method="POST"
                    >
                    {{ csrf_field() }}
                    <div class="form-body">

                        <input type="hidden" name="doctor_course_id" value="{{ $doctor_course->id }}" >

                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-9" style="font-size: 20px;" id="showErrorMessage"></div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Doctor Course Price : </label>
                            <div class="col-md-3">
                                <div class="input-icon" style="vertical-align: baseline;padding-top:10px;">
                                    <span class="text text-info"><b>{{ $doctor_course->course_price ?? '0' }} BDT</b></span>
                                </div>
                            </div>
                        </div>
                        @for($k = 1,$l = 0; $k <= $doctor_course->payment_times; $k++,$l++)
                        @php $suff = ($k==1)?'st':(($k==2)?'nd':(($k==3)?'rd':'th')); @endphp
                        <div class="form-group" style="position: relative; overflow: hidden;">
                            <label class="col-md-3 control-label">{{ $k.$suff }} Installment Last Date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input
                                        type="text"
                                        id="payment_date{{ $k }}"
                                        name="payment_date[{{ $k }}]"
                                        value="{{ $doctor_course->payment_options[$l]->payment_date ?? '' }}"
                                        class="form-control input-append date"
                                        autocomplete="off"
                                        required
                                        onchange="checkInstallmentOptionValidation()"
                                    >
                                </div>
                            </div>

                            <label class="col-md-3 control-label">{{ $k.$suff }} Installment target amount (%) (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input
                                        type="number"
                                        id="amount{{ $k }}"
                                        name="amount[{{ $k }}]"
                                        value="{{ $doctor_course->payment_options[$l]->amount ?? '' }}"
                                        class="form-control input-append"
                                        required
                                        oninput="checkInstallmentOptionValidation()"
                                    />
                                </div>
                            </div>

                            @if(isset($doctor_course->payment_options[$l]) && $doctor_paid_percent >= ($doctor_course->payment_options[$l]->amount ?? 0))
                            <div style="position: absolute; inset: 0; background: #ddd6; z-index: 999999;"></div>
                            @endif
                        </div>
                        @endfor          

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/doctors-courses') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>

                    </form>

                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    @endif
    <!-- END PAGE CONTENT-->

@endsection

@section('js')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <script>

        function installmentOptionSubmit(event) {
            const validationResult = checkValidation();

            showErrorMessage.apply(null, validationResult);

            return applyValidation.apply(null, validationResult);
        }

        function checkInstallmentOptionValidation() {
            const validationResult = checkValidation();

            console.log(validationResult);

            return showErrorMessage.apply(null, validationResult);
        }

        checkInstallmentOptionValidation();
        
        function showErrorMessage(status = true, message = '') {
            const showErrorMessage = document.getElementById('showErrorMessage')
            
            showErrorMessage.innerHTML = message;

            showErrorMessage.style.color = status ? 'green' : 'red';
        }

        function applyValidation(status, message = '') {
            if(!status && message)
            { 
                alert(message);
            }

            return status;
        }

        function checkValidation() {
            let status = true;
            let message = "";

            const total = parseInt(`{{ $doctor_course->payment_times }}`);

            let firstAmountPercent = document.getElementById(`amount1`).value;
            let lastAmountPercent = document.getElementById(`amount${total}`).value;

            let prevPaymentDateAsNumber = stringDateToNumber(document.getElementById(`payment_date1`).value);
            let prevAmountPercent = firstAmountPercent;


            function cardinalToOrdinal(number) {
                switch(number) {
                    case 1:
                        return number + 'st';
                    
                    case 2:
                        return number + 'nd';
                    
                    case 3:
                        return number + 'rd';

                    default:
                        return number + 'th';
                }
            }

            function stringDateToNumber(stringDate) {
                return new Date(stringDate).getTime();
            }

            if(firstAmountPercent <= 0) {
                message = `1st Installment target amount (%) must be greater than 0`;

                return [false, message];
            }

            if(lastAmountPercent != 100) {
                message = `${cardinalToOrdinal(total)} Installment target amount (%) must be 100`;

                return [false, message];
            }

            // from second to last
            for(let i = 2; i <= total; i++) {
                let paymentDate = document.getElementById(`payment_date${i}`).value;
                
                let paymentDateAsNumber = stringDateToNumber(paymentDate);

                if(prevPaymentDateAsNumber >= paymentDateAsNumber) {
                    let prevPaymentDate = document.getElementById(`payment_date${i - 1}`).value;

                    message = `${cardinalToOrdinal(i)} Installment Last Date must be greater than ${prevPaymentDate}`;

                    return [false, message];
                }

                prevPaymentDateAsNumber = paymentDateAsNumber;
            }
            
            // from second to 2nd last
            for(let i = 2; i < total; i++) {
                let amountPercent = parseFloat(document.getElementById(`amount${i}`).value);

                if(amountPercent >= 100) {
                    message = `${cardinalToOrdinal(i)} Installment target amount (%) must be less than 100`;

                    return [false, message];
                }

                if(amountPercent <= prevAmountPercent) {
                    message = `${cardinalToOrdinal(i)} Installment target amount (%) must be greater than ${prevAmountPercent}`;

                    return [false, message];
                }

                prevAmountPercent = amountPercent;
            }

            return [status, message];
        }

        $(document).ready(function() {

            $("body").on( "focus", ".date", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    startDate: '',
                    endDate: '',
                }).on('changeDate', function(e){
                    $(this).datepicker('hide');
                });
            })

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-course',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.course').html(data);
                        $('.faculty').html('');
                        $('.subject').html('');
                    }
                });
            })


            $("body").on( "change", "[name='faculty_id']", function() {
                var faculty_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/faculty-subject',
                    dataType: 'HTML',
                    data: {faculty_id: faculty_id},
                    success: function( data ) {
                        $('.subject').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='payment_option']", function() {
                var payment_option = $(this).val();
                if(payment_option == "default")
                {
                    $("[name='payment_times']").prop("disabled", true);
                }
                if(payment_option == "custom")
                {
                    $("[name='payment_times']").prop("disabled", false);
                }
                if(payment_option == "single" || payment_option == '')
                {
                    $("[name='payment_times']").prop("disabled", true);
                }
            });

            $("body").on("change", "[name='package_id']", function(){
                var package_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/shipment',
                    dataType: 'HTML',
                    data: {package_id: package_id},
                    success: function( data ) {
                        $('.shipments').html(data);
                    }
                });
            });
        })
    </script>

@endsection