@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                Lectures Exams Save
            </li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Lectues Exams Save
                    </div>
                </div>
                <div class="portlet-body form">
                    <div id="schedule_details_id" style="display: none;">{{$schedule_details->id}}</div>
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\BatchesSchedulesController@update',$schedule_details->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Batch Schedule Information</div>
                            <div class="panel-body">

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Schedule Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="name" value="{{ $schedule_details->name?$schedule_details->name:'' }}" class="form-control" >
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Schedule Sub Line </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="tag_line" value="{{ $schedule_details->tag_line?$schedule_details->tag_line:'' }}" class="form-control" >
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Schedule Address (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="address" value="{{ $schedule_details->address?$schedule_details->address:'' }}" class="form-control" >
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-1">Room Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-lg-3">
                                        @php  $rooms_types->prepend('Select Room', ''); @endphp
                                        {!! Form::select('room_id',$rooms_types, $schedule_details->room_id?$schedule_details->room_id:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                    </div>

                                    <label class="col-md-1 control-label">Schedule Contact Details (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)  </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="contact_details" value="{{ $schedule_details->contact_details?$schedule_details->contact_details:'' }}" class="form-control" >
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Schedule Type (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            {!! Form::select('type',$schedule_types, $schedule_details->type?$schedule_details->type:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                        </div>
                                    </div>

                                </div>



                                <div class="form-group">

                                    <label class="col-md-1 control-label">Service Package (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $service_packages->prepend('Select Service Package', ''); @endphp
                                            {!! Form::select('service_package_id',$service_packages, $schedule_details->service_package_id?$schedule_details->service_package_id:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Executive (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $executive_list->prepend('Select Executive', ''); @endphp
                                            {!! Form::select('executive_id',$executive_list, $schedule_details->executive_id?$schedule_details->executive_id:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Support Staff (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $support_stuff_list->prepend('Select Support Stuff', ''); @endphp
                                            {!! Form::select('support_stuff_id',$support_stuff_list, $schedule_details->support_stuff_id?$schedule_details->support_stuff_id:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>



                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Batch Schedule Course Information</div>
                            <div class="panel-body">

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Paper</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            {!! Form::select('paper',$papers, $schedule_details->paper?$schedule_details->paper:'' ,['class'=>'form-control']) !!}<i></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="years">
                                    <div class="form-group">
                                        <label class="col-md-1 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                {!! Form::select('year',$years, $schedule_details->year?$schedule_details->year:'' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sessions">
                                    <div class="form-group">
                                        <label class="col-md-1 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                @php  $sessions->prepend('Select Session', ''); @endphp
                                                {!! Form::select('session_id',$sessions, $schedule_details->session_id?$schedule_details->session_id:'' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="institutes">
                                    <div class="form-group">
                                        <label class="col-md-1 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                @php  $institutes->prepend('Select Institute', ''); @endphp
                                                {!! Form::select('institute_id',$institutes, $schedule_details->institute_id?$schedule_details->institute_id:'' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="courses">
                                    <div class="form-group">
                                        <label class="col-md-1 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                @php  $courses->prepend('Select Course', ''); @endphp
                                                {!! Form::select('course_id',$courses, $schedule_details->course_id?$schedule_details->course_id:'' ,['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="batches">
                                    <div class="form-group">
                                        <label class="col-md-1 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                @php  $batches->prepend('Select Batch', ''); @endphp
                                                {!! Form::select('batch_id',$batches, $schedule_details->batch_id?$schedule_details->batch_id:'' ,['class'=>'form-control','required'=>'required','id'=>'batch_id']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Batch Schedule Slot Information</div>
                            <div class="panel-body">
                                <div class="form-group">

                                    <label class="control-label col-lg-1">Initial Schedule Date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-lg-3">
                                        <input type="text" name="initial_date" id="initial_date" autocomplete="off" onchange="change_wd_value(this.value)" value="{{ $schedule_details->initial_date?date('Y-m-d',date_create_from_format('Y-m-d h:i:s',$schedule_details->initial_date)->getTimestamp()):'' }}" required class="form-control input-append date">
                                    </div>

                                    <div class="wd_ids">
                                        <label class="col-md-1 control-label">Select Batch Days (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                {!! Form::select('wd_ids[]',$week_days, $wd_ids?$wd_ids:'' ,[ 'id'=>'id_wd_ids','class'=>'form-control select2 ', 'multiple' => 'multiple','required'=>'required']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>



                                </div>

                                <div class="form-group">

                                    <label class="col-md-1 control-label">Select Slot One (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $slots_list->prepend('Select Slot', ''); @endphp
                                            {!! Form::select('slot_type[]',$slots_list, !empty($batch_slots[0])?$batch_slots[0]->slot_type:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Select Slot Time One (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control timepicker" required name="start_time[]" value="{{ !empty($batch_slots[0])?$batch_slots[0]->start_time:'' }}">
                                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                                            <span class="input-group-addon">To</span>
                                            <input type="text" class="form-control timepicker" required  name="end_time[]" value="{{ !empty($batch_slots[0])?$batch_slots[0]->end_time:'' }}">
                                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">

                                    <label class="col-md-1 control-label">Select Slot Two</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $slots_list->prepend('Select Slot', ''); @endphp
                                            {!! Form::select('slot_type[]',$slots_list, !empty($batch_slots[1])?$batch_slots[1]->slot_type:'' ,['class'=>'form-control']) !!}<i></i>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Select Slot Time Two</label>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control timepicker" name="start_time[]" value="{{ !empty($batch_slots[1])?$batch_slots[1]->start_time:'' }}">
                                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                                            <span class="input-group-addon">To</span>
                                            <input type="text" class="form-control timepicker" name="end_time[]" value="{{ !empty($batch_slots[1])?$batch_slots[1]->end_time:'' }}">
                                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">

                                    <label class="col-md-1 control-label">Select Slot Three</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $slots_list->prepend('Select Slot', ''); @endphp
                                            {!! Form::select('slot_type[]',$slots_list, !empty($batch_slots[2])?$batch_slots[2]->slot_type:'' ,['class'=>'form-control']) !!}<i></i>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Select Slot Time Three</label>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control timepicker" name="start_time[]" value="{{ !empty($batch_slots[2])?$batch_slots[2]->start_time:'' }}">
                                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                                            <span class="input-group-addon">To</span>
                                            <input type="text" class="form-control timepicker" name="end_time[]" value="{{ !empty($batch_slots[2])?$batch_slots[2]->end_time:'' }}">
                                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">

                                    <label class="col-md-1 control-label">Select Slot Four</label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $slots_list->prepend('Select Slot', ''); @endphp
                                            {!! Form::select('slot_type[]',$slots_list, !empty($batch_slots[3])?$batch_slots[3]->slot_type:'' ,['class'=>'form-control']) !!}<i></i>
                                        </div>
                                    </div>

                                    <label class="col-md-1 control-label">Select Slot Time Four</label>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control timepicker" name="start_time[]" value="{{ !empty($batch_slots[3])?$batch_slots[3]->start_time:'' }}">
                                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                                            <span class="input-group-addon">To</span>
                                            <input type="text" class="form-control timepicker" name="end_time[]" value="{{ !empty($batch_slots[3])?$batch_slots[3]->end_time:'' }}">
                                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-1 control-label"> Status</label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Update</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>





    <div class="row">
        <div class="col-lg-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Lectues Exams Class/Chapter Save
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="panel panel-primary" style=" border-color: #eee;" >
                        <div class="panel-heading"  style="background-color: #eee; color: black; border-color: #eee; ">
                            Add Batch Schedules Class/Chapter
                        </div>

                        <div class="panel-body">

                            <!-- BEGIN FORM-->
                            {!! Form::open(['action'=>['Admin\BatchesSchedulesController@save_batch_schedule_lectures_exams',$schedule_details->id],'files'=>true,'class'=>'form-horizontal']) !!}
                            <input type="hidden" name="schedule_id" value="<?php echo $schedule_details->id; ?>">
                            <table class="table table-bordered table-striped routine-rooms table_routine" id="id_table">
                                <tr align="center" bgcolor="#d2d2d2">
                                    <th>Date</th>
                                    @php $count_slots =  0; @endphp
                                    @php $m_slot_lists = array(); @endphp
                                    @php $count_rows = count($topics_list); @endphp
                                    @php $count_slots_all = count($batch_slots); @endphp
                                    @foreach ($batch_slots as $value)
                                        @if ($value['slot_type'])
                                            @php $count_slots++; @endphp
                                            @php $m_slot_lists[$count_slots] = $value['slot_type']; @endphp
                                            @if ($value['slot_type'] == '3')
                                                <th style="vertical-align: middle !important;text-align: center;border-left-width: 1px;border-bottom-width: 1px;" width="15%" id="id_break" rowspan="{{($count_rows + 1)}}">{{ $value->slot->slot_name }} <br> {{ $value['start_time'] }} TO {{ $value['end_time'] }} </th>
                                            @else
                                                <th style="vertical-align: middle !important;text-align: center;border-left-width: 1px;border-bottom-width: 1px;"width=""> {{ $value->slot->slot_name }} <br> {{ $value['start_time'] }} - TO - {{ $value['end_time'] }}</th>
                                            @endif
                                        @endif
                                    @endforeach
                                </tr>

                                @if(!empty($topics_list))
                                    @php $day_increament = 0; @endphp
                                    @php $i=1; @endphp
                                    @php $d=0; @endphp

                                    @foreach($schedule_dates as $key=>$schedule_date)

                                        <tr id="{{ 'row_'.$d }}">
                                            @for($c = 0 ;$c<=($count_slots);$c++)
                                                @if($c=='0')
                                                    <td  style="border-left-width: 1px;border-bottom-width: 1px;">
                                                        <div style="text-align:left;"><span id="{{ $d }}" onclick="remove_item(this.id)" style="cursor: pointer;" title="remove"><i class="fa fa-minus-circle"></i></span></div>
                                                        <div class="">

                                                            <div style="text-align:right;" data-date-viewmode="years" data-date-format="yyyy-mm-dd" data-date="{{ date("Y-m-d", strtotime($schedule_details->initial_date)) }}" class="">
                                                                {{--<input type="text"  name="schedule_dateggg" id="{{'schedule_date_main'.'__1'.'_'.$i.'_'.$c }}" onchange="change_df_value(this.id,this.value,{{ $count_slots+1 }})" value="{{date("Y-m-d", strtotime("+$day_increament day", strtotime($schedule_details->initial_date)))}}" size="16" class="form-control input-append date">
                                                                --}}
                                                                <input type="text"  name="schedule_dateggg" id="{{'schedule_date_main'.'__1'.'_'.$d.'_'.$c }}" onchange="change_df_value(this.id,this.value,{{ $count_slots+1 }})" value="{{ $schedule_dates[$d] }}" size="16" class="form-control input-append date">

                                                            </div>
                                                            <br>
                                                            <div id="{{ 'date_day__1' . '_' . $d . '_' . $c }}" style="text-align:center;">
                                                                {{--{{ date("l", strtotime("+$day_increament day", strtotime($schedule_details->initial_date))) }}--}}
                                                                {{ date('l',date_create_from_format('Y-m-d',$schedule_dates[$d])->getTimestamp()) }}

                                                            </div>
                                                        </div>
                                                    </td>
                                                @elseif($m_slot_lists[$c]==1 || $m_slot_lists[$c]==2)
                                                    <td  style="border-left-width: 1px;border-bottom-width: 1px;">
                                                        @php $data = array('type'  => 'hidden',
                                                                        'id'    => 'schedule_date'.'__1'.'_'.$d.'_'.$c,
                                                                        'class' => ''
                                                                        );
                                                        @endphp
                                                        {{--{!! Form::hidden('schedule_date[]', date("Y-m-d",strtotime("+$day_increament day",strtotime($schedule_details->initial_date))), $data) !!}--}}
                                                        {!! Form::hidden('schedule_date['.$d.']['.$c.'][]', $schedule_dates[$d], $data) !!}
                                                        @php unset($data) @endphp
                                                        @php
                                                            $data = array('type'  => 'hidden',
                                                                'id'    => '',
                                                                'class' => ''
                                                                );
                                                        @endphp
                                                        {!! Form::hidden('slot_id['.$d.']['.$c.'][]',$m_slot_lists[$c],$data) !!}
                                                        @php unset($data) @endphp
                                                        <div class="">
                                                            @php  $topics_list->prepend('Select Class/Chapter', '') @endphp
                                                            @php
                                                                $data = array('class' => 'form-control select2 ',
                                                                        'required' => 'required',
                                                                        'multiple'=>'multiple'
                                                                    );
                                                            @endphp
                                                            {{--{!! Form::select('examtopic_'.$d.'[]', $topics_list,(isset($key) ? $key : '') ,$data) !!}--}}
                                                            {!! Form::select('topic_id['.$d.']['.$c.'][]', $topics_list,'',$data) !!}

                                                        </div>
                                                        <br>
                                                        <div class="">
                                                            @php
                                                                $data = array('class' => 'form-control m-bot15');
                                                            @endphp
                                                            {!! Form::select('teacher_id['.$d.']['.$c.'][]', $teachers_list,'',$data) !!}
                                                        </div>
                                                    </td>
                                                @endif
                                            @endfor
                                        </tr>
                                        @php $d++; @endphp
                                        @php $countdays = count($wd_ids) @endphp
                                        @if($countdays==1)
                                            @php $dayinc=7; @endphp
                                        @elseif($countdays==2)
                                            @php $first_diff = $wd_ids[1]-$wd_ids[0]; @endphp
                                            @php $second_diff = 7-$first_diff; @endphp
                                            @php $dayinc = ($i%2==1)?$first_diff:$second_diff; @endphp
                                            @php $day_increament+=$dayinc; @endphp
                                            @php $i++; @endphp
                                        @endif
                                    @endforeach
                                @endif
                                <tr><td colspan="{{ count($batch_slots) + 1}}">
                                        <div style="text-align:right;"><span id="add" style="cursor: pointer;" title="add row"><i class="fa fa-plus-circle" style="font-size: 19px;"></i></span></div>
                                    </td>
                                </tr>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="">
                                <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="margin:5px;">
                                <input type="submit" name="submit_print" value="Save & Print" class="btn btn-primary" style="margin:5px;">
                            </div>
                        {!! Form::close() !!}
                        <!-- END FORM-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END PAGE CONTENT-->


@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>



    <script type="text/javascript">

        /*function change_wd_value( value_to_set ){
            var given_date = new Date(value_to_set);
            var given_date_weekday = given_date.getDay();
            $("#id_wd_ids").val(given_date_weekday);
            $('#id_wd_ids').trigger('change');
        }*/

        function change_wd_value( value_to_set ){
            var week_day_numbers_js_to_php = { 0:7, 1:1, 2:2, 3:3, 4:4, 5:5, 6:6, 7:7 };
            var given_date = new Date(value_to_set);
            var given_date_weekday_number_for_js = given_date.getDay();
            var given_date_weekday_number_for_php = week_day_numbers_js_to_php[given_date_weekday_number_for_js];
            $("#id_wd_ids").val(given_date_weekday_number_for_php);
            $('#id_wd_ids').trigger('change');
        }

        function change_df_value(element_id,value,column_count) {
            var week_day_numbers_js_to_php = { 0:7, 1:1, 2:2, 3:3, 4:4, 5:5, 6:6, 7:7 };
            var week_days_for_php = { 1:'Monday', 2:'Tuesday', 3:'Wednesdey', 4:'Thursday', 5:'Friday', 6:'Saturday', 7:'Sunday'};
            var given_date = new Date(value);
            var given_date_js_weekday = given_date.getDay();
            var given_date_php_weekday = week_day_numbers_js_to_php[given_date_js_weekday];
            var given_date_weekday_name = week_days_for_php[given_date_php_weekday];
            var id = element_id.split("__")[1];
            var form_id = id.split("_")[0];
            var row_id = id.split("_")[1];
            var col_id = id.split("_")[2];
            $("#date__"+id).val(value);
            $("#date__"+id).html(value);
            $("#date_day__"+form_id+"_"+row_id+"_"+col_id).html(given_date_weekday_name);
            for(col_id=1;col_id<column_count+1;col_id++){
                $("#schedule_date__"+form_id+"_"+row_id+"_"+col_id).val(value);
                $("#schedule_date__"+form_id+"_"+row_id+"_"+col_id).html(value);
            }

        }

        function remove_item(element_id){
            $("#row_"+element_id).remove();
            var rowspan = $('#id_break').attr('rowspan');
            $("#id_break").attr('rowspan', (rowspan-1));
        }


        $(document).ready(function() {

            $('.timepicker').datetimepicker({
                format: 'LT'
            });

            $('.date').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                startDate: '1900-01-01',
                endDate: '2035-01-01',
            }).on('changeDate', function(e){
                $(this).datepicker('hide');
            });

            $("body").on( "click", "[id='add']", function() {
                //alert($(".table tr:last").prev().find("tr").html());exit;
                //alert($(".table tr:last").prev().attr("id"));exit;
                var row_id = $(".table tr:last").prev().attr("id");
                var row = $(".table tr:last").prev();
                var d = row_id.split("_")[1];
                var schedule_details_id = $("#schedule_details_id").html();
                //console.log(row_date);exit;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/add-schedule-row',
                    dataType: 'HTML',
                    data: { d:++d,schedule_details_id:schedule_details_id },
                    success: function( data ) {
                        $("#id_table  tr:last").prev().after(data);
                        var rowspan = $('#id_break').attr('rowspan');
                        $("#id_break").attr('rowspan', (rowspan+1));
                        // reinit your plugin something like the below code.
                        $('.select2').select2();

                        $('.date').datepicker({
                            format: 'yyyy-mm-dd',
                            todayHighlight: true,
                            startDate: '1900-01-01',
                            endDate: '2035-01-01',
                        }).on('changeDate', function(e){
                            $(this).datepicker('hide');
                        });
                    }
                });

            });

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institutes-courses',
                    dataType: 'HTML',
                    data: { institute_id : institute_id },
                    success: function( data ) {
                        $('.courses').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/courses-batches',
                    dataType: 'HTML',
                    data: { course_id : course_id },
                    success: function( data ) {
                        $('.batches').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='batch_id']", function() {

                var year = $('#year').val().slice(-2);
                var session_id = $('#session_id').val();
                var course_id = $('#course_id').val();
                var faculty_id = $('#faculty_id').val();
                var subject_id = $('#subject_id').val();
                var batch_id = $('#batch_id').val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/reg-no',
                    dataType: 'HTML',
                    data: { year : year, session_id : session_id, course_id : course_id , faculty_id : faculty_id,  subject_id : subject_id, batch_id : batch_id },
                    success: function( data ) {
                        $('.reg_no').html(data);
                    }
                });
            });

            $('.select2').select2();
        })
    </script>


@endsection
