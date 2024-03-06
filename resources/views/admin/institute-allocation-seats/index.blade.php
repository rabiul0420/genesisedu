@extends('admin.layouts.app')
@section('institute-allocation-seats', 'active')
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
                        <i class="fa fa-globe"></i>Institute Seat List
                        @can('Exam Institute Seat')
                        <a href="{{ route('institute-allocation-seats.create') }}"> <i class="fa fa-plus"></i> </a>
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
                            <th class="text-center">SL</th>
                            <th>Name</th>
                            <th>Discipline</th>
                            <th>Course</th>
                            <th class="text-center">Year</th>
                            <th class="text-center">Seat : Private</th>
                            <th class="text-center">Seat : Government</th>
                            <th class="text-center">Seat : BSMMU</th>
                            <th class="text-center">Seat : Armed Forces</th>
                            <th class="text-center">Seat : Others</th>
                            <th class="text-center">Seat : Total</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($instituteAllocationSeats as $instituteAllocationSeat)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $instituteAllocationSeat->instituteAllocation->name ?? '' }}</td>
                                <td class="text-left">{{ $instituteAllocationSeat->instituteDiscipline->name ?? '' }}</td>
                                <td class="text-left">{{ $instituteAllocationSeat->course->name ?? '' }}</td>
                                <td>{{ $instituteAllocationSeat->year ?? '' }}</td>
                                <td>{{ $instituteAllocationSeat->private ?? 0 }}</td>
                                <td>{{ $instituteAllocationSeat->government ?? 0 }}</td>
                                <td>{{ $instituteAllocationSeat->bsmmu ?? 0 }}</td>
                                <td>{{ $instituteAllocationSeat->armed_forces ?? 0 }}</td>
                                <td>{{ $instituteAllocationSeat->others ?? 0 }}</td>
                                <td>{{
                                    ($instituteAllocationSeat->private ?? 0) + 
                                    ($instituteAllocationSeat->government ?? 0) + 
                                    ($instituteAllocationSeat->bsmmu ?? 0) + 
                                    ($instituteAllocationSeat->armed_forces ?? 0) + 
                                    ($instituteAllocationSeat->others ?? 0)
                                }}</td>
                                <td>
                                    @can('Exam Institute Seat')
                                    <a href="{{ route('institute-allocation-seats.show', $instituteAllocationSeat->id) }}" class="btn btn-xs btn-info">Show</a>
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
