@extends('admin.layouts.app')

@section('content')

    <div id="main" role="main">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="{{ url('/admin') }}">Dashboard</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="{{ url('/admin/administrator') }}">Administrator List</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <b>{{ $user->id }}</b>
                </li>
            </ul>
        </div>

        <section>
            <h3>
                <span>Administrator Profile</span>
                <a class="btn btn-primary btn-xs" href='{{ url("admin/administrator/{$user->id}/edit") }}' title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
            </h3>
            <div>
                <div class="widget-body no-padding">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>name: <b>{{ $user->name ?? '' }}</b></h4>
                            <h5>Email: <b>{{ $user->email }}</b></h5>
                            <h5>Phone: <b>{{ $user->phone_number }}</b></h5>
                            <h5>
                                Roles: 
                                @foreach ($user->roles->sortBy('name') as $role)
                                <span style="padding: 2px 6px; border-radius: 5px; background: #ccc;">{{ $role->name ?? '' }}</span>
                                @endforeach
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <br>

        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-globe"></i>Activity Logs
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Change On</th>
                            <th>Activity</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($user->logs as $log)
                        <tr>
                            <td class="text-left">
                                {{ $log->id }}
                            </td>
                            <td class="text-left">
                                <a target="_blank" href="{{ $log->loghistory->path_url ?? '' }}">
                                    {{ $log->loghistory->path_title ?? '' }} {{ $log->loghistory_id ?? '' }}
                                </a>
                            </td>
                            <td class="text-left">
                                {{ $log->activity ?? '' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection













