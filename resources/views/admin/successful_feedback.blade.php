@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Successful Feedback List</li> 
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Successful Feedback List                     
                    </div>
                </div>

                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Bmdc No</th>                          
                                <th>Batch </th>
                                <th>Medical College </th>
                                <th>Year</th>
                                <th>Mobile No</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($successful_feedback as $successful_feedback)
                                <tr>
                                    <td>{{ $successful_feedback->id }}</td>
                                    <td>{{ $successful_feedback->name }}</td>
                                    <td>{{ $successful_feedback->bmdc_no }}</td>                              
                                    <td>{{ $successful_feedback->batch_name }}</td>
                                    <td>{{ $successful_feedback->medical_college->name??'' }}</td>
                                    <td>{{ $successful_feedback->year }}</td> 
                                    <td>{{ $successful_feedback->mobile_number }}</td>
                                    <td><a class="btn btn-primary btn-sm" href="successful_feedback_view/{{$successful_feedback->id}}">view</a></td>                           
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
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                searchable:true,
            })
        })
    </script>

@endsection









