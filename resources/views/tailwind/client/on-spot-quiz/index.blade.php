@extends('tailwind.layouts.client')

@section('content')
<div class="max-w-6xl mx-auto px-2 py-4" x-data="initData()" x-init="callApi()">
    <template x-if="message">
        <div class="my-4 py-4 px-4 flex justify-center items-center bg-white shadow text-3xl rounded-lg overflow-hidden">
            <div x-text="message" class="text-base md:text-2xl"></div>
        </div>
    </template>

    <template x-if="loading">
        <div class="grid md:grid-cols-3 gap-4">
            <template x-for="n in 3">
                <div class="animate-pulse w-full bg-white rounded-md border shadow grid grid-cols-3 gap-4 p-4">
                    <div class="col-span-full h-5 bg-gray-300 rounded-lg"></div>
                    <div class="col-start-1 h-10 bg-gray-200 rounded-lg"></div>
                    <div class="col-start-3 h-10 bg-gray-200 rounded-lg"></div>
                    <div class="col-start-1 col-span-2 h-5 bg-gray-300 rounded-lg"></div>
                    <div class="col-start-2 h-8 bg-gray-300 rounded-lg"></div>
                </div>
            </template>
        </div>
    </template>
    
    <div class="grid md:grid-cols-3 gap-4">
        <template x-for="quiz in quizzes">
            <div class="block rounded shadow-lg shadow-sky-300 space-y-2 p-4 bg-gradient-to-tr from-sky-600/50 to-pink-600/50">
                <div class="flex gap-2">
                    <div class="text-gray-600">Title:</div>
                    <div class="font-semibold" x-html="quiz.title"></div>
                </div>
                <div class="flex items-end gap-1">
                    <div class="text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="font-semibold text-3xl" x-text="quiz.duration_in_minute"></div>
                    <div class="font-semibold">Minutes</div>
                </div>
                <div class="flex gap-2 items-end">
                    <div class="text-gray-600">Mark:</div>
                    <div class="font-semibold text-3xl" x-text="quiz.full_mark"></div>
                </div>
                <div class="flex gap-2">
                    <div class="text-gray-600">Question:</div>
                    <div class="font-semibold" x-html="quiz.question_criteria"></div>
                </div>
                <div class="flex justify-center items-center">
                    <template x-if="quiz.result_status">
                        <a :href="`/on-spot-quiz/` + quiz.key" class="py-1 px-4 rounded bg-green-600 text-white">View Result</a>
                    </template>
                    <template x-if="!quiz.result_status">
                        <a :href="`/on-spot-quiz/` + quiz.key" class="py-1 px-4 rounded bg-sky-600 text-white">Start Quiz</a>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>
<script>
    const url = `{{ $url }}`;
    const token = 'Bearer {{ $token }}';

    function initData() {
        return {
            quizzes: [],
            message: "",
            loading: true,
            callApi() {
                axios.get(url, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': token,
                    },
                })
                .then(({data}) => {
                    this.quizzes = data.quizzes;
                    this.loading = false;
                })
                .catch((error) => {
                    if(error.response) {
                        this.message = error.response.data.message;
                    } else {
                        this.message = "Somthing went wrong!";
                    }

                    this.loading = false;
                })
            },
        }
    }

    // console.log(initData());
</script>
@endsection