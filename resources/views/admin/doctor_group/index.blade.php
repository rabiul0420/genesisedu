@extends('admin.layouts.app')
@section('doctor-group-class', 'active')
@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Doctor Group List</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ Session::get('class') }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <div class="row " style="display: flex; justify-content: center;">
        <div class="col-md-8 col-sm-12 ">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Doctor Group List
                        @can('Doctor Add')
                            <a href="{{route('doctor-group.create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Year</th>
                            <th>Batch</th>
                            <th>Course</th>
                            <th>Average obtained mark percent</th>
                            <th>Minimum exam attentded</th>
                            <th>Total doctos</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        
                        <tbody>
                            @foreach ($doctor_special_batches as $doctor_special_batch)
                            <tr>
                                    <td>{{ $doctor_special_batch->id }}</td>
                                    <td>{{ $doctor_special_batch->year}}</td>
                                    <td>{{ $doctor_special_batch->batch->name ?? ''}}</td>
                                    <td>{{ $doctor_special_batch->course->name ?? ''}}</td>
                                    <td>{{ $doctor_special_batch->average_obtained_mark_percent}}</td>
                                    <td>{{ $doctor_special_batch->minimum_exam_attentded }}</td>
                                    <td>{{ $doctor_special_batch->count }}</td>

                                    <td>
                                        <a style="margin-bottom:3px" href="{{ url('admin/doctors-courses?year='.($doctor_special_batch->year??'') .'&course_id='.($doctor_special_batch->course->id??'') .'&batch_id='.($doctor_special_batch->batch->id??'') ) }}" 
                                                class="btn btn-xs btn-primary">Doctor course view</a>
                                                
                                        <a href="{{ url('admin/doctor-group/'.$doctor_special_batch->id.'/edit') }}" 
                                            class="btn btn-xs btn-danger">Edit</a>
                                    </td>
                            </tr>
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

<script>

        $(document).ready(function() {
                    $('.datatable').DataTable({
                        responsive: true,
                        "ordering": true,
                        "columnDefs": [
                            { "searchable": false, "targets": 7 },
                            { "orderable": false, "targets": 7 }
                        ]
                    })
                })

</script>
@endsection
