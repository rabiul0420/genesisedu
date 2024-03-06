@extends('tailwind.layouts.admin')

@section('content')

<div class="grid gap-4 md:grid-cols-3 2xl:grid-cols-5 print:grid-cols-3">
    <!-- <a class="block w-full px-3 py-2 border border-sky-400 text-sky-600 rounded text-center hover:bg-sky-600 hover:text-white" href="{{ route('subscribers.index') }}">Subscriber List</a> -->
    <select id="paymentStatusFilter" onchange="search()" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0">
        <option value=""> -- All Order -- </option>
        <option value="1" {{ request()->payment_status == '1' ? 'selected' : '' }}> Paid Order </option>
        <option value="0" {{ request()->payment_status == '0' ? 'selected' : '' }}> Unpaid Order </option>
    </select>
    <div class="flex items-center gap-2">
        <label class="shrink-0 grow-0" for="">From</label>
        <input type="date" id="fromFilter" onchange="search()" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0">
    </div>
    <div class="flex items-center gap-2">
        <label class="shrink-0 grow-0" for="">To</label>
        <input type="date" id="toFilter" onchange="search()" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0">
    </div>
    <input id="search" type="search" oninput="search()" value="{{ request()->search ?? '' }}" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" placeholder="Search" />
    <button type="button" onclick="download(`excel`)" class="flex justify-center items-center px-3 py-2 border bg-blue-500 text-white rounded focus:outline-0">
        Excel Download
    </button>
</div>

<hr class="my-3">

<div id="dataContainer">

</div>

<script>
    const searchInput = document.getElementById('search');

    function search(page = 1) {
        document.getElementById('dataContainer').innerHTML = `
            <div class="flex justify-center items-center h-96 text-sky-600">
                <span class="text-xl md:text-4xl">Loading...</span>
            </div>
        `;

        const search = searchInput ? searchInput.value : '';
        const paymentStatus = document.getElementById('paymentStatusFilter').value;
        const from = document.getElementById('fromFilter').value;
        const to = document.getElementById('toFilter').value;

        setUrl(page, search, { payment_status: paymentStatus });

        axios.get(`/admin/subscriptions?search=${search}`, {
                params: {
                    page,
                    search,
                    payment_status: paymentStatus,
                    from,
                    to,
                },
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
            .then(function (response) {
                document.getElementById('dataContainer').innerHTML = response.data;
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    search();
    
    function download(type) {
        const from = document.getElementById('fromFilter').value;
        const to = document.getElementById('toFilter').value;
        const search = searchInput ? searchInput.value : '';

        return window.location.href = `{{ route('subscriptions.download') }}/${type}?from=${from}&to=${to}&search=${search}`;
    }

    function setUrl(page, search, filters) {
        const url = new URL(window.location.href);

        url.searchParams.set('page', page);
        
        for (const [key, value] of Object.entries(filters)) {
            url.searchParams.delete(key);

            if(value) {
                url.searchParams.set(key, value);
            }
        }
        
        url.searchParams.delete('search');
        if(search) {
            url.searchParams.set('search', search);
        }
        
        window.history.pushState({}, '', url.href);
    }
</script>

@endsection