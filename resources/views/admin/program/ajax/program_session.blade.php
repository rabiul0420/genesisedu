<div class="form-group">
    <label class="col-md-3 control-label">Session  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $sessions->prepend('Select Session', ''); @endphp
        {!! Form::select('session_id',$sessions, old('session_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
    </div>
</div>



