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
                        @can('Add Institute')
                        <a href="{{url('admin/institutes/create')}}"> <i class="fa fa-plus"></i> </a>
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
                            <th>Institue Name</th>
                            <th>Has Faculty?</th>
                            <th>Reference Code</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($institutes as $institute)
                            <tr>
                                <td>{{ $institute->id }}</td>
                                <td>{{ $institute->name }}</td>
                                <td>{{ ($institute->type==1)?'Yes':'No' }}</td>
                                <td>{{ $institute->reference_code }}</td>
                                <td>{{ ($institute->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    @can('Edit Institute')
                                    <a href="{{ url('admin/institutes/'.$institute->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>  
                                    @endcan
                                    @can('Delete Institute')
                                    {!! Form::open(array('route' => array('institutes.destroy', $institute->id), 'method' => 'delete','style' => 'display:inline')) !!}
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
                    { "searchable": false, "targets": 4 },
                    { "orderable": false, "targets": 4 }
                ]
            })
        })
    </script>

@endsection