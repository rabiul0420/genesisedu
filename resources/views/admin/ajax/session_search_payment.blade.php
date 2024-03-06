
<div class="controls">
    @php  $sessions->prepend('Select Session', ''); @endphp
    {!! Form::select('session_id',$sessions, '' ,['class'=>'form-control batch2','required'=>'required','id'=>'session_id']) !!}<i></i>
</div>