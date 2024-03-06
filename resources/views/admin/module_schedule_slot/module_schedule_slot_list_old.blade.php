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
                        @can('Module Schedule Slot')
                        <a href="{{url('admin/module-schedule-slot-add/'.$module_schedule->id)}}"> <i class="fa fa-plus"></i> </a>
                        <a href="{{ url('admin/module-schedule-list/'.$module_schedule->module->id) }}" class="btn btn-xs btn-primary">Module Schedule</a>
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
                            <th>Module Schedule</th>
                            <th>Branch</th>
                            <th>Location</th>
                            <th>Floor</th>
                            <th>Room</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                            @foreach($module_schedule_slot_lists as $module_schedule_slot_list)
                                <tr>
                                    <td>{{ $module_schedule_slot_list->id }}</td>
                                    <td>{{ $module_schedule_slot_list->module_schedule->name ?? '' }}</td>
                                    <td>{{ $module_schedule_slot_list->slot->room->branch->name??'' }}</td>
                                    <td>{{ $module_schedule_slot_list->slot->room->location->name??'' }}</td>
                                    <td>{{ $module_schedule_slot_list->slot->room->floor??'' }}</td>
                                    <td>{{ $module_schedule_slot_list->slot->room->name??'' }}</td>
                                    <td>{{ $module_schedule_slot_list->slot->start_time??'' }}</td>
                                    <td>{{ $module_schedule_slot_list->slot->end_time??'' }}</td>
                                    <td>
                                        @can('Module Schedule Slot')
                                        <a href="{{ url('admin/module-schedule-slot-edit/'.$module_schedule_slot_list->id ) }}" class="btn btn-xs btn-primary">Edit</a>
                                        @endcan

                                        @can('Module Schedule Slot')
                                        <a href="{{ url('admin/module-schedule-slot-list/'.$module_schedule_slot_list->id ) }}" class="btn btn-xs btn-info">Program</a>
                                        @endcan

                                        @can('Module Schedule Slot')
                                        <a href="{{ url('admin/module-schedule-slot-delete/'.$module_schedule_slot_list->id) }}" class="btn btn-xs btn-danger">Delete</a>
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
