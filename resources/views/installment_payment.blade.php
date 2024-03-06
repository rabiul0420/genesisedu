@extends('layouts.app')


@section('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <style>
        .class-details {
            color: gray;
            border: none;
            background: none;
            outline: none;
        }

        .class-details:hover {
            color: green;
        }

    </style>

@endsection
@section('content')

    <div class="container">

        <div class="row">

            @include('side_bar')
            <div class="col-md-9 col-md-offset-0">

                <div class="row panel panel-default">
                
                <div class="row col-md-12">
                    <div class="row">
                    @if(Session::has('message'))
                        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                            <p> {{ Session::get('message') }}</p>
                        </div>
                    @endif
                    </div>

                    <!-- {!! Form::open(['url' => 'payment-create/' . $doctor_course->doctor->id . '/' . $doctor_course->id . '/' . $doctor_course->include_lecture_sheet, 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) !!} -->
                    {!! Form::open(['url' => 'payment-manual-installment/'.$doctor_course->id, 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) !!}
                    {{ csrf_field() }}
                    
                    @php $payable_amount = 0; @endphp
                    <div class="row">
                        
                        @if($doctor_course->installments() !== null && $doctor_course->payment_option != 'single' )                        
                        <div> Installment Details : <br>
                            <table class="table table-striped table-bordered table-hover userstable datatable dataTable no-footer">
                                <tr style="background-color:darkgray;color:white;">
                                    <th>Installment No</th><th>Installment Last Date</th><th>Installment Amount</th><th>CURRENCY</th>
                                </tr>
                                
                                @foreach($doctor_course->installments() as $k=>$installment)
                                <tr <?php echo $doctor_course->check_paid_installment($k)?'style="background-color:green;color:white;"':'style="background-color:red;color:white;"'?> >
                                    <td>{{ $k + 1 }}</td><td>{{ Date("jS F - Y",date_create_from_format('Y-m-d',$installment->payment_date)->getTimestamp()) }}</td><td>{{ $doctor_course->installment_gap($k-1,$k) }}</td><td>BDT</td>
                                </tr>
                                @endforeach                              
                            </table>
                        </div>                        
                        @endif  
                        
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Pay Amount (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)  </label>
                            <div class="col-md-6">
                                <input type="hidden" class="total" id="total" name="total" value="">
                                <input type="number" name="amount" class="form-control" required value="{{ $doctor_course->installment_payable_amount() }}" readonly min="0" max="{{ $doctor_course->installment_payable_amount() }}"><br>                                       
                            </div>

                        </div>
                    </div>

                    @if ($doctor_course->discount_old_student == 'No' || $doctor_course->discount_old_student == null)
                    <div class="row">
                        <div class="form-group">    
                            <div class="row my-3 auto_adjust request">
                                <div class="col-sm-6 d-flex justify-content-between align-items-center"
                                    style="border:3px solid rgb(206, 206, 6); padding: 7px 6px;">
                                    <label class="control-label">Discount Auto Adjusted?
                                        <span onclick="alert(`@include('title')`)">
                                            @include('batch_schedule.info-icon')
                                        </span>
                                    </label>
                                    <label class="radio-inline">
                                        <a class="btn btn-xm btn-success auto_adjust_yes"> Yes</a>
                                    </label>
                                    <label class="radio-inline">
                                        <a href="{{ url('discount-request/' . $doctor_course->id) }}"
                                            class="btn btn-xm btn-dark">No</a>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ url('promo-code/' . $doctor_course->id) }}" id="" style="color:slateblue;font-size:19px;font-weight:700;">Have a promo code?</a>
                    </div>

                    <div class="row">
                        <div class="pt-4">
                            <input type="checkbox" name="terms_condition" id="terms_condition" required>
                            <span class="pl-2">I agree to the</span>
                            <span style="cursor: pointer;" class="btn-link" data-toggle="modal"
                                data-target="#exampleModalCenter_terms_and_conditions">terms and conditions</span>
                            <span> & </span>
                            <span style="cursor: pointer;" class="btn-link" data-toggle="modal"
                                data-target="#exampleModalCenter_refund_policy">refund policy.</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-xm btn-primary btn-block"
                                style="margin: 20px 0;">Pay</button>
                        </div>


                        <!-- Modal -->
                        <div class="modal fade" id="myModal" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <!-- <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div> -->
                                    <div class="modal-body">
                                        <p class="text">EMI Tenure: 3,6 months.<br>
                                            Eligiable cards
                                            <br> 1. VISA CARD
                                            <br> 2. MASTER CARD.
                                            <br> 3. AMEX CARD.<br>

                                            <img class="img-1" src="{{ asset('img/emi.jpg') }}" alt=""
                                                width="460" height="520"> <br>

                                            Note: EMI is only applicable for purchases through Credit Card.
                                        </p> <br>
                                        <div class="close_button">
                                            <button type="button" class="btn btn-success"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div> Payment Details : <br>
                            <table class="table table-striped table-bordered table-hover userstable datatable dataTable no-footer">
                                <tr>
                                    <th>Name : </th><th colspan="3">{{ ($doctor_course->doctor->name.' - '.$doctor_course->reg_no) ?? "" }}</th>
                                </tr>
                                <tr>
                                    <th>Batch : </th><th colspan="3">{{ ($doctor_course->batch->name) ?? "" }}</th>
                                </tr>
                                <tr>
                                    <th></th><th colspan="3"> </th>
                                </tr>
                                <tr>
                                    <th>Serial No</th><th>Details</th><th>Amount</th><th>CURRENCY</th>
                                </tr>
                                @php $k=0; @endphp
                                <tr>
                                    <td>{{ ++$k }}</td><td>Admission Fee</td><td>{{ $doctor_course->actual_course_fee() ?? '0'}}</td><td>BDT</td>
                                </tr>
                                @if(isset($doctor_course->batch->is_show_lecture_sheet_fee) && $doctor_course->batch->is_show_lecture_sheet_fee == "Yes" && $doctor_course->include_lecture_sheet)
                                <tr>
                                    <td>{{ ++$k }}</td><td>Lecture Sheet Fee</td><td>{{ $doctor_course->lecture_sheet_fee() ?? '0'  }}</td><td>BDT</td>
                                </tr>

                                <tr>
                                    <td>{{ ++$k }}</td><td>Lecture Sheet Courier Charge</td><td>{{ $doctor_course->courier_charge() ?? '0' }}</td><td>BDT</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>{{ ++$k }}</td><td>Discount From Previous Admission</td><td>{{ $doctor_course->discount_from_prev_admission() ?? '0'}}</td><td>BDT</td>
                                </tr>
                                @if($doctor_course->discount_codes() !== null)
                                @foreach($doctor_course->discount_codes() as $discount_code)
                                <tr>
                                    <td>{{ ++$k }}</td><td>Coupon Discount ( {{ $discount_code->discount_code }} )</td><td>{{ $discount_code->amount ?? '0'}}</td><td>BDT</td>
                                </tr>
                                @endforeach
                                @endif
                                <style>
                                    .cbold
                                    {
                                        font-size: 15px;
                                        font-weight: 700;
                                    }
    
                                </style>
                                <tr class="cbold">
                                    <td colspan="2" style="text-align:right;">Total Course Fee</td><td>{{ $doctor_course->course_price ?? '0' }}</td><td>BDT</td>
                                </tr>

                                <tr class="cbold">
                                    <td colspan="2" style="text-align:right;">Paid</td><td>{{ $doctor_course->paid_amount() ?? '0' }}</td><td>BDT</td>
                                </tr>

                                <tr class="cbold">
                                    <td colspan="2" style="text-align:right;">Due</td><td>{{ ($doctor_course->course_price - $doctor_course->paid_amount()) > 0 ? ($doctor_course->course_price - $doctor_course->paid_amount()) : '0' }}</td><td>BDT</td>
                                </tr>
                                
                            </table>
                        </div>
                        
                    </div>

                    <div class="row">
                        
                        @if($doctor_course->installments() !== null && $doctor_course->payment_option != 'default' )                        
                        <div> Installment Details : <br>
                            <table class="table table-striped table-bordered table-hover userstable datatable dataTable no-footer">
                                <tr>
                                    <th>Installment No</th><th>Installment Last Date</th><th>Installment Target Amount (%)</th><th>Installment Target Amount</th><th>CURRENCY</th>
                                </tr>
                                @foreach($doctor_course->installments() as $k=>$installment)
                                <tr>
                                    <td>{{ ++$k }}</td><td>{{ Date("jS F - Y",date_create_from_format('Y-m-d',$installment->payment_date)->getTimestamp()) }}</td><td>{{ $installment->amount }} %</td><td>{{ ($installment->amount * $doctor_course->course_price)/100 }}</td><td>BDT</td>
                                </tr>
                                @endforeach                              
                            </table>
                        </div>                        
                        @endif  
                        
                    </div>

                    {!! Form::close() !!}

                    <div class="row">
                        
                        @if($doctor_course->payments() !== null)                        
                        <div> Payment History : <br>
                            <table class="table table-striped table-bordered table-hover userstable datatable dataTable no-footer">
                                <tr>
                                    <th>Serial No</th><th>Date</th><th>Trx ID</th><th>Note</th><th>Amount</th><th>CURRENCY</th>
                                </tr>
                                @foreach($doctor_course->payments() as $k=>$payment)
                                <tr>
                                    <td>{{ ++$k }}</td><td>{{ Date("jS F - Y, g:i a",$payment->created_at->timestamp) }}</td><td>{{ $payment->trans_id }}</td><td>{{ $payment->note }}</td><td>{{ $payment->amount }}</td><td>BDT</td>
                                </tr>
                                @endforeach                              
                            </table>
                        </div>                        
                        @endif  
                        
                    </div>


                </div>


            </div>

            
        </div>

    </div>
    </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter_terms_and_conditions" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle_terms_and_conditions" aria-hidden="true">
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

                    .terms_and_conditions b {
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
    <div class="modal fade my-5" id="batchDetails" tabindex="-1" role="dialog" aria-labelledby="cashPament"
        aria-hidden="true">
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
    <div class="modal fade" id="exampleModalCenter_refund_policy" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle_refund_policy" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle_refund_policy">Refund policy</h5>
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
    </div>

@endsection

@section('js')
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="script.js"></script>
    {{-- <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('.class-details'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script> --}}

    <script>
        tippy('#checkbox', {
            content: "EMI offers are applicable for Selected Credit Cards only.",
            placement: 'top-start',
            arrow: true,
            arrowtype: 'round',
            Animation: 'scale',
            themes: 'light-border'
        });
    </script>

    <script>
        // setInterval(function(){
        //     $('.request').each(function(){
        //     $(this).hide().load('tx.php').fadeIn('2000');
        //     $(this).removeClass('d-none');
        //     });
        // }, 2000);

        $('.auto_adjust_yes').on("click", function(e) {
            $('.auto_adjust').addClass('d-none');
            // e.preventDefault();
            // $('.auto_adjust').removeClass('d-none');
        })

        $("#checkbox").on("click", function(e) {
            var checkBox = $(this).is(":checked");
            console.log(checkBox)
            if (checkBox == true) {
                $('.emi_show').removeClass('d-none');
            } else {
                $('.emi_show').addClass('d-none');
            }
        });
        $("#promocode-link").on("click", function(e) {
            e.preventDefault();
            $("#promocode").toggleClass('d-flex');
            $(".table").show();
            amountInput.type = 'hidden'
        });
        discount_amount = 0;
        delevary_charges = 0;

        function calculateAndView() {
            var amount = Number($('.course_price').html());
            var total_payment = amount - Number(discount_amount) + Number(delevary_charges);
            if (Number(discount_amount) > 0) {
                $('.discount_amount').text(discount_amount);
                $('.show_discount').removeClass('d-none');
                $('.show_discount .course_discount_price').text(discount_amount);
            }
            $('.total_payment').text(total_payment);
            $('.total').val(total_payment);
            $('#amount').val(total_payment);
            $('.due_price').text(total_payment);
            amountInput.min = total_payment;
        }
        $("#discount_code").on("input", function(e) {
            e.preventDefault();
            var batch_id = $('#promocode-link').data('id');
            console.log(batch_id)
            var discount_code = $(this).val();
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
                    url: '/apply-discount-code',
                    dataType: 'JSON',
                    data: {
                        batch_id,
                        discount_code
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.valid == true) {
                            console.log('ok', data);
                            var amount = $('.amount_1').val();
                            discount_amount = data.amount;
                            calculateAndView();
                            $('.discount_row').removeClass('d-none')
                            $(".error_msg").addClass('d-none');
                        } else {
                            console.log('eror', data);
                            $(".error_msg").removeClass('d-none');
                            $(".error_msg").addClass('text-danger mt-2');
                            $(".error_msg").text('Your code is invalid');
                        }
                    },
                    error: function() {

                    }
                });
            } else {
                $("#discount_code").addClass('is-invalid')
            }
        });


        var amountInput = document.getElementById('amount')


        $(".amount_1").keyup(function() {
            var amount = $(this).val();
            $(".amount").text(amount);
            var delevary_charges = $('.delevary_charges').text();
            if (delevary_charges) {
                var total_payment = $('.total_payment').text(parseInt(amount) + parseInt(delevary_charges));
                var total = $('.total').val(parseInt(amount) + parseInt(delevary_charges));
            }
        });
        $("body").on("change", "[name='delevary_status']", function() {
            var delevary_status = $(this).val();
            if (delevary_status == 1) {
                $("div.address").show();
                $(".table").show();
                $('.addrs').attr('required', true);
                $('.divis').attr('required', true);
                $('.division_id').attr('required', true);
                amountInput.type = 'hidden'
            } else {
                $("div.address").hide();
                $(".table").hide();
                $('.addrs').attr('required', false);
                $('.divis').attr('required', false);
                $('.division_id').attr('required', false);
                amountInput.type = 'number'
            }
        });

        function myFunction() {
            var checkBox = document.getElementById("bil__ling");
            var same_as_present = document.getElementById("same_as_present");
            if (checkBox.checked == true) {
                var present_dist_id = $(".check_dist_id").val();
                if (present_dist_id == 1) {
                    $('.delevary_charges').text(200);
                } else {
                    $('.delevary_charges').text(250);
                }
                var delevary_charges = $('.delevary_charges').text();
                var amount = $(".amount").text();
                var total_payment = $('.total_payment').text(parseInt(amount) + parseInt(delevary_charges));
                var total = $('.total').val(parseInt(amount) + parseInt(delevary_charges));
                $('.delevary_charges').show();
                $('.total_payment').show();
                same_as_present.style.display = "block";
                $("div.billing_address").hide();
                $('.addrs').attr('required', false);
                $('.division_id').attr('required', false);
            } else {
                $('.delevary_charges').hide();
                $('.total_payment').hide();
                same_as_present.style.display = "none";
                $("div.billing_address").show();
                $('.addrs').attr('required', true);
                $('.division_id').attr('required', true);
            }
        }
        $("body").on("change", "[name='division_id']", function() {
            var division_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/division-district',
                dataType: 'HTML',
                data: {
                    division_id: division_id
                },
                success: function(data) {
                    $('.district').html(data);
                    $('.upazila').html('');
                }
            });
        });
        $("body").on("change", "[name='district_id']", function() {
            var district_id = $(this).val();
            var batch_id = '{{ $doctor_course['batch_id'] }}';
            //alert(total)
            // if (district_id == 1) {
            //     $('.delevary_charges').text(200);
            // } else {
            //     $('.delevary_charges').text(250);
            // }
            // var discount = $('.discount_amount').text();
            // var discount_amount = Number(discount);console.log(discount_amount);
            // delevary_charges = $('.delevary_charges').text();
            // calculateAndView();
            // var amount = $(".amount").text();
            // var total_payment = $('.total_payment').text(parseInt(amount) + parseInt(delevary_charges) - parseInt(discount_amount));
            // var total = $('.total').val(parseInt(amount) + parseInt(delevary_charges) - parseInt(discount_amount));

            amountInput.style.display = 'none'
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/district-upazila',
                dataType: 'JSON',
                data: {
                    district_id: district_id,
                    batch_id: batch_id
                },
                success: function(data) {
                    $('.upazila').html(data.upazila);
                    $('.delevary_charges').html(data.courier);
                    var discount = $('.discount_amount').text();
                    var discount_amount = Number(discount);
                    console.log(discount_amount);
                    delevary_charges = $('.delevary_charges').text();
                    calculateAndView();
                    var amount = $(".amount").text();
                    var total_payment = $('.total_payment').text(parseInt(amount) + parseInt(
                        delevary_charges) - parseInt(discount_amount));
                    var total = $('.total').val(parseInt(amount) + parseInt(delevary_charges) -
                        parseInt(discount_amount));
                }
            });
        });


        function showField() {
            let bkash_no = document.getElementById('hidden_bkash_number').value;
            let checkId = document.getElementById('pamnet_type')
            let trIdBox = document.getElementById('tr_id_box')
            if (checkId.checked) {
                trIdBox.innerHTML = '<input type="text" name="tr_id" required placeholder="Trx ID" class="form-control">' +
                    '<p class="pt-2 text-danger">Bkash Marchant No : <span>' + bkash_no + '</span></p>'
            } else {
                trIdBox.innerHTML = '<p class="p-2 text-danger"><i>[Bkash Payment করতে checkbox এ click করুন ]</i></p>'
            }
        }
    </script>



@endsection
