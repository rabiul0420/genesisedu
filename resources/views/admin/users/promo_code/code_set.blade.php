@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
          <li> <i class="fa fa-angle-right"></i> <a href="#">Notice</a> </li>
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
                        <i class="fa fa-globe"></i>Notice List
                        @can('Notice')
                        <a href="{{url('admin/notice/create')}}"> <i class="fa fa-plus"></i> </a>
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
                            <th>Date</th>
                            <th>Title</th>
                            <th>Attachment</th>
                            <th>Notice For</th>
                            <th>Status</th>
                            
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($notices as $notice)
                            <tr>
                                <td>{{ $notice->id }}</td>
                                <td>{{ $notice->created_at }}</td>
                                <td>{{ $notice->title }}</td>
                                <td>
                                    <?php
                                        if ($notice->attachment) {
                                            echo "<a href='".url($notice->attachment)."' target='_blank'>View Attachment</a>";
                                        }
                                    ?>
                                </td>
                                <td>{{ ($notice->type=='I')?'Individual':(($notice->type=='B')?'Batch':(($notice->type=='C')?'Course':'All')) }}</td>
                                <td>{{ ($notice->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    @can('Notice')
                                    <a href="{{ url('admin/notice_show/'.$notice->id) }}" class="btn btn-xs btn-primary">View</a>
                                    @endcan
                                    @can('Notice')
                                    <a href="{{ url('admin/notice/'.$notice->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan
                                    @can('Notice')
                                    {!! Form::open(array('route' => array('notice.destroy', $notice->id), 'method' => 'delete','style' => 'display:inline')) !!}
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
                    { "searchable": false, "targets": 3 },
                    { "orderable": false, "targets": 3 }
                ]
            })
        })
    </script>

@endsection