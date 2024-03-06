<label class="col-md-3 control-label">Faculty (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
<div class="col-md-3">
    <div class="input-icon right">
        @php  $question_source_faculties->prepend('Select Faculty', ''); @endphp
        {!! Form::select('source_faculty_id',$question_source_faculties, old('source_faculty_id')?old('source_faculty_id'):'' ,['class'=>'form-control','id'=>'source_faculty_id']) !!}<i></i>

    </div>
</div>
