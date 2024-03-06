@extends('tailwind.layouts.admin')

@section('content')
    <div class="bg-white md:sticky md:top-14 -mx-4 -mt-4 border-b py-3 px-4">
        <div class="flex flex-wrap justify-between items-center gap-2 max-w-7xl mx-auto">
            <h1 class="w-full md:w-auto pb-1 text-center text-lg md:text-xl text-sky-600 md:order-2">
                Assign <b id="questionCounter">0</b>/<b>{{ $property_number_of_question }}</b> <i>'{{ $question_type_text }}'</i> in <b>{{ $quiz->title ?? '' }}</b>
            </h1>
            <div class="w-24 md:w-40 grow-0 shrink-0 md:order-1">
                <a href="{{ route('quizzes.show', $quiz->id) }}" class="block py-2 bg-sky-400 rounded text-center text-white">
                    <b>&#8592;</b> Go Back
                </a>
            </div>
            <div class="max-w-xs grow shrink md:order-3">
                <input type="search" class="block w-full px-3 py-2 border border-gray-400 bg-white rounded focus:outline-0" id="search" placeholder="Search ..." value="{{ $search }}" oninput="search()" />
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 max-w-7xl mx-auto mt-4">
            <select class="block w-full py-2 px-3 rounded border" id="filterSubject" onchange="changeSubject(this.value)">
                <option value="">--Select Subject--</option>
            </select>
            <select class="block w-full py-2 px-3 rounded border" id="filterChapter" onchange="changeChapter(this.value)">
                <option value="">--Select Chapter--</option>
            </select>
            <select class="block w-full py-2 px-3 rounded border" id="filterTopic" onchange="changeTopic(this.value)">
                <option value="">--Select Topic--</option>
            </select>
        </div>

    </div>
    
    <div id="dataContainer"></div>

    <script>
        function callApi(option, url, data = {}) {
            axios.get(url, {
                params: data
            })
            .then((res) => {
                setOptionByData(res.data, option)
            });
        }

        function setOptionByData(array, option) {
            let data = `<option value="">--Select ${option}--</option>`;
            
            array.map((item) => {
                data += `<option value="${item.id}">${item.name}</option>`;
            });
            
            return document.getElementById(`filter${option}`).innerHTML = data;
        }

        function setOptionBlank(option) {
            let data = `<option value="">--Select ${option}--</option>`;
            
            return document.getElementById(`filter${option}`).innerHTML = data;
        }

        callApi('Subject', '/api/question-subjects'); // Subject Load

        function changeSubject(subject) {
            setOptionBlank('Chapter');
            setOptionBlank('Topic');
            
            if(subject) {
                callApi('Chapter', '/api/question-chapters', { subject });
            }

            search(1);
        }
        
        function changeChapter(chapter) {
            setOptionBlank('Topic');
            
            if(chapter) {
                callApi('Topic', '/api/question-topics', { chapter });
            }

            search(1);
        }

        function changeTopic(topic) {
            search(1);
        }

        function search(page = 1) {
            const url = `{{ route('quizzes.assign', [$quiz->id, $question_type]) }}?page=${page}`;
            const text = document.getElementById('search').value.trim();

            historyTitle = document.title;
            historyState = {}
            historyUrl = text ? `${url}&search=${text}` : url;
            // localStorage.setItem('historyUrl', historyUrl);
            window.history.pushState(historyState, historyTitle, historyUrl);

            axios.get(url, {
                params: {
                    search: text,
                    flag: true,
                    subject: document.getElementById(`filterSubject`).value,
                    chapter: document.getElementById(`filterChapter`).value,
                    topic: document.getElementById(`filterTopic`).value,
                }
            })
            .then((res) => {
                document.getElementById('dataContainer').innerHTML = res.data.html;
                document.getElementById('questionCounter').innerHTML = res.data.totalQuestion;
            })
            .catch((err) => {
                console.log(err);
            })
        }

        search(`{{ $page ?? 1 }}`);

        function selectQuestion(question) {
            const url = `{{ route('quizzes.assign', [$quiz->id, $question_type]) }}`;

            axios.post(url, {
                question_id: question.value,
                checked: question.checked,
            })
            .then((res) => {
                document.getElementById(`questionId${question.value}`).innerHTML = res.data.message;
                document.getElementById('questionCounter').innerHTML = res.data.totalQuestion;

                if(!res.data.success) {
                    const input = document.getElementById(`questionId${question.value}`).nextElementSibling.firstElementChild;
                    input.checked = ! input.checked;
                }

                setTimeout(() => {
                    document.getElementById(`questionId${question.value}`).innerHTML = '';  
                }, 1000);
            })
        }
    </script>
@endsection
