<div class="form-group">
    <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            @php  $reference_courses->prepend('Select Course', ''); @endphp
            {!! Form::select('course_id',$reference_courses, old('course_id')?old('course_id'):'' ,['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>

        </div>
    </div>
</div>