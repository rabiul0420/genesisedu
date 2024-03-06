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
            <p> {!! Session::get('message') !!}</p>
        </div>
    @endif

    <style>
        
        input[type=checkbox][disabled] {
            outline: 5px solid #31b0d5;
            outline-offset: -20px;
        }

        .title-content
        {
            padding: 20px;
            font-size: 27px;
            text-align: center;
            font-weight:700;
            color:blueviolet;
        }

    </style>


    <div class="row title-content">
        {{ $room->name }} Slot List
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>{{ $room->name }} Slot List
                        @can('Room Slot')
                        <a href="{{url('admin/room-slot-add/'.$room->id)}}"> <i class="fa fa-plus"></i> </a>
                        <a href="{{ url('admin/room') }}" class="btn btn-xs btn-primary">Room</a>
                        @endcan
                    </div>
                </div>
                <div style="font-weight:700;text-align:center;">
                    <div class="caption">
                    
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Start Time - End Time</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                            @foreach($room_slot_lists as $room_slot_list)
                                <tr>
                                    <td>{{ $room_slot_list->id }}</td>
                                    <td>{{ $room_slot_list->room->name??'' }}</td>
                                    <td>{{ $room_slot_list->custom_date() }}</td>
                                    <td>{{ $room_slot_list->hrstart_time().' - '.$room_slot_list->hrend_time() }}</td>
                                    <td>
                                        @can('Room Slot')
                                        <a href="{{ url('admin/room-slot-edit/'.$room_slot_list->id ) }}" class="btn btn-xs btn-primary">Edit</a>
                                        @endcan

                                        @can('Room Slot')
                                        <a href="{{ url('admin/room-slot-delete/'.$room_slot_list->id) }}" class="btn btn-xs btn-danger">Delete</a>
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
