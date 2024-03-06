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
                        <a href="{{ url('admin/module-schedule-print/'.$module_schedule->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>
                        @endcan
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body">
                    <style>
                        
                        .action-button
                        {
                            margin: 5px;
                        }
                        .custom-video-link
                        {
                            margin: 7px;
                            padding: 7px;
                            border: 3px solid magenta;
                            border-radius: 15px;

                        }

                        .custom-live-link
                        {
                            margin: 7px;
                            padding: 7px;
                            border: 3px solid blue;
                            border-radius: 15px;

                        }

                        .custom-exam-link
                        {
                            margin: 7px;
                            padding: 7px;
                            border: 3px solid blueviolet;
                            border-radius: 15px;

                        }

                        .link
                        {
                            margin: 2px;
                            padding: 2px;
                            display: block;
                        }
                    </style>
                    <table class="table table-bordered table-hover datatable">
                        <thead style="background-color:#428bca;color:white;">
                            <th style="text-align:center;">Date</th>
                            @for($i = $module_schedule->max_slots(); $i>0;$i--)
                            <th style="text-align:center;"><span>Room</span><br><span>Start Time - End Time</span></th>
                            @endfor
                        </thead>
                        <tbody>
                            
                            @foreach($module_schedule->array_custom_slot_list() as $k=>$array_slot)
                                @php $m = 0; $date = explode("-",$k) ;@endphp
                                <tr>
                                    <td><b>{{ $date[2].'-'.$date[1].'-'.$date[0] }}</b></td>
                                    @foreach($array_slot as $l=>$slot)
                                    <td>
                                    <span><a href="{{ $slot->edit_link() }}" class="btn btn-xs btn-primary action-button" title="Edit Schedule Slot"><i class="fa fa-edit"></i></a></span><span><a onclick="return confirm('Are you sure to delete this slot from module schedule?');" href="{{ $slot->delete_link() }}" class="btn btn-xs btn-danger action-button"  title="Delete Schedule Slot"><i class="fa fa-trash-o"></i></a></span><br>
                                    <span>{{ $slot->room_name() }}</span><br><span><b>{{ $slot->time_span() }}</b></span><br>
                                    @if($slot->program_id <= 0)
                                    <span><a href="{{ $slot->program_edit_link() }}" class="btn btn-xs btn-info action-button" title="Add Slot Program"><i class="fa fa-plus"></i></a></span>
                                    @elseif($slot->program_id > 0)
                                    <span><a href="{{ $slot->program_edit_link() }}" class="btn btn-xs btn-info action-button" title="Edit Slot Program"><i class="fa fa-edit"></i></a></span>
                                    <span><a onclick="return confirm('Are you sure to delete this slot from module schedule?');" href="{{ $slot->program_delete_link() }}" class="btn btn-xs btn-warning action-button"  title="Delete Slot Program"><i class="fa fa-trash-o"></i></a></span><br><br>
                                    @endif
                                    @if(isset($slot->program))
                                    <span>{{ $slot->program->name ?? '' }}</span><br><br>
                                    @if(in_array('Recorded', $slot->program->media_types()))
                                    @foreach($slot->program->lecture_videos() as $lecture_video)
                                    <span class="link"><a class="custom-video-link" href="#">{{ $lecture_video->name ?? '' }} <i class="fa fa-play-circle-o" style="font-size:larger;"></i></a></span><br>  
                                    @endforeach
                                    @endif
                                    @if(in_array('Online', $slot->program->media_types()))
                                    @foreach($slot->program->exams() as $exam)
                                    <span class="link"><a class="custom-exam-link" href="#">{{ $exam->name ?? '' }} <i class="fa fa-graduation-cap" style="font-size:larger;"></i></a></span><br>  
                                    @endforeach
                                    @endif
                                    @if(in_array('Live', $slot->program->media_types()) && $slot->slot->room->live_link )
                                    <span class="link"><a class="custom-live-link" href="{{ $slot->slot->room->live_link }}">{{ $slot->slot->room->name ?? '' }}  <span class="fa-stack fa-1x"> <i class="fa fa-television fa-stack-2x"></i> <i class="fa fa-users fa-stack-1x"></i></span></a></span><br>
                                    @endif
                                    @if(in_array('Offline', $slot->program->media_types()))                                                    
                                    @endif
                                    @endif    
                                    </td>
                                    @php $m++ @endphp
                                    @endforeach

                                    @if($m < $module_schedule->max_slots() )
                                    @for($m;$m < $module_schedule->max_slots();$m++)
                                    <td></td>
                                    @endfor
                                    @endif
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
