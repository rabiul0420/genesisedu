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
                        <i class="fa fa-globe"></i>Alumni Create
                        @can('Add Alumni')
                        <a href="{{ action('Admin\InformationalumniController@create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Doctor</th>
                            <th>Course</th>
                            <th>Result</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i=1;
                        @endphp

                        @foreach($informationalumni as $informationalumn)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td style="max-width: 200px; overflow: auto;">
                                    <div style="max-height: 100px; overflow: auto; width: 100%; text-align: left;">
                                        {{ $informationalumn->doctor->name ?? ' '}}
                                       
                                    </div>
                                </td>
                                
                                <td> {{ $informationalumn->course->name ?? ' '}}</td>
                                <td>{{ isset($informationalumn->result) ?$informationalumn->result : '' }}</td>
                                <td>{{ isset($informationalumn->email) ?$informationalumn->email : '' }}</td>
                                <td>{{ isset($informationalumn->phone) ?$informationalumn->phone : '' }}</td>
                               
                                <td>
                                    @can('Edit Alumni')
                                    
                                    <a href="{{ url('admin/information-alumni/'.$informationalumn->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan

                                    @can('Delete Alumni')
                                    {!! Form::open(array('route' => array('information-alumni.destroy', $informationalumn->id), 'method' => 'delete','style' => 'display:inline')) !!}
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