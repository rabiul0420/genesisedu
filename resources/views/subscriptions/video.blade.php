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
                
                <div class="col-md-12">
                    <div class="d-flex justify-content-between py-2">
                        <a class="btn btn-secondary" href="{{ route('my.subscriptions.index') }}">
                            <b>&#8592;</b> Back to Subscriptions
                        </a>
                    </div>
                </div>

                <hr>

                <div hidden style="position: relative; width: 100%; padding-top: 56.25%;" id="videoPlayerContainer">
                    <div style="position: absolute; inset: 0;">
                        <iframe hidden id="videoPlayer" width="100%" height="100%" src="" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
                    </div>
                </div>

                <div class="bg-white shadow-sm">
                    <h5 hidden class="pt-3 pb-1 px-3" id="videoTitle"></h5>
                    <h5 hidden class="pb-3 pt-1 px-3 text-danger" id="videoPassword"></h5>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="row mx-0" id="contentWrapper">
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

    let responseSubscriptionItems;
    const contentWrapper = document.getElementById('contentWrapper');
    const videoPlayer = document.getElementById('videoPlayer');
    const videoTitle = document.getElementById('videoTitle');
    const videoPassword = document.getElementById('videoPassword');
    const videoPlayerContainer = document.getElementById('videoPlayerContainer');

    function callGroupApi() {
        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/groups/{{ $group_id }}`;

        const data = {};
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            const { group } = data;

            contentWrapper.innerHTML = '';

            contentWrapper.innerHTML += `
                <div hidden class="col-12 px-0 my-2">
                    <div class="h-100 border rounded p-3 bg-white text-center d-flex justify-content-between">
                        <div class="w-100 d-flex flex-wrap pt-2">
                            <div class="mr-2 mb-2 py-2 px-3 rounded-lg bg-secondary text-white">${group.year}</div>
                            <div class="mr-2 mb-2 py-2 px-3 rounded-lg bg-secondary text-white">${group.course_name}</div>
                            <div class="mr-2 mb-2 py-2 px-3 rounded-lg bg-secondary text-white">${group.session_name}</div>
                        </div>
                    </div>
                </div>
                <div class="p-0" id="subscriptionContainer_${group.id}">
                </div>
            `;

            callSubscriptionApi(group.id);
        })
        .catch((error) => {
            console.log(error);
        });
    }

    function callSubscriptionApi(groupId) {
        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/groups/${groupId}`;

        const data = {};
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            if(Object.keys(data.subscriptions).length) {
                responseSubscriptionItems = data.subscriptions;

                document.getElementById(`subscriptionContainer_${data.group.id}`).innerHTML = '';

                Object.values(data.subscriptions).forEach((subscription) => {
                    document.getElementById(`subscriptionContainer_${data.group.id}`).innerHTML += `
                        <div class="d-flex justify-content-between align-items-center mb-3 py-2 px-3 rounded-lg bg-white">
                            <div class="w-100">
                                <div class="py-2">${subscription.name}</div>
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="pl-1" id="subscription__${subscription.id}">...</div>
                                </div>
                            </div>
                            <svg onclick="playVideo(${subscription.id})" class="text-danger" style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    `;

                    countdown(`subscription__${subscription.id}`, subscription.expired_at);
                });
            }
        })
        .catch((error) => {
            console.log(error);
        });
    }

    callGroupApi();

    function countdown(subscriberId, expiredAt) {
        let countDownDate = new Date(expiredAt).getTime();

        let x = setInterval(function() {
            let now = new Date().getTime();
            let distance = countDownDate - now;

            if (distance <= 0) {
                clearInterval(x);
                window.location.reload();
            }

            let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            let finalString = (days > 0 ? (days + "d ") : "") + String(hours).padStart(2, '0') + ":" + String(minutes).padStart(2, '0').padStart(2, '0') + ":" + String(seconds).padStart(2, '0');

            document.getElementById(subscriberId).innerHTML = `
                <span class="${days > 0 ? '' : 'text-danger'} border px-2 py-1 rounded-lg">
                    ${finalString}
                </span>
            `;
        }, 1000);
    }

    function playVideo(subscriptionId) {
        const subscription = responseSubscriptionItems.find((subscription) => subscription.id == subscriptionId);

        videoPlayer.src = subscription.link;
        videoPlayer.hidden = false;
        videoTitle.hidden = false;
        videoPassword.hidden = false;
        videoPlayerContainer.hidden = false;

        videoTitle.innerHTML = subscription.name;
        videoPassword.innerHTML = `Password: ${subscription.password}`;

        window.scrollTo(0, 0);
    }
</script>
@endsection