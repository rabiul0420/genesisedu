<tr id="{{ 'row_'.$d }}">
    @for($c = 0 ;$c<=($count_slots);$c++)
        @if($c=='0')
            <td  style="border-left-width: 1px;border-bottom-width: 1px;">
                <div style="text-align:left;"><span id="{{ $d }}" onclick="remove_item(this.id)" style="cursor: pointer;" title="remove"><i class="fa fa-minus-circle"></i></span></div>
                <div class="">

                    <div style="text-align:right;" data-date-viewmode="years" data-date-format="yyyy-mm-dd" data-date="{{ '' }}" class="">
                        {{--<input type="text"  name="schedule_dateggg" id="{{'schedule_date_main'.'__1'.'_'.$i.'_'.$c }}" onchange="change_df_value(this.id,this.value,{{ $count_slots+1 }})" value="{{date("Y-m-d", strtotime("+$day_increament day", strtotime($schedule_details->initial_date)))}}" size="16" class="form-control input-append date">
                        --}}
                        <input type="text"  name="schedule_dateggg" id="{{'schedule_date_main'.'__1'.'_'.$d.'_'.$c }}" onchange="change_df_value(this.id,this.value,{{ $count_slots+1 }})" value="{{ '' }}" size="16" class="form-control input-append date">

                    </div>
                    <br>
                    <div id="{{ 'date_day__1' . '_' . $d . '_' . $c }}" style="text-align:center;">
                        {{--{{ date("l", strtotime("+$day_increament day", strtotime($schedule_details->initial_date))) }}--}}
                        {{ ''/*date('l',date_create_from_format('Y-m-d',$schedule_dates[$d])->getTimestamp())*/ }}

                    </div>
                </div>
            </td>
        @elseif($m_slot_lists[$c] != 3)
            <td  style="border-left-width: 1px;border-bottom-width: 1px;">
                @php $data = array('type'  => 'hidden',
                                                                        'id'    => 'schedule_date'.'__1'.'_'.$d.'_'.$c,
                                                                        'class' => ''
                                                                        );
                @endphp
                {{--{!! Form::hidden('schedule_date[]', date("Y-m-d",strtotime("+$day_increament day",strtotime($schedule_details->initial_date))), $data) !!}--}}
                {!! Form::hidden('schedule_date['.$d.']['.$c.'][]', '', $data) !!}
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
                    {!! Form::select('topic_id['.$d.']['.$c.'][]', $topics_list,(isset($key) ? $key : ''),$data) !!}

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
