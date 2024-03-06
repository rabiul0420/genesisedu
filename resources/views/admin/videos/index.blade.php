@extends('admin.layouts.app')
@section('videos', 'active')
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
                        <i class="fa fa-globe"></i>Video List
                        @can('Lecture Video')
                        <a href="{{ route('videos.create') }}"> <i class="fa fa-plus"></i> </a>
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
                            <th>Url</th>
                            <th>Site Link</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($videos as $video)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $video->name ?? '' }}</td>
                                <td class="text-left">{{ $video->url ?? '' }}</td>
                                <td class="text-left">{{ url("v/{$video->id}") }}</td>
                                <td>
                                    @can('Free Videos')
                                    <a href="{{ route('videos.show', $video->id) }}" class="btn btn-xs btn-info">Show</a>
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
