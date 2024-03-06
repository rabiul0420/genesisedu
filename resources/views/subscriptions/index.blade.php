@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center pt-3">
                        <h2 class="h2 brand_color">Subscriptions</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif
                </div>

                @if($status)
                <div class="col-md-12 mb-2">
                    <div class="d-flex justify-content-between py-2">
                        <a class="btn btn-info" href="{{ route('my.subscriptions.add-subscription') }}">+ Add Subscriptions</a>
                        <a class="btn btn-primary" href="{{ route('my.subscriptions.orders.index') }}">Order List</a>
                    </div>
                </div>

                <hr>

                <div class="col-md-12">
                    <div class="row mx-0" id="contentWrapper">
                        <div class="text-center py-5 bg-white rounded shadow-sm" >
                            <div class="py-2 text-success d-flex justify-content-center align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                <span style="font-size: 24px;" class="px-1">
                                    <b>Congratulations</b>
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div class="py-2">You are eligible to become</div>
                            <div class="py-2">subscriber of "<b>GENESIS Video Library</b>"</div>
                            <a class="mt-4 btn btn-info" href="{{ route('my.subscriptions.add-subscription') }}">+ Add Subscriptions</a>
                        </div>      
                    </div>
                </div>
                @else
                <div class="col-md-12 py-5 bg-white rounded shadow-sm">
                    <div class="text-center">
                        <div class="py-2">Dear Doctor,</div>
                        <div class="py-2">Thanks for your interest.</div>
                        <div class="py-2">Please contact "<b>GENESIS OFFICE</b>"</div>
                        <div class="py-2">For next proccess</div>
                        <a href="{{ route('contactus') }}" class="mt-2 btn btn-info">Next</a>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>


</div>
@endsection

@section('js')
@if($status)
<script>
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer {{ $token }}`,
    };
    const contentWrapper = document.getElementById('contentWrapper');

    function callGroupApi() {
        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/groups`;

        const data = {};
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            if(Object.keys(data.groups).length) {
                contentWrapper.innerHTML = '';

                Object.values(data.groups).forEach((group) => {
                    contentWrapper.innerHTML += `
                        <div class="col-12 px-0 my-2">
                            <div class="h-100 border rounded p-3 bg-white text-center d-flex justify-content-between">
                                <div class="w-100 d-flex flex-wrap pt-2">
                                    <div class="mr-2 mb-2 py-2 px-3 rounded-lg bg-secondary text-white">${group.year}</div>
                                    <div class="mr-2 mb-2 py-2 px-3 rounded-lg bg-secondary text-white">${group.course_name}</div>
                                    <div class="mr-2 mb-2 py-2 px-3 rounded-lg bg-secondary text-white">${group.session_name}</div>
                                    <a href="/my/subscriptions/groups/${group.id}" class="ml-auto mb-2 py-2 px-3 rounded-lg bg-primary text-white">
                                        See All
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
        })
        .catch((error) => {
            console.log(error);
        });
    }

    callGroupApi();
</script>
@endif
@endsection