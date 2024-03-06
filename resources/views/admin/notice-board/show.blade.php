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
                        <i class="fa fa-globe"></i>Notice Board Show
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body" style="max-width: 560px; margin: 30px auto;">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <tr>
                            <th>Title</th>
                            <td class="text-left">{{ $noticeBoard->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Notice</th>
                            <td class="text-left">{!! $noticeBoard->description ?? '' !!}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td class="text-left">{{ ($noticeBoard->status==1) ? 'Active' : 'InActive' }}</td>
                        </tr>
                        <tr>
                            <th>Action</th>
                            <td class="text-left">
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
                    </table>
                    <div class="text-center">
                        <a class="btn btn-sm btn-info" href="{{ route('notice-board.index') }}">Back to list</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
