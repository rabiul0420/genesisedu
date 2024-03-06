@extends('admin.layouts.app')
@section('allocation-results', 'active')
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
                        <i class="fa fa-globe"></i>Allocation Discipline List
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
                            <th class="text-center">Discipline</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($disciplines as $discipline)
                            @if($discipline->doctor_course->subject)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $discipline->doctor_course->subject->name ?? '' }}</td>
                                <td>
                                    <a class="btn btn-info btn-xs" href="{{ route('allocation-results.results', [ ($discipline->doctor_course->course_id??'0') ,$exam_id, $discipline->doctor_course->subject->name]) }}">View Result</a>
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
