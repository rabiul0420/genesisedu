<label class="col-md-3 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
<div class="col-md-3">
    <div class="input-icon right">
        @php  $question_source_sessions->prepend('Select Session', ''); @endphp
        {!! Form::select('source_session_id',$question_source_sessions, old('source_session_id')?old('source_session_id'):'' ,['class'=>'form-control','id'=>'source_session_id']) !!}<i></i>

    </div>
</div>
