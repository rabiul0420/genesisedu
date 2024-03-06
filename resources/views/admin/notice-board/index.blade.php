@extends('admin.layouts.app')
@section('notice-board', 'active')
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
                        <i class="fa fa-globe"></i>Notice Board List
                        @can('Lecture Video')
                        <a href="{{ route('notice-board.create') }}"> <i class="fa fa-plus"></i> </a>
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
                            <th>Title</th>
                            <th>Notice</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($noticeBoards as $noticeBoard)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <th class="text-left">{{ $noticeBoard->title ?? '' }}</th>
                                <td class="text-left" style="max-width: 560px;">{!! $noticeBoard->description ?? '' !!}</td>
                                <td>{{ ($noticeBoard->status==1) ? 'Active' : 'InActive' }}</td>
                                <td>
                                    @can('Notice Board')
                                    <a href="{{ route('notice-board.show', $noticeBoard->id) }}" class="btn btn-xs btn-info">Show</a>
                                    @endcan
                                    @can('Notice Board')
                                    <a href="{{ route('notice-board.edit', $noticeBoard->id) }}" class="btn btn-xs btn-warning">Edit</a>
                                    @endcan
                                    @can('Notice Board')
                                    <form action="{{ route('notice-board.destroy', $noticeBoard->id) }}" method="POST" style="display: inline">
                                        {{method_field('Delete')}}
                                        {{ csrf_field() }}
                                        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    </form>
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
