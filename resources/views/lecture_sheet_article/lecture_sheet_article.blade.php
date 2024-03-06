@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'Lecture Sheet' }}</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                        <div class="col-md-12 py-3">
                            <table class="bg-white table text-center table-striped table-bordered rounded p-1 table-hover datatable">
                                <thead>
                                <tr>
                                    <th style="width: 50px;">SL</th>
                                    <th style="width: 180px;">Actions</th>
                                    <th>Reg. No.</th>
                                    <th>Year</th>
                                    <th>Session</th>
                                    <th>Institute</th>
                                    <th>Course</th>
                                    <th>Discipline</th>
                                    <th>Batch</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($doctor_courses as $key=>$doctor_course)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        @php $batchInactive = isset($doctor_course->batch->status) && $doctor_course->batch->status == 0; @endphp
                                        <a  href="{{ $batchInactive ? 'javascript:void(0)': url('lecture-sheet-article-topics/'.$doctor_course->id) }}" 
                                            class="btn btn-sm btn-primary {{ $batchInactive ? 'disabled':'' }}">Lecture Sheet</a>

                                        <a  href="{{ $batchInactive? 'javascript:void(0)': url('doctor-lecture-sheet-delivery-print/'.$doctor_course->id) }}" 
                                            class="btn btn-sm btn-warning mt-1 {{ $batchInactive ? 'disabled' : '' }}">Lecture Sheet Status</a>
                                        
                                        @if( $batchInactive )
                                            <span class="badge bg-danger">Batch Inactive</span>
                                        @endif
                                        <a  href="{{ url('my-orders/'.$doctor_course->id) }}" class="btn btn-sm btn-primary mt-1">Order Track</a>
                                    </td>
                                    <td>{{ $doctor_course->reg_no }}</td>
                                    <td>{{ $doctor_course->year }}</td>
                                    <td>
                                        {{$doctor_course->session->name??''}}
                                    </td>
                                    <td>
                                        {{$doctor_course->institute->name??''}}
                                    </td>
                                    <td>
                                        {{$doctor_course->course->name??''}}
                                    </td>
                                    <td>
                                        {{$doctor_course->subject->name??''}}
                                    </td>
                                    <td>
                                        {{ $doctor_course->batch->name??''}}
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
