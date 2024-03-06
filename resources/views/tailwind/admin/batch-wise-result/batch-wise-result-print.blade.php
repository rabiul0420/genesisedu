@extends('tailwind.layouts.admin')
@section('content')
    <div class="max-w-6xl print:max-w-6xl mx-auto space-y-8">
        <div class="flex justify-center items-center print:hidden">
            <button type="button" onclick="window.print()" class="px-3 py-2 rounded bg-sky-500 text-white">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
        <div class="flex flex-col text-center">
            <span class="text-4xl font-bold">Genesis</span>
            <span class="text-sm">Post Graduation Medical Orientation Center</span>
            <span class="text-xl font-bold mt-2 underline">Result Sheet</span>
            <span><span class="font-bold">Exam:</span> {{ $exam->name ?? '' }}</span>
            @if($batch)
            <div class="flex justify-center items-center gap-1">
                <a href="{{ route('batch-wise-result-print') }}?exam={{ $exam->id }}" class="mr-2 -mt-1 text-2xl text-rose-600 cursor-pointer print:hidden">&times;</a>
                <span class="font-bold">Batch:</span> 
                <span>{{ $batch->name ?? '' }}</span>
            </div>
            @endif
        </div>
        <div class="overflow-auto print:overflow-hidden">
            <table class="w-full border border-b-0 print:border-black print:text-black">
                <thead>
                    <tr class="bg-gray-100 print:bg-transparent">
                        <th class="text-left px-3 py-2 border-b print:border-black">Doctor Name</th>
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Reg.No</th>
                        @if(!$batch)
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Batch Name</th>
                        @endif
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Faculty</th>
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Subject</th>
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Marks</th>
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Position</th>
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Subject Position</th>
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Candidate Position</th>
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Batch Position</th>
                        <th class="text-left px-3 py-2 border-b border-l print:border-black">Wrong Answer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($array as $result)
                        <tr class="{{ $loop->iteration % 2 ?: 'bg-gray-100' }} hover:bg-gray-200 print:hover:bg-transparent">
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b print:border-black">
                                {{ $result['doctor_name'] }}
                            </td>
                            <td class="text-left px-3 py-2 border-b border-l print:border-black">
                                {{ $result['reg_no'] }}
                            </td>
                            @if(!$batch)
                            <td class="text-left px-3 py-2 border-b border-l print:border-black">
                                <a href="{{ route('batch-wise-result-print') }}?exam={{ $exam->id }}&batch={{ $result['batch_id'] }}" class="text-sky-600 print:text-black">
                                    {{ $result['batch_name'] }}
                                </a>
                            </td>
                            @endif
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b border-l print:border-black">
                                {{ $result['faculty'] }}
                            </td>
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b border-l print:border-black">
                                {{ $result['subject'] }}
                            </td>
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b border-l print:border-black">
                                {{ $result['obtained_mark'] }}
                            </td>
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b border-l print:border-black">
                                {{ $result['overall_position'] }}
                            </td>
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b border-l print:border-black">
                                {{ $result['subject_position'] }}
                            </td>
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b border-l print:border-black">
                                {{ $result['candidate_position'] }}
                            </td>
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b border-l print:border-black">
                                {{ $result['batch_position'] }}
                            </td>
                            <td class="text-center text-sm print:text-md px-3 py-2 border-b border-l print:border-black">
                                {{ $result['wrong_answer'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
