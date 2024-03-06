@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'My Results' }}</h2>
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
                                    <th>Actions</th>
                                    <th>Reg. No.</th>
                                    <th>Year</th>
                                    <th>Session</th>
                                    <th>Course</th>
                                    <th>Discipline</th>
                                    <th>Batch</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($course_info as $sl => $value)
                                <tr>
                                    @php $batchInactive = isset($value->batch->status) && $value->batch->status == 0;   @endphp

                                    <td>
                                        <a href="{{ $batchInactive? "javascript:void(0)": url('doc-profile/view-course-result/'.$value->id) }}" target="_blank" 
                                            class="btn btn-sm btn-primary {{ $batchInactive ? 'disabled':'' }}">Result</a>
                                    </td>
                        
                                    <td>{{ $value['reg_no'] }}</td>
                                    <td>{{ $value['year'] }}</td>
                                    <td>{{ (isset($value->session->name))?$value->session->name:'' }}</td>
                                    <td>{{ (isset($value->course->name))?$value->course->name:'' }}</td>
                                    <td>{{ (isset($value->subject->name))?$value->subject->name:'' }}</td>
                                    <td>
                                        {{ (isset($value->batch->name))?$value->batch->name:'' }}
                                        @if ( $batchInactive ) <span class="badge bg-danger">Batch Inactive</span> @endif
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
