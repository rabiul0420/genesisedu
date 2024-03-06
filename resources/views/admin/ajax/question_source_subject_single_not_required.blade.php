<label class="col-md-3 control-label">Subject (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
<div class="col-md-3">
    <div class="input-icon right">
        @php  $question_source_subjects->prepend('Select Subject', ''); @endphp
        {!! Form::select('source_subject_id',$question_source_subjects, old('source_subject_id')?old('source_subject_id'):'' ,['class'=>'form-control','id'=>'source_subject_id']) !!}<i></i>

    </div>
</div>
