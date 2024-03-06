@extends('admin.layouts.app')

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}"> Home </a><i class="fa fa-angle-right"></i>
            </li>
            <li>Batches Trash List</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Batches Trash List
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Batch ID</th>
                                <th>Course Name</th>
                                <th>Displaying Batch Name</th>
                                <th>Main Batch</th>
                                <th>Starting from</th>
                                <th>Days</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i=1;
                            @endphp

                            @foreach($data as $batch_trash)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $batch_trash->id ?? '' }}</td>
                                    <td>{{ $batch_trash->course_name ?? '' }}</td>
                                    <td>{{ $batch_trash->batch_name ?? '' }}</td>
                                    <td>{{ $batch_trash->name ?? '' }}</td>
                                    <td>{{ $batch_trash->start_date ?? '' }}</td>
                                    <td>{{ $batch_trash->days ?? '' }}</td>
                                    <td>{{ $batch_trash->time ?? '' }}</td>
                                    <td>{{ $batch_trash->status == 1 ? "Active" : 'Inactive' }}</td>

                                    {{-- Restore Button --}}
                                    <td>
                                        @can('Available Batch Delete')
                                        <a onclick="return confirm('Are You Sure ?')" href="{{ url('/admin/available-batches-restore/' .$batch_trash->id) }}" class='btn btn-xs btn-info' type="submit">Restore</a>
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