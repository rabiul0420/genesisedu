<div id="formContainer{{ $quiz->id }}" class="__formContainer__ bg-gray-600/50 hidden fixed inset-0 overflow-y-auto z-50">
    <div class="max-w-xl m-4 md:mx-auto md:my-16 z-20 bg-white rounded p-4 space-y-3">
        <div class="flex justify-between items-center">
            <div class="text-indigo-500 text-lg bg-indigo-200 px-3 py-1 rounded-md">
                @if($quiz->id ?? false)
                <span>ID : {{ $quiz->id ?? 'New' }}</span>
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
            <form action="{{ route('quizzes.save', $quiz->id ?? '') }}" method="POST">
                {{ csrf_field() }}
            
                <div class="grid gap-4">
                    <div class="border border-dashed border-sky-400 rounded-md p-3 grid md:grid-cols-2 col-span-full gap-4">
                        <div class="col-span-full grid sm:grid-cols-4 items-center gap-x-2">
                            <label class="text-sm text-gray-500 sm:text-right">Quiz Title</label>
                            <input {{ $quiz_participants_count > 0 ? 'disabled' : '' }} type="text" name="title" value="{{ $quiz->title ?? '' }}" class="w-full sm:col-span-3 px-2 py-1.5 border rounded block text-left" min="0" autocomplete="off" required />
                        </div>
                        <div class="col-span-full grid sm:grid-cols-4 items-center gap-x-2">
                            <label class="text-sm text-gray-500 sm:text-right">Property</label>
                            <select {{ $quiz_participants_count > 0 ? 'disabled' : '' }} name="property_id" class="w-full sm:col-span-3 px-2 py-1.5 border rounded block text-left" required>
                                <option value="" >-- Select --</option>
                                @foreach ($quiz_properties as $quiz_property)
                                <option value="{{ $quiz_property->id ?? '' }}" {{ $quiz->quiz_property_id == $quiz_property->id ? 'selected' : '' }} >
                                    {{ $quiz_property->title ?? '' }} ({{ $quiz_property->course->name ?? '' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if($quiz->id ?? false)
                        <div class="col-span-full grid sm:grid-cols-4 items-center gap-x-2">
                            <label class="text-sm text-gray-500 sm:text-right">Status</label>
                            <select name="status" class="w-full sm:col-span-3 px-2 py-1.5 border rounded block text-left" required>
                                @if($quiz_participants_count < 1)
                                <option value="0" {{ $quiz->status == '0' ? 'selected' : '' }} >Unpublish</option>
                                @endif
                                @if($quiz->quiz_property->total_question == $quiz->quiz_questions->count())
                                <option value="1" {{ $quiz->status == '1' ? 'selected' : '' }} >Publish</option>
                                <option value="2" {{ $quiz->status == '2' ? 'selected' : '' }} >Closed</option>
                                @endif
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
        
                <div class="flex justify-end items-center py-4">
                    <button class="px-4 py-1.5 border rounded-md bg-sky-500 text-white">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>