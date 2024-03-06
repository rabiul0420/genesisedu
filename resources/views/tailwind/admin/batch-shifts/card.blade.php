<div class="flex justify-end gap-2">
    <div
        class="md:col-span-2 border rounded-xl bg-gray-200 flex flex-col justify-center items-center py-1 px-3"
    >
        {{ $batch_shift->shifted_at ? $batch_shift->shifted_at->format('d M Y') : '' }}
    </div>
    <div class="ml-auto">
        <button type="button" onclick="document.getElementById('formContainer{{ $batch_shift->id }}').classList.remove('hidden')" class="ml-auto block rounded border space-y-2 px-4 py-2 bg-white hover:bg-gray-100 transition-all ease-linear">
            Edit
        </button>
    </div>
</div>
<hr class="my-3">
<div class="grid grid-cols-3 gap-4 border rounded-xl bg-gray-200 py-2">
    <div class="grid justify-center items-center">
        <span class="text-xs md:text-sm text-center">Shift Fee</span>
        <div class="text-xl font-bold text-center">
            {{ $batch_shift->shift_fee ?? 0 }} (TK)
        </div>
    </div>
    <div class="grid justify-center items-center">
        <span class="text-xs md:text-sm text-center">Maintenance</span>
        <div class="text-xl font-bold text-center">
            {{ $batch_shift->service_charge ?? 0 }} (TK)
        </div>
    </div>
    <div class="grid justify-center items-center">
        <span class="text-xs md:text-sm text-center">Adjustment</span>
        <div class="text-xl font-bold text-center">
            {{ $batch_shift->payment_adjustment ?? 0 }} (TK)
        </div>
    </div>
</div>
<hr class="my-3">
<div class="grid gap-2">
    <div class="flex gap-1 text-gray-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
        </svg>
        <a href="{{ route('go-to-doctor-profile', $batch_shift->from_doctor_course->doctor_id) }}" target="_blank" class="underline text-sky-600 underline-offset-2">
            <b>{!! str_ireplace($search, "<mark>$search</mark>", $batch_shift->from_doctor_course->doctor->name ? $batch_shift->from_doctor_course->doctor->name : 'N/A') !!}</b>
        </a>
    </div>
    <div class="flex flex-wrap gap-y-2 gap-x-4">
        <div class="flex gap-1 text-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
            </svg>
            <b>{!! str_ireplace($search, "<mark>$search</mark>", $batch_shift->from_doctor_course->doctor->phone ?? '') !!}</b>
        </div>
        <div class="flex gap-1 text-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
            </svg>
            <b>{!! str_ireplace($search, "<mark>$search</mark>", $batch_shift->from_doctor_course->doctor->bmdc_no ?? '') !!}</b>
        </div>
    </div>
</div>

<hr class="my-3">
<div class="grid grid-cols-2 gap-4 relative">
    <a
        target="_blank"
        href="{{ route('doctors-courses.edit', $batch_shift->from_doctor_course_id) }}"
        class="border rounded-xl bg-rose-200 flex flex-col justify-center items-center py-3 px-2 hover:bg-sky-600 hover:text-white"
    >
        <span class="text-xs md:text-sm">From</span>
        <div class="text-2xl font-bold">
            {!! str_ireplace($search, "<mark>$search</mark>", $batch_shift->from_doctor_course->reg_no ?? '') !!}
        </div>
        <span class="text-xs text-center">{{ $batch_shift->from_doctor_course->batch->name ?? '' }}</span>
    </a>
    <a
        target="_blank"
        href="{{ route('doctors-courses.edit', $batch_shift->to_doctor_course_id) }}"
        class="border rounded-xl bg-green-200 flex flex-col justify-center items-center py-3 px-2 hover:bg-sky-600 hover:text-white"
    >
        <span class="text-xs md:text-sm">To</span>
        <div class="text-2xl font-bold">
            {!! str_ireplace($search, "<mark>$search</mark>", $batch_shift->to_doctor_course->reg_no ?? '') !!}
        </div>
        <span class="text-xs text-center">{{ $batch_shift->to_doctor_course->batch->name ?? '' }}</span>
    </a>
    <div class="flex justify-center items-center absolute max-w-max max-h-max left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
        </svg>
    </div>
</div>
<hr class="my-4">
<div class="flex justify-center items-center gap-2">
    Note: <i>{!! str_ireplace($search, "<mark>$search</mark>", $batch_shift->note ?? '') !!}</i>
</div>

@include('tailwind.admin.batch-shifts.form', compact('batch_shift'))