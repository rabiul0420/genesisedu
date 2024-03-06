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
                        <i class="fa fa-globe"></i>Institute Seat Show
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body" style="max-width: 560px; margin: 30px auto;">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <tr>
                            <th>Name</th>
                            <td class="text-left">{{ $instituteAllocationSeat->instituteAllocation->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Discipline</th>
                            <td class="text-left">{{ $instituteAllocationSeat->instituteDiscipline->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Year</th>
                            <td class="text-left">{{ $instituteAllocationSeat->year ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Seat : Private</th>
                            <td class="text-left">{{ $instituteAllocationSeat->private ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Seat : Government</th>
                            <td class="text-left">{{ $instituteAllocationSeat->government ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Seat : BSMMU</th>
                            <td class="text-left">{{ $instituteAllocationSeat->bsmmu ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Seat : Armed Forces</th>
                            <td class="text-left">{{ $instituteAllocationSeat->armed_forces ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Seat : Others</th>
                            <td class="text-left">{{ $instituteAllocationSeat->others ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Action</th>
                            <td class="text-left">
                                @can('Medical College')
                                <a href="{{ route('institute-allocation-seats.edit', $instituteAllocationSeat->id) }}" class="btn btn-xs btn-warning">Edit</a>
                                @endcan
                                @can('Medical College')
                                <form action="{{ route('institute-allocation-seats.destroy', $instituteAllocationSeat->id) }}" method="POST" style="display: inline">
                                    {{method_field('Delete')}}
                                    {{ csrf_field() }}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                </form>
                                @endcan
                                @can('Medical College')
                                <a href="{{ route('institute-allocation-seats.duplicate', $instituteAllocationSeat->id) }}" class="btn btn-xs btn-info">Duplicate</a>
                                @endcan
                            </td>
                        </tr>
                    </table>
                    <div class="text-center">
                        <a class="btn btn-sm btn-info" href="{{ route('institute-allocation-seats.index') }}">Back to list</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
