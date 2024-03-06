@extends('admin.layouts.app')
@section('institute-disciplines', 'active')
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
                        <i class="fa fa-globe"></i>Discipline List
                        @can('Lecture Video')
                        <a href="{{ route('institute-disciplines.create') }}"> <i class="fa fa-plus"></i> </a>
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
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($instituteDisciplines as $instituteDiscipline)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $instituteDiscipline->name ?? '' }}</td>
                                <td>
                                    @can('Institute')
                                    <a href="{{ route('institute-disciplines.show', $instituteDiscipline->id) }}" class="btn btn-xs btn-info">Show</a>
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
