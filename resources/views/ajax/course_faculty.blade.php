<div class="form-group mt-3">
<label class="col-md-3 control-label">Faculty </label>
<div class="col-md-3">
    @php  $faculty->prepend('Select Faculty', ''); @endphp
    {!! Form::select('faculty_id',$faculty, old('faculty_id'),['class'=>'form-control']) !!}<i></i>
</div>
</div>





