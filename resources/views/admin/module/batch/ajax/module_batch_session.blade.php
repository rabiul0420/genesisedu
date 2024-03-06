<div class="form-group col-md-2">
    <h5>Session <span class="text-danger"></span></h5>
    <div class="controls">
        @php  $sessions->prepend('Select Session', ''); @endphp
        {!! Form::select('session_id',$sessions, old('session_id'),['class'=>'form-control select2','required'=>'required']) !!}<i></i>
    </div>
</div>



