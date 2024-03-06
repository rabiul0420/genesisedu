<div class="form-group">
    <label class="col-md-3 control-label">Lecture Sheet Topics</label>
    <div class="col-md-3">
            @php  $lecture_sheet_topics->prepend('Select Lecture Sheet Topic', ''); @endphp
            {!! Form::select('lecture_sheet_topic_id[]',$lecture_sheet_topics, '',['class'=>'form-control select2','id'=>'lecture_sheet_topic_id','multiple'=>'multiple']) !!}<i></i>
    </div>
</div>