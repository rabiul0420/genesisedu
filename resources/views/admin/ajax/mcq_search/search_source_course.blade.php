<div class="source_course">
    <div class="form-group col-md-3">
        <h5>Source Course <span class="text-danger"></span></h5>
        <div class="controls">
            @php  $source_courses->prepend('Select Course', ''); @endphp
            {!! Form::select('source_course_id',$source_courses, '' ,['class'=>'form-control','id'=>'source_course_id']) !!}<i></i>
        </div>
    </div>
</div>