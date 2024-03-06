@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Executive</li>
        </ul>
    </div>

    @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('success') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Executive Course List
                        @can('Teacher Add')
                            <a href="{{url('admin/executive-course/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Course Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($executive_courses as $executive_course)
                            
                                <tr>
                                    <td>{{ $executive_course->id }}</td>
                                    <td>{{ $executive_course->executive->name ?? ' ' }}</td>
                                    <td>{{ $executive_course->course->name ?? ' ' }}</td>
                                    <td style="color: {{  $executive_course->status == 1 ? 'green':'#aaa' }}; font-weight: bold;">
                                        {!! $executive_course->status == 1 ? 'Active':'Inactive' !!}
                                    </td>
                                    <td>
                                        @can('Executive Course')
                                        <a href="{{ url('admin/executive-course/'.$executive_course->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                        @endcan
                                        @can('Executive Course')
                                        {!! Form::open(array('route' => array('executive-course.destroy', $executive_course->id), 'method' => 'delete','style' => 'display:inline')) !!}
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
        $(document).ready(function() {
            $('.datatable').DataTable({
               
            })
        })
    </script>

@endsection