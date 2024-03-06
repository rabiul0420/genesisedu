@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Faq</li>
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
                        <i class="fa fa-globe"></i>Faq Create
                        @can('Faq')
                        <a href="{{ action('Admin\FaqController@create') }}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i=1;
                        @endphp

                        @foreach($faq_details as $faq_detail)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td style="max-width: 200px; overflow: auto;">
                                    <div style="max-height: 100px; overflow: auto; width: 100%; text-align: left;">
                                        {!! isset($faq_detail->title) ? $faq_detail->title : '' !!}
                                    </div>
                                </td>
                                <td style="max-width: 200px; overflow: auto;">
                                    <div style="max-height: 100px; overflow: auto; width: 100%; text-align: left;">
                                        {!! isset($faq_detail->description) ? $faq_detail->description : '' !!}
                                    </div>
                                </td>
                                <td>{{ isset($faq_detail->priority) ?$faq_detail->priority : '' }}</td>
                                <td>{{($faq_detail->status == 1)? 'Active':'Inactive'}}</td>
                                <td>
                                    @can('Faq Edit')
                                    <a href="{{ url('admin/faq/'.$faq_detail->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan

                                    @can('Faq')
                                    {!! Form::open(array('route' => array('faq.destroy', $faq_detail->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!}
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
                "columnDefs": [
                    { "searchable": false, "targets": 8 },
                    { "orderable": false, "targets": 8 }
                ]
            })
        })
    </script>

@endsection