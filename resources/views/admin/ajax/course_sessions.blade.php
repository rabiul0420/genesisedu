
<div class="form-group col-md-2">
    <label class="col-md-3 control-label">Sessions</label>
    <div class="controls">
        @php  $sessions->prepend('Select Session', ''); @endphp
        {!! Form::select('session_id',$sessions, '' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
    </div>
</div>


