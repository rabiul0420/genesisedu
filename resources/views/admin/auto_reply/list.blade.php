@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Auto Reply</li>
        </ul>
    </div>

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
                        <i class="fa fa-globe"></i>Auto Reply Link
                        <a href="{{ action('Admin\AutoReplyController@create') }}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>title</th>
                            <th>Auto Reply_link</th>                          
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i=1;
                        @endphp

                        @foreach($questionlink as $questionlink)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td style="max-width: 200px; overflow: auto;">
                                    <div style="max-height: 100px; overflow: auto; width: 100%; text-align: left;">
                                        {!! isset($questionlink->title) ? $questionlink->title : '' !!}
                                    </div>
                                </td>
                                <td style="max-width: 200px; overflow: auto;">
                                    <div style="max-height: 100px; overflow: auto; width: 100%; text-align: left;">
                                        {!! isset($questionlink->question_link) ? $questionlink->question_link : '' !!}
                                    </div>
                                </td>
                                
                                <td>
                                    <a href="{{ url('admin/auto-reply-link/'.$questionlink->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>


                                    {!! Form::open(array('route' => array('auto-reply-link.destroy', $questionlink->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!}
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



@endsection