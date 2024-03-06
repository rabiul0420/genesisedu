<div class="topics">
    <div class="form-group col-md-3">
        <h5>Topic <span class="text-danger"></span></h5>
        <div class="controls">
            @php  $topics->prepend('Select Topic', ''); @endphp
            {!! Form::select('topic_id',$topics, '' ,['class'=>'form-control select2','required'=>'required','id'=>'topic_id']) !!}<i></i>
        </div>
    </div>
</div>