@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center pt-3">
                        <h2 class="h2 brand_color">Subscription Orders</h2>
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
                        <a class="btn btn-secondary" href="{{ route('my.subscriptions.index') }}">
                            <b>&#8592;</b> Back to Subscriptions
                        </a>
                    </div>
                </div>

                <hr>

                <div class="col-md-12">
                    <div class="row" id="contentWrapper">    
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

    const contentWrapper = document.getElementById('contentWrapper');

    function callOrderListApi() {
        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/orders`;

        const data = {};
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            Object.values(data.orders).forEach((order) => {
                contentWrapper.innerHTML += `
                    <div class="col-md-4 p-2">
                        <div class="h-100 border rounded p-3 bg-white text-center" style="overflow: hidden; position: relative;">
                            <div style="position: absolute; top: 9px; right: -27px; transform: rotate(45deg); width: 100px;">
                                <div>
                                    <div 
                                        class="${order.payment_status ? 'bg-success' : 'bg-danger'} text-white text-center w-100" 
                                        style="padding: 4px 0; font-size: 14px;"
                                    >
                                        ${order.payment_status ? 'Paid' : 'Unpaid'}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="my-3 d-flex justify-content-center align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 11V9a2 2 0 00-2-2m2 4v4a2 2 0 104 0v-1m-4-3H9m2 0h4m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="d-flex align-items-end">
                                        <span style="font-size: 32px; padding: 0 3px;">
                                            ${order.payable_amount}
                                        </span>
                                        <span class="py-1">TK</span>
                                    </div>
                                </div>
                                <div class="my-3 d-flex justify-content-center align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div class="d-flex align-items-end">
                                        <span style="font-size: 32px; padding: 0 3px;">
                                            ${order.duration_in_day}
                                        </span>
                                        <span class="py-1">Days</span>
                                    </div>
                                </div>
                                <div class="my-3 d-flex justify-content-center align-items-end">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                    </svg>

                                    <div class="d-flex align-items-end">
                                        <span style="font-size: 16px; padding: 0 3px;">
                                            ${order.expired_at || 'Pending...'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div style="display: flex; justify-content: center; gap: 12px;">
                                <a class="btn btn-sm btn-sm btn-outline-info w-50" href="/my/subscriptions/orders/${order.id}">Details</a>
                            </div>
                        </div>
                    </div>
                `;
            });
        })
        .catch((error) => {
            console.log(error);
        });
    }

    callOrderListApi();

</script>
@endsection
