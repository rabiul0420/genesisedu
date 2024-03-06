<div class="col-md-12 py-3 px-0">

    <table class="card card-body table table-borderless shadow-sm rounded-lg py-2 my-0">
        <tr style="border-bottom: 2px solid #00000040;">
            <th colspan="3" style="width: 100%;">
                <p>
                    Sheet of
                    <span class="text-danger">{{ $doc_info->name ?? ' ' }}</span>
                </p>

            </th>
        </tr>
        <tr>
            <td colspan="3">
                <div class="d-flex justify-content-between align-items-center">


                    @php
                    $step1_class = $step2_class = $step3_class = $step4_class = '';
                    switch( $first_shipment->lecture_sheet_delivery_status ?? '' ) {
                    case 'Completed':
                    $step1_class = $step2_class = $step3_class = $step4_class = 'text-success';
                    break;
                    case 'In_Courier':
                    $step1_class = $step2_class = $step3_class = 'text-success';
                    case 'In_Progress':
                    $step1_class = $step2_class = 'text-success';
                    break;
                    case 'Not_Delivered':
                    $step1_class = 'text-success';
                    break;
                    default:
                    $step1_class = 'text-success';

                    }
                    @endphp


                    <svg style="width: 50px;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="Admitted with LECTURE SHEETS" class="{{ $step1_class }}"
                        xmlns="http://www.w3.org/200 0/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />

                    </svg>

                    <hr class="w-25 border border-dark">
                    <svg style="width: 50px;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="Office Processing Completed" class="{{ $step2_class }}"
                        xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>

                    <hr class="w-25 border border-dark">
                    <svg style="width: 50px;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="Courier Processing Ongoing" class="{{ $step3_class }}" xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>

                    <hr class="w-25 border border-dark">
                    <svg style="width: 50px;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="Received From Courier Services" class="{{ $step4_class }}"
                        xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>

                </div>
            </td>
        </tr>
        <tr>
            <td style="min-width:140px;">Order</td>
            <td>:</td>
            <td style="width: 100%;">{{ $doc_info->created_at ?? ' '}}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Courier</td>
            <td>:</td>
            <td style="width: 100%;">{{$first_shipment->courier->name ?? ''}}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Courier memo no</td>
            <td>:</td>
            <td style="width: 100%;">{{ $first_shipment->courier_memo_no ?? ' '}}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Quantity</td>
            <td>:</td>
            <td style="width: 100%;">{{ $first_shipment->packet ??' ' }}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Payment</td>
            <td>:</td>
            <td style="width: 100%;">{{ $doc_info->payment_status ?? '' }}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Shipment (1st)</td>
            <td>:</td>
            <td style="width: 100%;">{{ $first_shipment->lecture_sheet_delivery_status  ?? ''}}</td>
        </tr>
        <tr>
            @if( isset($first_shipment->lecture_sheet_delivery_status) ?
            (($first_shipment->lecture_sheet_delivery_status == 'In_Courier'
            ||$first_shipment->lecture_sheet_delivery_status == 'Completed') ?? '') : '' )
            <td>Feedback</td>
            <td>:</td>
            @if ($first_shipment->lecture_sheet_delivery_status == 'Completed')
            <td style="font-size: 14px;">লেকচার সীট সংগ্রহ করেছি</td>
            @else
            <form action="{{ route('lecture_sheet_delivery.feedback') }}" method="POST">
                {{ csrf_field() }}
                <td>
                    <input type="hidden" name="lecture_sheet_delivery_status_id" value="{{ $first_shipment->id }}">
                    <select name="feedback" class="form-select" style="max-width: max-content; font-size: 14px;">
                        <option value="" selected>-- Select Your Feedback --</option>
                        <option value="লেকচার সীট সংগ্রহ করেছি"
                            {{ $first_shipment->feedback == 'লেকচার সীট সংগ্রহ করেছি' ? 'selected' : '' }}>
                            লেকচার সীট সংগ্রহ করেছি</option>
                        <option value="এখনো লেকচার সীট পাইনি"
                            {{ $first_shipment->feedback == 'এখনো লেকচার সীট পাইনি' ? 'selected' : '' }}>
                            এখনো লেকচার সীট পাইনি</option>
                        <option value="কুরিয়ার থেকে ফোন পেয়েছি"
                            {{ $first_shipment->feedback == 'কুরিয়ার থেকে ফোন পেয়েছি' ? 'selected' : '' }}>
                            কুরিয়ার থেকে ফোন পেয়েছি</option>
                    </select>
                    <input type="submit" class="btn btn-success mt-3" value="Submit">
                </td>
            </form>
            @endif
            @endif
        </tr>

    </table>

    @if($doc_info->shipment == 2 )

    <table class="card card-body table table-borderless shadow-sm rounded-lg py-2 my-0">
        <tr style="border-bottom: 2px solid #00000040;">
            <th colspan="3" style="width: 100%;">
                <p>
                    Sheet of
                    <span class="text-danger">{{ $doc_info->name ?? ' '}}</span>
                </p>

            </th>
        </tr>
        <tr>
            <td colspan="3">
                <div class="d-flex justify-content-between align-items-center">

                    @php
                    $step1_class = $step2_class = $step3_class = $step4_class = '';

                    switch( $second_shipment->lecture_sheet_delivery_status ?? '' ) {
                    case 'Completed':
                    $step1_class = $step2_class = $step3_class = $step4_class = 'text-success';
                    break;
                    case 'In_Courier':
                    $step1_class = $step2_class = $step3_class = 'text-success';
                    case 'In_Progress':
                    $step1_class = $step2_class = 'text-success';
                    break;
                    case 'Not_Delivered':
                    $step1_class = 'text-success';
                    break;
                    default:
                    $step1_class = 'text-success';

                    }
                    @endphp


                    <svg style="width: 50px;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="Admitted with LECTURE SHEETS" class="{{ $step1_class }}"
                        xmlns="http://www.w3.org/200 0/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>

                    <hr class="w-25 border border-dark">

                    <svg style="width: 50px;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="Office Processing Completed" class="{{ $step2_class }}"
                        xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>

                    <hr class="w-25 border border-dark">

                    <svg style="width: 50px;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="Courier Processing Ongoing" class="{{ $step3_class }}" xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>

                    <hr class="w-25 border border-dark">

                    <svg style="width: 50px;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        title="Received From Courier Services" class="{{  $step4_class }}"
                        xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>

                </div>
            </td>
        </tr>
        <tr>
            <td style="min-width:140px;">Order</td>
            <td>:</td>
            <td style="width: 100%;">{{ $doc_info->created_at ?? ' '}}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Courier</td>
            <td>:</td>
            <td style="width: 100%;">{{$second_shipment->courier->name ?? ''}}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Courier memo no</td>
            <td>:</td>
            <td style="width: 100%;">{{ $second_shipment->courier_memo_no ?? ' '}}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Quantity</td>
            <td>:</td>
            <td style="width: 100%;">{{ $second_shipment->packet ??' ' }}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Payment</td>
            <td>:</td>
            <td style="width: 100%;">{{ $doc_info->payment_status ?? '' }}</td>
        </tr>
        <tr>
            <td style="min-width:140px;">Shipment (2nd)</td>
            <td>:</td>
            <td style="width: 100%;">{{ $second_shipment->lecture_sheet_delivery_status ?? ' '}}
            </td>
        </tr>
        <tr>
            @if( isset($second_shipment->lecture_sheet_delivery_status) ?
            (($second_shipment->lecture_sheet_delivery_status == 'In_Courier'
            ||$second_shipment->lecture_sheet_delivery_status == 'Completed') ?? '') : '' )
            <td>Feedback</td>
            <td>:</td>
            @if ($second_shipment->lecture_sheet_delivery_status == 'Completed')
            <td style="font-size: 14px;">লেকচার সীট সংগ্রহ করেছি</td>
            @else
            <form action="{{ route('lecture_sheet_delivery.feedback') }}" method="POST">
                {{ csrf_field() }}
                <td>
                    <input type="hidden" name="lecture_sheet_delivery_status_id" value="{{ $second_shipment->id }}">
                    <select name="feedback" class="form-select" style="max-width: max-content; font-size: 14px;">
                        <option value="" selected>-- Select Your Feedback --</option>
                        <option value="লেকচার সীট সংগ্রহ করেছি"
                            {{ $second_shipment->feedback == 'লেকচার সীট সংগ্রহ করেছি' ? 'selected' : '' }}>
                            লেকচার সীট সংগ্রহ করেছি</option>
                        <option value="এখনো লেকচার সীট পাইনি"
                            {{ $second_shipment->feedback == 'এখনো লেকচার সীট পাইনি' ? 'selected' : '' }}>
                            এখনো লেকচার সীট পাইনি</option>
                        <option value="কুরিয়ার থেকে ফোন পেয়েছি"
                            {{ $second_shipment->feedback == 'কুরিয়ার থেকে ফোন পেয়েছি' ? 'selected' : '' }}>
                            কুরিয়ার থেকে ফোন পেয়েছি</option>
                    </select>
                    <input type="submit" class="btn btn-success mt-3" value="Submit">
                </td>
            </form>
            @endif
            @endif
        </tr>

    </table>
    @endif
</div>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"></script>
    <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    </script>               