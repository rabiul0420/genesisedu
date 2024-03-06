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
                        <i class="fa fa-globe"></i>{{ $module_name }} List
                        @can('Module Schedule')
                        <a href="{{url('admin/module-schedule-add/'.$module->id)}}"> <i class="fa fa-plus"></i> </a>
                        <a href="{{ url('admin/module') }}" class="btn btn-xs btn-primary">Module</a>
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
                            <th>Module</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                            @foreach($module_schedule_lists as $module_schedule_list)
                                <tr>
                                    <td>{{ $module_schedule_list->id }}</td>
                                    <td>{{ $module_schedule_list->name }}</td>
                                    <td>{{ $module_schedule_list->module->name??'' }}</td>
                                    <td>{{ $module_schedule_list->contact_details }}</td>
                                    <td>
                                        @can('Module Schedule')
                                        <a href="{{ url('admin/module-schedule-edit/'.$module_schedule_list->id ) }}" class="btn btn-xs btn-primary">Edit</a>
                                        @endcan

                                        @can('Module Schedule')
                                        <a href="{{ url('admin/module-schedule-slot-list/'.$module_schedule_list->id ) }}" class="btn btn-xs btn-info">Schedule Slots</a>
                                        @endcan

                                        @can('Module Schedule')
                                            <a href="{{ url('admin/module-schedule-print/'.$module_schedule_list->id ) }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i> Print</a>
                                        @endcan

                                        @can('Module Schedule')
                                        <a href="{{ url('admin/module-schedule-delete/'.$module_schedule_list->id) }}" class="btn btn-xs btn-danger">Delete</a>
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

@endsection
