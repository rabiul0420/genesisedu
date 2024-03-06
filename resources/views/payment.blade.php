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

                        {!! Form::open(['url' => 'payment-create/' . $doctor->id . '/' . $course_info['id'] . '/' . $course_info['include_lecture_sheet'], 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) !!}
                        @if ($course_info->include_lecture_sheet == 1)
                            <div class="col-md-6">

                                <h4>Payable Amount <span class="course_price">{{ $course_info->course_price }}</span>
                                </h4>
                                <h4 class="d-none show_discount">Discount Amount : <span class="course_discount_price"></span>
                                </h4>

                                <h5>Due Amount : <span class="due_price"> {{ $course_info->course_price }} </span>
                                </h5>

                                {{-- min="{{$course_info->course_price/100*$course_info->batch->minimum_payment}}"
                        max="{{$course_info->course_price}}" --}}

                                <input id="amount" type="number" name="amount" required
                                    value="{{ $course_info->course_price }}"
                                    min="{{ ($course_info->course_price / 100) * $course_info->batch->minimum_payment }}"
                                    max="{{ $course_info->course_price }}" class="form-control amount_1" readonly>
                                <table class="table table-striped table-bordered table-hover datatable"
                                    style="display:none">
                                    <thead>
                                        <tr>
                                            <th>Amount</th>
                                            <th><span class="amount">{{ $course_info->course_price }}</span></th>
                                        </tr>
                                        <tr class="charge_row">
                                            <th>Delivery Charge</th>
                                            <th><span name="delevary_charges" class="delevary_charges"></span></th>
                                        </tr>
                                        <tr class="discount_row d-none">
                                            <th>Discount</th>
                                            <th><span name="discount_amount" class="discount_amount"></span></th>
                                        </tr>
                                        <tr>
                                            <th>Total Payable Amount</th>
                                            <th><span class="total_payment text-danger" name="total_payment"></span></th>
                                            <input type="hidden" class="total" id="total" name="total" value="">
                                        </tr>
                                    </thead>
                                </table>


                            </div>


                            <div class="col-md-9 mt-3">
                                <div class="row">
                                    <div class="form-group ">
                                        <label class="col-md-3 control-label">Delivery (<i class="fa fa-asterisk ipd-star"
                                                style="font-size:11px;"></i>) </label>



                                        <label class="radio-inline">
                                            <input type="radio" class="home" name="delevary_status" required
                                                value="1" {{ old('delevary_status') === '1' ? 'checked' : '' }}> Courier
                                            Address
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" class="home" name="delevary_status" required
                                                value="0" {{ old('delevary_status') === '0' ? 'checked' : '' }}> GENESIS
                                            Office
                                            Collection
                                        </label>

                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="price-details address pb-3" style="display:none">
                                            <div class="row" style="margin-top:15px">
                                                <div class="">
                                            <div class="
                                                    courier_address">Courier Billing Address &nbsp;&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="billing_address">

                                            <textarea class="form-control shadow-none mb-3 addrs" rows="2"
                                                name="address">{{ old('address') }}</textarea>
                                            <div class="col pr-0 divis">
                                                @php $divisions->prepend('Select Division', ''); @endphp
                                                {!! Form::select('division_id', $divisions, old('division_id') ? old('division_id') : '', [
    'class' => 'form-control
                                            division_id',
    '',
]) !!}
                                            </div>

                                            <div class="district mt-3">
                                            </div>

                                            <div class="upazila mt-3">
                                            </div>

                                        </div>


                                        <div id="same_as_present" style="display:none">
                                            <div class="form-control shadow-none mb-3"
                                                value="{{ $doctor->present_address }}">
                                                {{ $doctor->present_address ? $doctor->present_address : '' }}</div>
                                            <div class="col form-control pr-0"
                                                value="{{ $doctor->present_division_id }}">
                                                {{ isset($doctor->present_division->name) ? $doctor->present_division->name : '' }}
                                            </div>
                                            <input type="hidden" class="check_dist_id" name="check_dist_id"
                                                value="{{ $doctor->present_district_id }}">

                                            <div class="form-control col pr-0 pl-1 check_dist"
                                                value="{{ $doctor->present_district_id }}">
                                                {{ isset($doctor->present_district->name) ? $doctor->present_district->name : '' }}
                                            </div>

                                            <div class="form-control col pl-1" value="{{ $doctor->present_upazila_id }}">
                                                {{ isset($doctor->present_upazila->name) ? $doctor->present_upazila->name : '' }}
                                            </div>
                                        </div>

                                        <div id="same_as_present" style="display:none">
                                            <div class="form-control shadow-none mb-3"
                                                value="{{ $doctor->present_address }}">
                                                {{ $doctor->present_address ? $doctor->present_address : '' }}</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                    </div>
                @else
                    <div class="col-md-6">

                        <h4>Payable Amount : <span class="course_price">{{ $course_info->course_price }}</span></h4>
                        <h4 class="d-none show_discount">Discount Amount : <span class="course_discount_price"></span>
                        </h4>

                        <h5>Due Amount : <span class="due_price">{{ $course_info->course_price }}</span></h5>


                        <input id="amount" type="number" name="amount" required value="{{ $course_info->course_price }}"
                            min="{{ ($course_info->course_price / 100) * $course_info->batch->minimum_payment }}"
                            max="{{ $course_info->course_price }}" class=" form-control amount_1" readonly>



                        <table class="table table2 table-striped table-bordered table-hover datatable" style="display:none">

                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th><span class="amount">{{ $course_info->course_price }}</span></th>
                                </tr>
                                <tr class="charge_row">
                                    <th>Delivery Charge</th>
                                    <th><span name="delevary_charges" class="delevary_charges"></span></th>
                                </tr>
                                <tr class="discount_row d-none">
                                    <th>Discount</th>
                                    <th><span name="discount_amount" class="discount_amount"></span></th>
                                </tr>
                                <tr>
                                    <th>Total Payable Amount</th>
                                    <th><span class="total_payment text-danger" name="total_payment"></span></th>
                                    <input type="hidden" class="total" id="total" name="total" value="">
                                </tr>
                            </thead>
                        </table>

                    </div>
                    @endif

                    @if ($course_info->discount_old_student == 'No' || $course_info->discount_old_student == null)

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
                                    <a href="{{ url('discount-request/' . $course_info->id) }}"
                                        class="btn btn-xm btn-dark">No</a>
                                </label>
                            </div>
                        </div>

                    @endif


                    <div class="mt-3">
                        <a href="" id="promocode-link" data-id="{{ $course_info->batch_id }}">Have a promo code?</a>
                    </div>

                    <div class="pt-4">
                        <input type="checkbox" name="terms_condition" id="terms_condition" required>
                        <span class="pl-2">I agree to the</span>
                        <span style="cursor: pointer;" class="btn-link" data-toggle="modal"
                            data-target="#exampleModalCenter_terms_and_conditions">terms and conditions</span>
                        <span> & </span>
                        <span style="cursor: pointer;" class="btn-link" data-toggle="modal"
                            data-target="#exampleModalCenter_refund_policy">refund policy.</span>
                    </div>

                    <div class="row d-none" id='promocode'>

                        <div class="form-group col-md-12 my-3 input-icon right">
                            <label class="col-md-3 control-label">Code (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-4 d-flex">
                                {{-- {!! Form::input( 'text', 'coupon_code','',['class'=>'form-control','id'=>'coupon_code']) !!}<i></i> --}}
                                <input type="text" class="form-control" id="discount_code" name="discount_code">
                            </div>
                            <p class="error_msg d-none"></p>
                        </div>
                    </div>

                    {{-- @if (false)
                    <div class="mt-1" id="tr_id_box">
                        <p class="p-2 text-danger">
                            <i>[Bkash Payment করতে checkbox এ click করুন ]</i>
                        </p>
                        <div class="mt-3 w-100">
                            <input type="checkbox" onchange="showField()" name="pament_type" id="pamnet_type"
                                value="true">
                            <label for="pamnet_type">Manual Payment</label>
                        </div>
                        <input type="hidden" value="{{ $course_info->course->site_setup->bkash_number }}"
                            id="hidden_bkash_number">
                    </div> --}}

                    @if ($course_info->batch->is_emi)
                        <!-- <div class="emi my-3">
                            <label class="checkbox-inline">
                                <input type="checkbox" id="checkbox" class="emi_payment" name="emi">
                                EMI( <i> Credit Card only </i>)
                            </label> <br>
                            <a data-toggle="modal" data-target="#myModal" href="">View Details </a>
                        </div> -->
                    @endif

                    <div class="row">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-xm btn-primary btn-block"
                                style="margin: 20px 0;">Submit</button>
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


                </div>


            </div>

            {!! Form::close() !!}
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
            var batch_id = '{{ $course_info['batch_id'] }}';
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
