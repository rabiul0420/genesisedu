@extends('admin.layouts.app')
@section('medical-colleges', 'active')
@section('content')

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
                        <i class="fa fa-globe"></i>Medical College List
                        @can('Lecture Video')
                        <a href="{{ route('medical-colleges.create') }}"> <i class="fa fa-plus"></i> </a>
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
                            <th>SL</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($medicalColleges as $medicalCollege)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $medicalCollege->name ?? '' }}</td>
                                <td class="text-left">{{ $medicalCollege->type ?? '' }}</td>
                                <td>{{ ($medicalCollege->status==1) ? 'Active' : 'InActive' }}</td>
                                <td class="text-left">{{ $medicalCollege->user->name ?? '' }}</td>
                                <td>
                                    @can('Medical College')
                                    <a href="{{ route('medical-colleges.show', $medicalCollege->id) }}" class="btn btn-xs btn-info">Show</a>
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
            })
        })
    </script>

@endsection
