@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>User Complain Assign List</li>
        </ul>
    </div>

    @if (Session::has('message'))
        <div class="alert {{ Session::get('class') ?? 'alert-success' }} " role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>User Complain Assign List
                        <a href="{{ action('Admin\UserComplainAssignController@create') }}"> <i
                                class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Name</th>
                                <th>Complain Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user_complain_assigns as $user_complain_assign)
                                <?php
                                $course_complain_type_ids = json_decode($user_complain_assign->course_complain_type_id);                                
                                $courseComplainTypes = $course_complain_types instanceof Illuminate\Support\Collection ? $course_complain_types->whereIn('id', $course_complain_type_ids) : [];
                                
                                ?>
                                <tr>
                                    <td>{{ $user_complain_assign->id }}</td>
                                    <td>{{ $user_complain_assign->user->name ?? ' '}}</td>

                                    <td>
                                        @foreach ($courseComplainTypes as $course_complain_type)
                                            <p>{{ $course_complain_type->course->name ?? '' }} ---
                                                {{ $course_complain_type->complain_type->name ?? '' }}</p>
                                        @endforeach
                                    </td>

                                    <td>{{ $user_complain_assign->status ? 'Active' : 'In-Active' }}</td>
                                    <td>
                                        <a href="{{ url('admin/user-complain-assign/' . $user_complain_assign->id . '/edit') }}"
                                            class="btn btn-primary">Edit</a>
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


    </script>

@endsection
