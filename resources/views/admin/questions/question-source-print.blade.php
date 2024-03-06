@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl">
        <span>Quetions :</span>
        <div>
            @foreach ($questionSources as $questionSource)
                <div>
                    Q. {{ $loop->iteration }} {!! $questionSource->question->question_title !!}
                    <sap name="" id="" cols="30" rows="10">{!! $questionSource->question->question_and_answers !!}</sap>
                </div>
            @endforeach
        </div>
    </div>
@endsection
