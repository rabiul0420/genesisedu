@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Administrator List</li>
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
                        <i class="fa fa-globe"></i>Administrator List
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>User Work Email</th>
                            <th>User Phone</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="text-left">
                                    <a href="{{ url('admin/administrator/'.$user->id) }}">
                                        {{ $user->name }}
                                        <span class="text-danger">{{ $user->id === auth()->id() ? "(You)" : '' }}</span>
                                    </a>
                                </td>
                                <td class="text-left">{{ $user->email }}</td>
                                <td class="text-left">{{ $user->phone_number }}</td>
                                <td>
                                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                        @foreach ($user->roles->sortBy('name') as $role)
                                        <span style="padding: 4px 8px; border-radius: 5px; background: #ccc;">{{ $role->name ?? '' }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>{{ ($user->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    <a href="{{ url('admin/administrator/'.$user->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    {!! Form::open(array('route' => array('administrator.destroy', $user->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!}
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
                    { "searchable": false, "targets": 6 },
                    { "orderable": false, "targets": 6 }
                ]
            })
        })
    </script>

@endsection