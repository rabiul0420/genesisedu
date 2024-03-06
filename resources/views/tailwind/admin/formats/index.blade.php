@extends('tailwind.layouts.admin')

@section('content')

<div class="grid gap-4 md:grid-cols-3 2xl:grid-cols-5 print:grid-cols-3">
    <select id="typeFilter" onchange="search()" class="flex w-full px-3 py-2 border border-gray-400 rounded focus:outline-0">
        <option value=""> -- All -- </option>
        <option value="1" {{ request()->type == '1' ? 'selected' : '' }}> SMS </option>
    </select>
    <input id="search" type="search" oninput="search()" value="{{ request()->search ?? '' }}" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" placeholder="Search" />
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
        const type = document.getElementById('typeFilter').value;

        setUrl(page, search, { type });

        axios.get(`/admin/formats?search=${search}`, {
                params: {
                    page,
                    search,
                    type,
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

    search(`{{ request()->page ?? 1 }}`);

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