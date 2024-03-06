@extends('tailwind.layouts.admin')

@section('content')

<div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-4 print:grid-cols-2">
    <input id="search" type="search" oninput="search()" value="{{ request()->search }}" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" placeholder="Search" />
    <input id="date_from" type="date" oninput="search()" value="{{ request()->date_from }}" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" />
    <input id="date_to" type="date" oninput="search()" value="{{ request()->date_to }}" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" />
    <div class="flex justify-center items-center gap-2">
        <span>To</span>
        <select id="to_batch" onchange="search()" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0">
            <option value="">-- All Batch --</option>
            @foreach($to_batches as $to_batch)
            <option value="{{ $to_batch->id }}" {{ request()->to_batch == $to_batch->id ? 'selected' : '' }}>
                {{ $to_batch->name ?? '' }}
            </option>
            @endforeach
        </select>
    </div>
</div>

<hr class="my-3 print:hidden">
<div class="my-2" id="dataContainer">
</div>

<script>
    function search(page = 1) {
        const search = document.getElementById('search').value;
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;
        const toBatch = document.getElementById('to_batch').value;

        setUrl(page, search, {
            date_from: dateFrom,
            date_to: dateTo,
            to_batch: toBatch,
        });

        axios.get(`/admin/batch-shifts`, {
                params: {
                    page,
                    search,
                    date_from: dateFrom,
                    date_to: dateTo,
                    to_batch: toBatch,
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