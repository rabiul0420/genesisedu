<div id="formContainer{{ $quiz_property->id }}" class="__formContainer__ bg-gray-600/50 hidden fixed inset-0 overflow-y-auto z-50">
    <div class="max-w-xl m-4 md:mx-auto md:my-16 z-20 bg-white rounded p-4 space-y-3">
        <div class="flex justify-between items-center">
            <div class="text-indigo-500 text-lg bg-indigo-200 px-3 py-1 rounded-md">
                @if($quiz_property->id ?? false)
                <span>ID : {{ $quiz_property->id ?? 'New' }}</span>
                @else
                <span>Add New</span>
                @endif
            </div>
            <button 
                type="button"
                class="text-4xl text-rose-500 -mt-2 cursor-pointer" 
                onclick="this.closest('.__formContainer__').classList.add('hidden')"
            >
                &times;
            </button>
        </div>

        <div>
            @if($quiz_property->status != '2' && !$quiz_property->quizzes->count())
            <form action="{{ route('quiz-properties.save', $quiz_property->id ?? '') }}" method="POST">
                {{ csrf_field() }}
            @endif
            
                <div class="grid gap-4">
                    <div class="border border-dashed border-sky-400 rounded-md p-3 grid md:grid-cols-2 col-span-full gap-4">
                        <div class="grid gap-x-2">
                            <label class="text-sm text-gray-500">Quiz Duration (minute)</label>
                            <input type="number" name="duration" value="{{ $quiz_property->duration }}" class="px-2 py-1.5 border rounded w-full text-right" min="0" autocomplete="off" required />
                        </div>
                        <div class="grid gap-x-2">
                            <label class="text-sm text-gray-500">Pass Mark Percent</label>
                            <input type="number" name="pass_mark_percent" value="{{ $quiz_property->pass_mark_percent }}" class="px-2 py-1.5 border rounded w-full text-right" min="0" autocomplete="off" required />
                        </div>
                        <div class="grid gap-x-2">
                            <label class="text-sm text-gray-500">Status</label>
                            <select name="status" class="px-2 py-1.5 border rounded w-full text-left" required>
                                <option value="1" {{ $quiz_property->status == '1' ? 'selected' : '' }} >Active</option>
                                <option value="0" {{ $quiz_property->status == '0' ? 'selected' : '' }} >Inactive</option>
                                <option value="2" {{ $quiz_property->status == '2' ? 'selected' : '' }} >&#128274; Lock</option>
                            </select>
                        </div>
                        <div class="grid gap-x-2">
                            <label class="text-sm text-gray-500">Course</label>
                            <select name="course_id" class="px-2 py-1.5 border rounded w-full text-left" required>
                                <option value="">--Select--</option>
                                @foreach ($institutes as $institute)
                                <optgroup label="{{ $institute->name }}">
                                    @foreach ($institute->active_courses as $course)
                                    <option value="{{ $course->id }}" {{ $quiz_property->course_id == $course->id ? 'selected' : ''  }} >
                                        {{ $course->name ?? '' }}
                                    </option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @foreach(\App\Question::$question_type_array as $question_type => $question_type_text)
                    @php $quiz_property_item = $quiz_property->quiz_property_items->where('question_type', $question_type)->first(); @endphp
                    <div class="border border-dashed border-sky-400 rounded-md p-3 grid md:grid-cols-2 gap-3">
                        <div class="grid grid-cols-2 items-center gap-x-2">
                            <label class="text-sm text-gray-500 text-right">Question Type</label>
                            <select name="question_type[]" class="px-2 py-1.5 border rounded block w-full text-right">
                                <option value="{{ $question_type }}" selected>{{ $question_type_text }}</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 items-center gap-x-2">
                            <label class="text-sm text-gray-500 text-right">Number Of Question</label>
                            <input type="number" name="number_of_question[]" value="{{ $quiz_property_item->number_of_question ?? '' }}" class="px-2 py-1.5 border rounded block w-full text-right" min="0" autocomplete="off" />
                        </div>
                        <div class="grid grid-cols-2 items-center gap-x-2">
                            <label class="text-sm text-gray-500 text-right">Per Stem Mark</label>
                            <input type="number" name="per_stamp_mark[]" value="{{ $quiz_property_item->per_stamp_mark ?? '' }}" class="px-2 py-1.5 border rounded block w-full text-right" min="0" step="0.01" autocomplete="off" />
                        </div>
                        <div class="grid grid-cols-2 items-center gap-x-2">
                            <label class="text-sm text-gray-500 text-right">Per Stem Negative</label>
                            <input type="number" name="per_stamp_negative_mark[]" value="{{ $quiz_property_item->per_stamp_negative_mark ?? '' }}" class="px-2 py-1.5 border rounded block w-full text-right" min="0" step="0.01" autocomplete="off" />
                        </div>
                    </div>
                    @endforeach
                </div>
        
                <div class="flex justify-end items-center py-4">
                    <button class="px-4 py-1.5 border rounded-md {{ ($quiz_property->status != '2' && !$quiz_property->quizzes->count()) ? 'bg-sky-500' : 'bg-rose-500' }} text-white">
                        {{ ($quiz_property->status != '2' && !$quiz_property->quizzes->count()) ? 'Save' : '&#128274; Lock' }}
                    </button>
                </div>
            @if($quiz_property->status != '2' && !$quiz_property->quizzes->count())
            </form>
            @endif
        </div>
    </div>
</div>