@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls='';
            ?>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i> List
                        @can('Exam Batch')
                        <a href="{{url('admin/exam-batch/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Year</th>
                            <th>Session</th>
                            <th>Branch</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($data as $exam_batch)
                            <tr>
                                <td>{{ $exam_batch->id }}</td>
                                <td>{{ $exam_batch->year }}</td>

                                <td>{{ $exam_batch->session->name??''}}</td>
                                <td>{{ $exam_batch->branch->name??''  }}</td>
                                <td>{{ $exam_batch->institute->name??'' }}</td>
                                <td>{{ $exam_batch->course->name??'' }}</td>
                                <td>{{ isset($exam_batch->batch->name)?$exam_batch->batch->name : '' }}</td>

                                <td>{{ ($exam_batch->status==1) ? 'Active' : 'InActive' }}</td>
                                <td>
  
                                    @can('Exam Batch')
                                   
                                    <a onclick="return confirm('Are You Sure ?')" href="{{ url('/admin/exam-batch-restore/' .$exam_batch->id) }}" class='btn btn-xs btn-success' type="submit">Restore</a>
                                  
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                "columnDefs": [
                    { "searchable": false, "targets": 8 },
                    { "orderable": false, "targets": 8 }
                ]
            })
        })
    </script>

@endsection