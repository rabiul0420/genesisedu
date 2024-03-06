<div class="form-group">
    <label class="col-md-3 control-label">Faculty </label>
    <div class="col-md-3">
        @php  $faculties->prepend('Select Faculty', ''); @endphp
        {!! Form::select('faculty_id',$faculties, old('faculty_id')?old('faculty_id'):'',['class'=>'form-control','id'=>'faculty_id']) !!}<i></i>
    </div>
</div>