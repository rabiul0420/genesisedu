<div class="form-group">
    <label class="col-md-3 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            @php  $reference_sessions->prepend('Select Session', ''); @endphp
            {!! Form::select('session_id',$reference_sessions, old('session_id')?old('session_id'):'' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>

        </div>
    </div>
</div>