<div id="question">
    @if(isset($exam_question['question_title']))

        <input type="hidden" name="doctor_course_id" value="{{ $doctor_course_id }}">
        <input type="hidden" name="exam_question_id" value="{{ $exam_question['exam_question_id'] }}">
        <input type="hidden" name="exam_id" value="{{ $exam_id }}">
        <input type="hidden" name="exam_question_type" value="{{ $exam_question['question_type'] }}">

        <div>
            {{--                                                <h4 class='modal-title' id='myModalLabel'>{!! '('.($serial_no).' of '.$exam->exam_questions->count().' ) '.$exam_question['question_title'] !!}</h4>--}}
            <h4 class='modal-title' id='myModalLabel'>{!! '('.($serial_no).' of ' . $total_questions . ' ) '.$exam_question['question_title'] !!}</h4>
        </div>

        <table class="table table-borderless" style="table-layout: auto;">
            @if($exam_question['question_type'] == "1" || $exam_question['question_type'] == "3")
                @foreach($exam_question['question_option'] as $k=>$answer)
                    @if($k<session('stamp'))
                        <tr>
                            <td>
                                {!! isset( $answer[ 'option_title' ]  )? $answer[ 'option_title' ]  :'' !!}
                            </td>

                            <td style="width: 99px;">
                                <label class='radio-inline'><input type='radio' name="{{ $options[$k] ?? $k  }}" value='T'  > T </label>
                                <label class='radio-inline'><input type='radio' name="{{ $options[$k] ?? $k  }}" value='F'  > F </label>
                            </td>

                        </tr>
                    @endif
                @endforeach
            @elseif($exam_question['question_type'] == "2" || $exam_question['question_type'] == "4")
                @foreach($exam_question['question_option'] as $k=>$answer)
                    @if($k<session('stamp'))
                        <tr>
                            <td>
                                {!! isset( $answer[ 'option_title' ] )? $answer[ 'option_title' ] :'' !!}
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td>
                        <label class='radio-inline'><input type='radio' name='ans_sba' value='A' > A </label>
                        <label class='radio-inline'><input type='radio' name='ans_sba' value='B' > B </label>
                        <label class='radio-inline'><input type='radio' name='ans_sba' value='C' > C </label>
                        <label class='radio-inline'><input type='radio' name='ans_sba' value='D' > D </label>
                        @if(session('stamp')==5)
                            <label class='radio-inline'><input type='radio' name='ans_sba' value='E' > E </label>
                        @endif
                    </td>
                </tr>
            @endif
        </table>

        <div style="float:right;">
            <button id="id_button_skip" class='btn btn-warning' onclick='skip_question()' {{ ($exam_finish=='Finished') ? 'disabled' : '' }}>Skip</button>
            <button id="id_button_submit" class='btn btn-success' onclick='submit_answer()' {{ ($exam_finish=='Finished') ? 'disabled' : '' }}>{{ $exam_finish }}</button>
        </div>
    @endif
</div>
