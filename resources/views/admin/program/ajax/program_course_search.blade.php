<div class="form-group col-md-2">
    <h5>Course <span class="text-danger"></span></h5>
    <div class="controls">
        @php  $courses->prepend('Select Course', ''); @endphp
        {!! Form::select('course_id',$courses, old('course_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
    </div>
</div>



