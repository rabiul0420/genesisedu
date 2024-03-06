@extends('tailwind.layouts.client')

@section('content')
<div class="max-w-2xl mx-auto px-2" x-data="initData()" x-init="callApi()">
    <template x-if="message">
        <div class="my-4 py-4 px-4 flex justify-center items-center bg-white shadow text-3xl rounded-lg overflow-hidden">
            <div x-text="message" class="text-base md:text-2xl"></div>
        </div>
    </template>

    <template x-if="result_status">
        
        <div class="my-4 grid md:grid-cols-2 gap-4">
            <template x-if="result.coupon_status">
                <div class="col-span-full py-4 px-2 md:px-4 flex flex-col justify-center items-center bg-white shadow text-3xl rounded-lg overflow-hidden">
                    <div class="flex justify-start items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" class="w-6 md:w-8 h-6 md:h-8 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-lg md:text-2xl font-semibold">Congratulations</span>
                    </div>
                    <div class="text-sm md:text-lg text-center">You got a coupon for your excellent performance.</div>
                    <div class="text-sm md:text-lg text-center">You can avail reward with this coupon from <b>GENESIS</b>.</div>
                </div>
            </template>
            <template x-if="result.coupon_status">
                <div class="w-full flex flex-col justify-between bg-white shadow-lg text-3xl rounded-lg overflow-hidden">
                    <div x-text="result.coupon" class="h-28 flex justify-center items-center text-4xl select-all"></div>
                    <div class="text-2xl text-center w-full bg-brand-primary text-white py-2">Coupon</div>
                </div>
            </template>
            <div class="w-full flex flex-col justify-between bg-white shadow-lg text-3xl rounded-lg overflow-hidden">
                <div class="flex justify-center items-center gap-2">
                    <div x-text="result.mark" class="h-28 flex justify-center items-center text-5xl select-all"></div>
                    <span>/</span>
                    <div x-text="quiz.full_mark" class="h-28 flex justify-center items-center text-5xl select-all"></div>
                </div>
                <div class="text-2xl text-center w-full bg-brand-primary text-white py-2">Mark</div>
            </div>
        </div>
    </template>

    <template x-if="!result_status">
        <div class="w-full">
            <div class="py-4 flex items-center">
                <h1 class="grow shrink text-base md:text-2xl font-bold" x-text="quiz.title"></h1>
                <template x-if="questions.length && !loading">
                    <button @click="finishQuiz()" class="grow-0 shrink-0 px-4 py-2 rounded-md bg-sky-600 text-white my-4" >Finish Quiz</button>
                </template>
            </div>

            <div class="grid gap-8">
                <template x-if="!questions.length">
                    <template x-for="i in 2">
                        <div class="animate-pulse w-full bg-white p-2 md:px-4 rounded-md border shadow">
                            <div class="flex items-center gap-4">
                                <div class="my-2 h-7 w-7 rounded-full bg-gray-300"></div>
                                <div class="grow h-3 bg-gray-300 rounded-lg"></div>
                            </div>
                            <template x-for="i in 5">
                                <div class="border-t px-6 py-3">
                                    <div 
                                        class="grow h-3 bg-gray-300 rounded-lg"
                                        :class="i%2 ? 'w-5/6' : 'w-4/6'"
                                    ></div>
                                </div>
                            </template>
                        </div>
                    </template>
                </template>
                <template x-for="(question, questionIndex) in questions">
                    <div class="w-full bg-white px-2 md:px-4 py-1 rounded-md border shadow">
                        <h5 class="py-2 flex gap-1">
                            <div class="grow-0 shrink-0 h-7 w-7 rounded-full bg-gray-300/50 flex justify-center items-center text-sm font-semibold" x-text="questionIndex + 1"></div>
                            <div class="grow shrink text-sky-600 text-sm md:text-base" x-html="question.title"></div>
                        </h5>
                        <template x-if="question.options.length">
                            <div>
                                <template x-for="(option, index) in question.options">
                                    <div class="flex items-center border-t">
                                        <div class="shrink grow py-2.5 px-2 md:px-4 text-sm md:text-base" x-html="option.title"></div>
                                        <template x-if="question.type == '1'">
                                            <div class="shrink-0 grow-0 grid grid-cols-2 gap-4 py-2">
                                                <label 
                                                    @click="selectOption(question.id, 'T', index)"
                                                    class="apply-radio-button"
                                                    :class="{ 'apply-radio-button-checked' : checkSelectOption(question.id, 'T', index) }" 
                                                >
                                                    T
                                                </label>
                                                <label 
                                                    @click="selectOption(question.id, 'F', index)" 
                                                    class="apply-radio-button"
                                                    :class="{ 'apply-radio-button-checked' : checkSelectOption(question.id, 'F', index) }" 
                                                >
                                                    F
                                                </label>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="question.type == '2'">
                                    <div class="border-t flex justify-center items-center gap-8 py-3">
                                        <template x-for="(option, index) in question.options">
                                            <label 
                                                @click="selectOption(question.id, option.serial)" 
                                                class="apply-radio-button"
                                                :class="{ 'apply-radio-button-checked' : checkSelectOption(question.id, option.serial) }"
                                                x-text="option.serial"
                                            ></label>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="w-full py-4 flex justify-end items-center">
                <template x-if="questions.length && !loading">
                    <button @click="finishQuiz()" class="grow-0 shrink-0 px-4 py-2 rounded-md bg-sky-600 text-white my-4" >Finish Quiz</button>
                </template>
            </div>
        </div>
    </template>
</div>

<script>
    const url = `{{ $url }}`;
    const token = 'Bearer {{ $token }}';

    function initData() {
        return {
            quiz: [],
            questions: [],
            quiz_answers: [],
            message: "",
            loading: true,
            result_status: false,
            result: [],
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': token,
            },
            getQuizQuestion(data) {
                let oldAnswers = localStorage.getItem(data.quiz.key);

                return oldAnswers
                    ? JSON.parse(oldAnswers)
                    : data.quiz_answers;
            },
            removeLocalStorageData() {
                if(this.result_status) {
                    localStorage.removeItem(this.quiz.key);
                }
            },
            callApi() {
                axios.get(url, {
                    headers: this.headers,
                })
                .then(({data}) => {
                    this.quiz = data.quiz;
                    this.questions = data.questions;

                    this.quiz_answers = this.getQuizQuestion(data);
                })
                .catch((error) => {
                    if(error.response) {
                        this.message = error.response.data.message;

                        if(error.response.data) {
                            this.quiz = error.response.data.quiz;
                            this.result_status = error.response.data.quiz.result_status;
                            this.result = error.response.data.quiz.result;
                        }
                    } else {
                        this.message = "Somthing went wrong!";
                    }
                })
                .finally(() => {
                    this.loading = false;
                    this.removeLocalStorageData();
                });
            },
            getSkipQuestion() {
                let count = this.questions.length;

                Object.values(this.quiz_answers).forEach((quizAnswer) => {
                    answer = quizAnswer.answer.replaceAll('.', '');

                    if(answer.length) {
                        count--;
                    }
                });

                return count;
            },
            selectOption(quizQuestionId, value, index = 0) {
                let selectQuestion = null;
                
                selectQuestion = Object.values(this.quiz_answers).filter(answer => {
                    return answer.quiz_question_id == quizQuestionId;
                })[0];

                if(selectQuestion) {
                    const optionArray = selectQuestion.answer.split('');

                    optionArray[index] = value;
                    
                    selectQuestion.answer = optionArray.join('');
                }

                localStorage.setItem(this.quiz.key, JSON.stringify(this.quiz_answers))
            },
            checkSelectOption(quizQuestionId, value, index = 0) {
                let selectQuestion = null;
                let isSelected = false;
                
                selectQuestion = Object.values(this.quiz_answers).filter(answer => {
                    return answer.quiz_question_id == quizQuestionId;
                })[0];

                if(selectQuestion) {
                    const optionArray = selectQuestion.answer.split('');

                    isSelected = optionArray[index] === value;
                }

                return isSelected;
            },
            finishQuiz() {
                if(!confirm(`You skip ${this.getSkipQuestion()} questions. Do you want to submit?`)) {
                    return;
                }

                this.loading = true;

                axios.post(url, {
                    quiz_answers: this.quiz_answers,
                }, 
                {
                    headers: this.headers,
                })
                .then(({data}) => {
                    console.log(data);

                    if(data.quiz.result_status) {
                        this.quiz = data.quiz;
                        this.result_status = data.quiz.result_status;
                        this.result = data.quiz.result;
                    }
                })
                .finally(() => {
                    this.loading = false;
                    this.removeLocalStorageData();
                });
            }
        }
    }
</script>
@endsection