@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'Notice' }}</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                        <div class="col-md-12 py-3">
                            <table class="bg-white table table-striped table-bordered rounded datatable p-1">
                                <thead>
                                <tr>
                                    <th style="width: 50px;">SL</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Options</th>
                                    <th>Answer</th>
                                    <th>Answer</th>
                                </tr>
                                </thead>
                                <tbody>

                                    @foreach($exam as $question)
                                                                            
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $question->question_type }}</td>
                                            <td>{!! $question->question_title !!}</td>
                                            <td>
                                                @foreach ($question->question_option as $option)
                                                    {!! $option !!}
                                                @endforeach
                                            </td>
                                            <td style="width: 85px;">
                                                @foreach ($question->question_option as $k => $option)
                                                <label>T</label>
                                                <input type="radio" name="ans_{{ $k }}" value="T">
                                                &nbsp;
                                                <label>F</label>
                                                <input type="radio" name="ans_{{ $k }}" value="F">
                                                @endforeach
                                            </td>
                                            <td style="width: 45px;">
                                                @foreach ($question->question_option as $k => $option)
                                                <label>{{ $k == 0 ? 'A' : ($k == 1 ? 'B' : ($k == 2 ? 'C' : ($k == 3 ? 'D' : 'E' ) ) ) }}</label>
                                                <input type="radio" name="ans" value="T">
                                                <br>
                                                @endforeach
                                            </td>
                                        </tr>
                                        
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                </div>
            </div>
        </div>

    </div>


</div>
@endsection
