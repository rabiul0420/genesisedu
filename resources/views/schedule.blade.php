@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')


        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Schedule</h3></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <h4><b>Batch List </b></h4>
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Batch Name</th>
                                            <th>Schedule Name</th>
                                            <th>Course</th>
                                            <th>Type</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($doc_info->doctorcourses as $k=>$value)
                                            @if(isset($value->schedule->id) && $value->is_trash=='0' && $value->status ==1)
                                                
                                                <tr>
                                                    <td>{{ $k+1 }}</td>
                                                    <td>{{ $value->schedule->batch->name }}</td>
                                                    <td>{{ $value->schedule->name }}</td>
                                                    <td>{{ $value->schedule->course->name }}</td>
                                                    <td>{{ $value->schedule->type }}</td>
                                                    <td>
                                                        <a href="{{ url("doc-profile/print-batch-schedule/".$value->schedule->id) }}" class='btn btn-sm btn-primary' target='_blank'>View Schedule</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>

    </div>


</div>
@endsection
