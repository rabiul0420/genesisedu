<div id="formContainer{{ $batch_shift->id }}" class="__formContainer__ bg-gray-600/50 hidden fixed inset-0 overflow-y-auto z-50">
    <div class="max-w-xl m-4 md:mx-auto md:my-16 z-20 bg-white rounded p-4 space-y-3">
        <div class="flex justify-between items-start">
            <div>
                <div class="text-xs md:text-sm">Shifting From</div>
                <div class="text-indigo-500 text-sm md:text-lg font-bold">
                    ({{ $batch_shift->from_doctor_course->reg_no ?? '' }}) {{ $batch_shift->from_doctor_course->batch->name ?? '' }}
                </div>
            </div>
            <div class="w-20 flex justify-end items-start">
                <button 
                    type="button"
                    class="cursor-pointer -mt-4" 
                    onclick="this.closest('.__formContainer__').classList.add('hidden')"
                >
                    <span class="text-5xl text-rose-500">&times;</span>
                </button>
            </div>
        </div>

        <div>
            <form action="{{ route('batch-shifts.update', $batch_shift->id ?? '') }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
            
                <div class="grid gap-4">
                    <div class="border border-dashed border-sky-400 rounded-md p-3 grid grid-cols-2 md:grid-cols-4 col-span-full gap-x-4 gap-y-6">
                        <div class="col-span-full grid gap-x-2">
                            <label class="text-sm text-gray-500">Shifting To</label>
                            <select name="to_doctor_course_id" class="w-full px-2 py-1.5 border rounded block text-left focus:outline-none" required>
                                <option value="">Select</option>
                                <option value="0" {{ $batch_shift->to_doctor_course_id === 0 ? 'selected' : '' }}>
                                    {!! $batch_shift->to_doctor_course_id === 0 ? '&check;' : '' !!} Future Batch
                                </option>
                                @if($batch_shift->to_doctor_course_id && $batch_shift->to_doctor_course)
                                <option value="{{ $batch_shift->to_doctor_course_id }}" selected>
                                    &check; ({{ $batch_shift->to_doctor_course->reg_no ?? '' }}) {{ $batch_shift->to_doctor_course->batch->name ?? '' }}
                                </option>
                                @endif
                                @foreach($batch_shift->from_doctor_course->doctor->doctor_courses as $doctor_course)
                                @if($batch_shift->to_doctor_course_id != $doctor_course->id)
                                <option value="{{ $doctor_course->id }}">
                                    ({{ $doctor_course->reg_no }}) {{ $doctor_course->batch->name ?? '' }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 grid gap-x-2">
                            <label class="text-sm text-gray-500">Shift Fee</label>
                            <input type="number" name="shift_fee" value="{{ $batch_shift->shift_fee ?? '' }}" class="w-full px-2 py-1.5 border rounded block text-right focus:outline-none" />
                        </div>
                        <div class="col-span-2 grid gap-x-2">
                            <label class="text-sm text-gray-500">Maintenance Charge</label>
                            <input type="number" name="service_charge" value="{{ $batch_shift->service_charge ?? '' }}" class="w-full px-2 py-1.5 border rounded block text-right focus:outline-none" />
                        </div>
                        <div class="col-span-2 grid gap-x-2">
                            <label class="text-sm text-gray-500">Payment Adjustment</label>
                            <input type="number" name="payment_adjustment" value="{{ $batch_shift->payment_adjustment ?? '' }}" class="w-full px-2 py-1.5 border rounded block text-right focus:outline-none" />
                        </div>
                        <div class="col-span-2 grid gap-x-2">
                            <label class="text-sm text-gray-500">Shifting Date</label>
                            <input type="date" name="shifted_at" value="{{ $batch_shift->shifted_at ? $batch_shift->shifted_at->format('Y-m-d') : '' }}" class="w-full px-2 py-1.5 border rounded block text-left focus:outline-none" />
                        </div>
                        <div class="col-span-full grid gap-x-2">
                            <label class="text-sm text-gray-500">Note Box</label>
                            <input type="text" name="note" value="{{ $batch_shift->note ?? '' }}" class="w-full px-2 py-1.5 border rounded block text-left focus:outline-none" />
                        </div>
                    </div>
                </div>
        
                <div class="flex justify-end items-center py-4">
                    <label class="flex gap-2 items-center">
                        <input type="checkbox" required>
                        আমি ঘোষণা করছি যে সমস্ত তথ্য সঠিক।
                    </label>
                    <button type="submit" class="ml-auto px-4 py-1.5 border rounded-md bg-sky-500 text-white">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>