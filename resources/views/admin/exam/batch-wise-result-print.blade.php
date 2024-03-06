@extends('admin.layouts.app')

@section('content')
    <style>
        @media print {
            .no-print {
                visibility: hidden;
            }

            @page {
                margin-top: 0;
                margin-bottom: 0;
            }

            body {
                padding-top: 72px;
                padding-bottom: 72px;
            }
        }

    </style>

    <div class="container" style="width:90%">
        <div class="text-center">
            <button class="btn btn-success no-print" onclick="window.print()">Print</button>
        </div>
        <div class="row print-area">
            <div class="col text-center">
                <h2 style="font:bold">Genesis</h2>
                <h4>Post Graduation Medical Orientation Center</h4>
                <h4>Result Sheet</h4>
                <h5>Exam : {{ $exam->name ?? '' }}</h5>
                <div style="font-weight: 900">Batch : {{ $batch->name ?? '' }}</div>
            </div>
            <div class="col w-full">
                <div style="display: flex; justify-content:space-between">
                    <div style="font-weight: 900">Batch : {{ $batch->name ?? '' }}</div>
                </div>
                <div>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Reg.No</th>
                                <th>Doctor Name</th>
                                <th>Faculty</th>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Position</th>
                                <th>Subject Position</th>
                                <th>Candidate Position</th>
                                <th>Wrong Answer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                
                            @endphp
                            @foreach ($array as $result)
                                <tr>
                                    <td>{{ $result['reg_no'] }}</td>
                                    <td>{{ $result['doctor_name'] }}</td>
                                    <td>{{ $result['faculty'] }}</td>
                                    <td>{{ $result['subject'] }}</td>
                                    <td>{{ $result['obtained_mark'] }}</td>
                                    <td>{{ $result['overall_position'] }}</td>
                                    <td>{{ $result['subject_position'] }}</td>
                                    <td>{{ $result['candidate_position'] }}</td>
                                    <td>{{ $result['wrong_answer'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
