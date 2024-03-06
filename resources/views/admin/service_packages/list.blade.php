@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'">'.$value.'</a> </li>';
            }
            ?>
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
                        <i class="fa fa-globe"></i><?php echo $module_name;?> List
                        <a href="{{url('admin/service-packages/create')}}"> <i class="fa fa-plus"></i> </a>
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
                            <th>Service Package Name</th>
                            <th>Status</th>
                            {{--<th>Created By</th>
                            <th>Created At</th>--}}
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($service_packages as $service_package)
                            <tr>
                                <td>{{ $service_package->id }}</td>
                                <td>{{ $service_package->name }}</td>
                                <td>{{ ($service_package->status==1)?'Active':'InActive' }}</td>
                                {{--<td>{{ ($service_package->created_by)?$service_package->user->name:'' }}</td>
                                <td>{{ ($service_package->created_at)?date('Y-m-d',strtotime($service_package->created_at)):'' }}</td>--}}
                                <td>
                                    @can('Service Package Edit')
                                    <a href="{{ url('admin/service-packages/'.$service_package->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan
                                    @can('Service Package Delete')
                                        {!! Form::open(array('route' => array('service-packages.destroy', $service_package->id), 'method' => 'delete','style' => 'display:inline')) !!}
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