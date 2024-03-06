@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>

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

                        <i class="fa fa-globe"></i>Upazila List
                        <a href="{{url('admin/upazila/create')}}"> <i class="fa fa-plus"></i> </a>

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
                            <th>Upazila Name</th>
                            <th>Upazila Bangla Name</th>
                            <th>District</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
 
                        @foreach($upazilas as $upazila)
                            <tr>
                                <td>{{ $upazila->id }}</td>
                                <td>{{ $upazila->name }}</td>
                                <td>{{ $upazila->bn_name }}</td>
                                <td>{{ $upazila->district->name }}</td>
                                <td>
                                    <a href="{{ url('admin/upazila/'.$upazila->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    {{-- {!! Form::open(array('route' => array('discount.destroy', $upazila->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!} --}}
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
