<div class="form-group">
    <label class="col-md-3 control-label">Course</label>
    <div class="col-md-3">
        @php  $courses->prepend('Select Course', ''); @endphp
        {!! Form::select('course_id',$courses, old('course_id'),['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
        <input type="hidden" name="url" value="{{$url}}">
    </div>
</div>
