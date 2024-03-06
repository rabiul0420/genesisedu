@extends('tailwind.layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('addon-services.store') }}" class="grid grid-cols-2 md:grid-cols-5 rounded shadow-md p-4 gap-4 shadow-zinc-50 bg-zinc-200 print:hidden">
            {{ csrf_field() }}
            <input name="name" class="block col-span-2 w-full px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text" placeholder="Addon Service Name" required autocomplete="off">
            <input name="regular_price" type="number" placeholder="Regular Price" required autocomplete="off" class="block px-2 py-2 border rounded border-gray-200 focus:outline-none">
            <input name="sale_price" type="number" placeholder="Sale Price" required autocomplete="off" class="block px-2 py-2 border rounded border-gray-200 focus:outline-none">
            <input type="submit" value="(+) Add New" class="col-span-2 md:col-span-1 block w-full px-4 py-2 rounded bg-sky-500 text-white focus:outline-none cursor-pointer">
        </form>

        <div class="mt-6 space-y-4">
            {{ $addon_services->links('tailwind.components.paginator') }}

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($addon_services as $index => $addon_service)
                    <div class="rounded overflow-hidden shadow p-4 border grid gap-4">
                        <div class="flex justify-between items-center gap-4">
                            <span class="text-sky-600 rounded px-2 py-1 border border-dashed border-sky-400">
                                ID: {{ $addon_service->id }}
                            </span>
                            <a href="{{ route('addon-services.show', $addon_service->id) }}"
                            class="print:hidden shrink-0 px-2 py-1 rounded border border-sky-500 text-sky-500 hover:bg-sky-500 hover:text-white focus:outline-none cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>
                        <div class="">
                            <input id="name__link__{{ $index }}"
                                class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text"
                                value="{{ $addon_service->name ?? '' }}" placeholder="Addon Service Name">
                        </div>
                        <div class="flex gap-4">
                            <input id="regular_price__link__{{ $index }}"
                                class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text"
                                value="{{ $addon_service->regular_price ?? 0 }}" placeholder="Regular Price">
                            <input id="sale_price__link__{{ $index }}"
                                class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text"
                                value="{{ $addon_service->sale_price ?? 0 }}" placeholder="Sale Price">
                            <input type="button" value="Save" onclick="updateLink(this, `{{ $addon_service->id }}`, `{{ $index }}`)" class="print:hidden w-20 shrink-0 py-1.5 rounded bg-sky-400 text-white focus:outline-none cursor-pointer">
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{ $addon_services->links('tailwind.components.paginator') }}
        </div>
    </div>

    <script>
        function updateLink(button, addonServiceId, index) {
            let name = document.getElementById(`name__link__${index}`).value.trim();
            let regular_price = document.getElementById(`regular_price__link__${index}`).value.trim();
            let sale_price = document.getElementById(`sale_price__link__${index}`).value.trim();

            axios.post(`/admin/addon-services/${addonServiceId}`, {
                name,
                regular_price,
                sale_price,
                _method: 'PUT'
            })
            .then(function(response) {
                button.classList.remove('bg-sky-400');
                button.classList.add('bg-green-400');
                button.value = '✓';
                setTimeout(() => {
                    button.value = 'Save';
                    button.classList.remove('bg-green-400');
                    button.classList.add('bg-sky-400');
                }, 1000);
                console.log(response.data);
            })
            .catch(function(error) {
                console.log(error);
            });
        }
    </script>

@endsection
