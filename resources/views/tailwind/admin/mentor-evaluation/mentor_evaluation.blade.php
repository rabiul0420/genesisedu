@extends('tailwind.layouts.admin')

@section('content')
    <div class="max-w-2xl print:max-w-4xl mx-auto space-y-8">
        <div class="flex justify-center items-center print:hidden">
            <button type="button" onclick="window.print()" class="px-3 py-2 rounded bg-gray-500 text-white">
                Print
            </button>
        </div>
        <div class="text-center">
            <h2 class="text-2xl">Mentor Evaluation</h2>
            <h5>{{ $data->doctor_course->batch->name ?? '' }}</h5>
            <h5><span>Name Of Teacher : </span>{{ $data->lecture_video->teacher->name ?? '' }}</h5>
            <h5><span>Lecture Name : </span>{{ $data->lecture_video->name ?? '' }}</h5>
        </div>
        <div class="">
            <table class="w-full table table-auto border border-b-0 print:border-black">
                <thead>
                    <tr>
                        <td class="text-left px-3 py-2 border-b print:border-black">Criteria</td>
                        @foreach ($data->getProgresses() as $progress_id => $progress)
                            @if ($progress_id > 0)
                                <td class="text-left px-3 py-2 border-b border-l print:border-black">{{ $progress }}</td>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->getPrimaryRatings() as $key => $value)
                        <tr>
                            <td class="text-left px-3 py-2 border-b print:border-black">{{ $key }}</td>
                            <td class="text-center px-3 py-2 border-b border-l print:border-black">
                                @if ($value == 1)
                                    <i class="fa fa-check text-sky-500 print:text-black"></i>
                                @endif
                            </td>
                            <td class="text-center px-3 py-2 border-b border-l print:border-black">
                                @if ($value == 2)
                                    <i class="fa fa-check text-sky-500 print:text-black"></i>
                                @endif
                            </td>
                            <td class="text-center px-3 py-2 border-b border-l print:border-black">
                                @if ($value == 3)
                                    <i class="fa fa-check text-sky-500 print:text-black"></i>
                                @endif
                            </td>
                            <td class="text-center px-3 py-2 border-b border-l print:border-black">
                                @if ($value == 4)
                                    <i class="fa fa-check text-sky-500 print:text-black"></i>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="">
            <table class="w-full table table-auto border border-b-0 print:border-black">
                <thead>
                    <tr>
                        <td class="text-left px-3 py-2 border-b print:border-black">Criteria</td>
                        @foreach ($data->getVideoProgresses() as $progress_id => $progress)
                            @if ($progress_id > 0)
                                <td class="text-left px-3 py-2 border-b border-l print:border-black">{{ $progress }}</td>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->getVideoRatings() as $key => $value)
                        <tr>
                            <td class="text-left px-3 py-2 border-b print:border-black">{{ $key }}</td>
                            <td class="text-center px-3 py-2 border-b border-l print:border-black">
                                @if ($value == 1)
                                    @if ($value)
                                        <i class="fa fa-check text-sky-500 print:text-black"></i>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center px-3 py-2 border-b border-l print:border-black">
                                @if ($value == 2)
                                    @if ($value)
                                        <i class="fa fa-check text-sky-500 print:text-black"></i>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center px-3 py-2 border-b border-l print:border-black">
                                @if ($value == 3)
                                    @if ($value)
                                        <i class="fa fa-check text-sky-500 print:text-black"></i>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="">
            <h4 class="font-bold">Feedback:</h4>
            <p class="">{!! $data->feedback !!}</p>
        </div>
    </div>
@endsection
