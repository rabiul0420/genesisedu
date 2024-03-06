<div class="source_subject">
    <div class="form-group col-md-3">
        <h5>Source Subject <span class="text-danger"></span></h5>
        <div class="controls">
            @php  $source_subjects->prepend('Select Subject', ''); @endphp
            {!! Form::select('source_subject_id',$source_subjects, '' ,['class'=>'form-control','id'=>'source_subject_id']) !!}<i></i>
        </div>
    </div>
</div>