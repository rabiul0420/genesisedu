<div class="subjects">
    <div class="form-group col-md-2">
        <h5>Discipline <span class="text-danger"></span></h5>
        <div class="controls">
            @php  $subjects->prepend('Select Discipline', ''); @endphp
            {!! Form::select('subject_id',$subjects, '' ,['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>
        </div>
    </div>
</div> 

<div class="form-group col-md-2">
    <label class="col-md-3 control-label">Faculty </label>
    <div class="controls">
        @php  $faculties->prepend('Select Faculty', ''); @endphp
        {!! Form::select('faculty_id',$faculties, old('faculty_id')?old('faculty_id'):'',['class'=>'form-control','id'=>'faculty_id']) !!}<i></i>
    </div>
</div>
