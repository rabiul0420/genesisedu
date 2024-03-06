<div class="form-group">
    <label class="col-md-3 control-label">Faculty (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $faculties->prepend('Select Faculty', ''); @endphp
        {!! Form::select('faculty_id',$faculties, old('faculty_id')?old('faculty_id'):'',['class'=>'form-control','required'=>'required','id'=>'faculty_id']) !!}<i></i>
    </div>
</div>