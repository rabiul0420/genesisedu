@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <form action="{{ route('my.subscriptions.available', [$year, $course->id, $session->id]) }}" method="POST" class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center pt-3">
                        <h2 class="h2 brand_color">Available Subscriptions</h2>
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
                        <a class="btn btn-secondary" href="{{ route('my.subscriptions.add-subscription') }}">
                            <b>&#8592;</b> Back
                        </a>
                    </div>
                </div>

                <hr>

                <div class="row py-3" style="position: sticky; top: 100px; background: rgb(235, 238, 239); z-index: 999;">
                    <input type="hidden" name="faculty_id" value="{{ $faculty->id ?? '' }}" />
                    <input type="hidden" name="discipline_id" value="{{ $discipline->id ?? '' }}" />
                    <input type="hidden" name="total" value="{{ $total_amount }}" id="totalAmount" />
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
                    <div class="col-6 col-md-4 my-1">
                        <div class="form-control text-center">
                            Selected <b class="text-info px-1" id="selectedItem">{{ $items_count ?? 0 }}</b>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 my-1">
                        {{ csrf_field() }}
                        <button onclick="submit()" type="button" class="btn btn-info w-100">
                            Next
                        </button>
                    </div>
                </div>

                <hr class="mt-0">

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

                <div class="row" id="itemContainer">
                </div>
            </form>
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

    const itemContainer = document.getElementById('itemContainer');
    const durationSelector = document.getElementById('durationSelector');
    const selectedItem = document.getElementById('selectedItem');
    const totalAmount = document.getElementById('totalAmount');

    function callItemApi(courseId, year, sessionId, facultyId, disciplineId) {
        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/items`;

        const data = {
            course_id: courseId,
            year: year,
            session_id: sessionId,
            faculty_id: facultyId,
            discipline_id: disciplineId,
        };
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            Object.values(data.items).forEach((item) => {
                itemContainer.innerHTML += `
                    <div class="col-md-6 p-2">
                        <div class="h-100 border rounded p-3 bg-white">
                            <div class="d-flex justify-content-between">
                                <label for="item__${item.id}" class="btn btn-sm btn-outline-primary">
                                    ${item.price} TK
                                </label>
                                <input id="item__${item.id}" name="items[]" value="${item.id}" ${items.includes(item.id) ? 'checked' : ''} data-price="${item.price}" type="checkbox" class="form-check-input border border-primary" onChange="selectItem(this, ${item.id}, ${item.price})" />
                            </div>
                            <hr class="my-2">
                            <div>
                                ${item.name}
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

    callItemApi(`{{ $course->id }}`, `{{ $year }}`, `{{ $session->id }}`, `{{ $faculty->id ?? '' }}`, `{{ $discipline->id ?? '' }}`);

    function selectItem(inputElement, itemId, price) {
        let selectedItemValue = parseInt(selectedItem.textContent);

        if(inputElement.checked) {
            selectedItem.innerHTML = selectedItemValue + 1;
        } else {
            selectedItem.innerHTML = selectedItemValue - 1;
        }

        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;

        const itemInputs = document.querySelectorAll('input[name="items[]"][type="checkbox"]:checked');

        itemInputs.forEach((input) => {
            total += parseInt(input.getAttribute('data-price'));
        });

        totalAmount.value = total;
    }

</script>
@endsection
