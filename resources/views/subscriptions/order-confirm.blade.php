@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center pt-4 pb-2">
                        <h2 class="h2 brand_color">Order of Subscription</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif
                </div>
                
                <div class="col-md-12">
                    <div class="d-flex justify-content-between py-2">
                        <a class="btn btn-secondary" href="{{ route('my.subscriptions.available', [$year, $course->id, $session->id ]) }}?{{ request()->getQueryString() }}">
                            <b>&#8592;</b> Back
                        </a>
                    </div>
                </div>

                <hr>

                <div>
                    <input type="hidden" name="faculty_id" value="{{ $faculty->id ?? '' }}" />
                    <input type="hidden" name="discipline_id" value="{{ $discipline->id ?? '' }}" />
                    @foreach ($items as $item)
                    <input type="hidden" name="items[]" value="{{ $item }}" />
                    @endforeach
                    <div class="row mb-3">
                        <div class="col-md-4 my-1">
                            <div hidden class="form-control text-center cursor-pointer" 
                                onclick="
                                    document.getElementById('infoContainer').hidden = ! document.getElementById('infoContainer').hidden;
                                    this.innerHTML = document.getElementById('infoContainer').hidden ? 'Show Information' : 'Hide Information';
                                "
                            >
                                Show Information
                            </div>
                        </div>
                        <div class="col-md-4 my-1">
                            <div class="form-control text-center">
                                <b id="totalAmount"></b>
                                of <b id="totalItem">{{ count($items ?? []) ?? 0 }}</b> Item
                            </div>
                        </div>
                        <div class="col-md-4 my-1">
                            <button type="button" id="orderConfirmButton" class="btn btn-info w-100" disabled>
                                Confirm Order
                            </button>
                        </div>
                    </div>
                    <hr class="mt-0">
                    <div style="font-size: 24px;" class="d-flex align-items-center text-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="pl-1">Please Select <b>Duration</b></span>
                    </div>
                    <div hidden id="infoContainer">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;">
                            <div class="w-auto form-control text-center">{{ $course->name ?? '' }}</div>
                            <div class="w-auto form-control text-center">{{ $year }}</div>
                            <div class="w-auto form-control text-center">{{ $session->name ?? '' }}</div>
                            @if($faculty)
                            <div class="w-auto form-control text-center">{{ $faculty->name ?? '' }}</div>
                            @endif
                            @if($discipline)
                            <div class="w-auto form-control text-center">{{ $discipline->name ?? '' }}</div>
                            @endif
                        </div>

                        <hr class="mb-0">
                    </div>
                    <div class="row mb-3 pt-2" id="durationSelectorContainer">
                    </div>
                </div>
            </div>
        </div>

    </div>


</div>
@endsection

@section('js')
<script>
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer {{ $token }}`,
    };

    const items = `@json($items)`;
    const totalPrice = `{{ $total_price ?? 0 }}`;

    const durationSelectorContainer = document.getElementById('durationSelectorContainer');
    const orderConfirmButton = document.getElementById('orderConfirmButton');
    const totalAmount = document.getElementById('totalAmount');

    function callDurationApi(itemsCount) {
        // loading('course', true);

        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/durations`;

        const data = {
            items_count: `{{ $items_count ?? 0 }}`,
        };
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            Object.values(data.durations).forEach((duration) => {
                const durationSelector = document.createElement('div');
                durationSelector.className = 'col-6 col-md-3 my-1';
                durationSelector.innerHTML = `
                    <label class="p-2 w-100 h-100 bg-white rounded-lg border position-relative" style="overflow: hidden;">
                        <div class="text-right">
                            <input onchange="selectDuration(${duration.price_factor})" name="duration_in_day" value="${duration.duration_in_day}" type="radio" class="form-check-input border border-primary" />
                        </div>
                        <div class="text-center mt-3 mb-4">
                            <span class="display-3">${duration.duration_in_day}</span>
                            <span>Days</span>
                        </div>
                        <div ${duration.save_parcentage ? '' : 'hidden'} class="text-center my-2 position-absolute px-5 py-1 bg-info text-white rounded-lg" style="left: -48px; top: 8px; transform: rotate(-45deg)">
                            Save ${duration.save_parcentage}%
                        </div>
                    </label>
                `;
                durationSelectorContainer.appendChild(durationSelector);
            });
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            // loading('course', false);  
        });
    }

    function callOrderApi(durationInDay) {
        // loading('course', true);

        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/orders`;

        const data = {
            year: `{{ $year ?? '' }}`,
            course_id: `{{ $course->id ?? '' }}`,
            session_id: `{{ $session->id ?? '' }}`,
            faculty_id: `{{ $faculty->id ?? '' }}`,
            discipline_id: `{{ $discipline->id ?? '' }}`,
            duration_in_day: durationInDay,
            items: JSON.parse(items),
        };
        
        axios.post(url, 
            data,
            {
                headers
            }
        )
        .then(({data}) => {
            window.location.href = `{{ route('my.subscriptions.orders.index') }}/${data.order.id}`;
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            // loading('course', false);  
        });
    }

    callDurationApi(`{{ $items_count ?? 0 }}`);

    orderConfirmButton.addEventListener('click', () => {
        const durationInDay = document.querySelector('input[name="duration_in_day"]:checked').value;
        callOrderApi(durationInDay);
    });

    function selectDuration(priceFactor) {
        orderConfirmButton.disabled = false;
        let finalPayableAmount = Math.floor(totalPrice * priceFactor);
        totalAmount.innerHTML = `${finalPayableAmount} TK`;
    }

</script>
@endsection
