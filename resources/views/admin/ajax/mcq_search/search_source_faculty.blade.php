<div class="source_faculty">
    <div class="form-group col-md-3">
        <h5>Source Faculty <span class="text-danger"></span></h5>
        <div class="controls">
            @php  $source_faculties->prepend('Select Faculty', ''); @endphp
            {!! Form::select('source_faculty_id',$source_faculties, '' ,['class'=>'form-control','id'=>'source_faculty_id']) !!}<i></i>
        </div>
    </div>
</div>