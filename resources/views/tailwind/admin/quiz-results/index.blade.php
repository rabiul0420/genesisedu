@extends('tailwind.layouts.admin')

@section('content')
<div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3 print:grid-cols-2">
    <select id="quizFilter" onchange="search()" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0">
        <option value=""> -- All Quiz -- </option>
        @foreach ($quizzes as $quiz)
        <option value="{{ $quiz->id }}" {{ $quiz->id == $filter_quiz_id ? 'selected' : '' }}>{{ $quiz->title ?? '' }}</option>
        @endforeach
    </select>
    <input id="search" type="search" oninput="search()" value="{{ $search }}" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" placeholder="Search" />
</div>

<hr class="my-3 print:hidden">
<div class="my-2" id="dataContainer">
</div>

<script>
    function search(page = 1) {
        const search = document.getElementById('search').value;
        const quiz = document.getElementById('quizFilter').value;

        setUrl(page, search, {quiz});

        axios.get(`/admin/quiz-results?search=${search}`, {
                params: {
                    page,
                    search,
                    quiz,
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