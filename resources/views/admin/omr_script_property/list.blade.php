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
                        <i class="fa fa-globe"></i> {{ $module_name }} List
                        @can('Omr Script Property')
                            <a href="{{url('admin/omr-script-property/create')}}"> <i class="fa fa-plus"></i> </a>                            
                        @endcan
                        @can('Omr Script')
                            <a href="{{url('admin/omr-script')}}" class="btn btn-xs btn-info">Omr Scripts</a>                            
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
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($omr_script_properties as $omr_script_property)
                            <tr>
                                <td>{{ $omr_script_property->id }}</td>
                                <td>{{ $omr_script_property->name }}</td>
                                <td>{{ ($omr_script_property->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    @can('Courier')
                                    <a href="{{ url('admin/omr-script-property/'.$omr_script_property->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan
                                    @can('Courier')
                                    {!! Form::open(array('route' => array('omr-script-property.destroy', $omr_script_property->id), 'method' => 'delete','style' => 'display:inline')) !!}
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
                    { "searchable": false, "targets": 2 },
                    { "orderable": false, "targets": 2 }
                ]
            })
        })
    </script>

@endsection
