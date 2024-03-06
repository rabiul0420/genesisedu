<label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
<div class="col-md-3">
    <div class="input-icon right">
        @php  $question_source_courses->prepend('Select Course', ''); @endphp
        {!! Form::select('source_course_id',$question_source_courses, old('source_course_id')?old('source_course_id'):'' ,['class'=>'form-control','id'=>'source_course_id']) !!}<i></i>

    </div>
</div>
