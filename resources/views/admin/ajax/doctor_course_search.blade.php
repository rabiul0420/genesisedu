<div class="course">
    <div class="form-group col-md-2">
        <h5>Course <span class="text-danger"></span></h5>
        <div class="controls">
            @php  $courses->prepend('Select Course', ''); @endphp
            {!! Form::select('course_id',$courses, '' ,['class'=>'form-control batch2 course_id','required'=>'required','id'=>'course_id']) !!}<i></i>
        </div>
    </div>
</div>