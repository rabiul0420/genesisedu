<div class="form-group col-md-2">
    <h5>Session <span class="text-danger"></span></h5>
    <div class="controls">
        @php  $sessions->prepend('Select Session', ''); @endphp
        {!! Form::select('session_id',$sessions, '' ,['class'=>'form-control session_id','id'=>'session_id']) !!}<i></i>
    </div>
</div>
 