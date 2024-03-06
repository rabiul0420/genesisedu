<div class="form-group col-md-2">
    <label class="col-md-3 control-label">Discipline</label>
    <div class="controls">
        @php  $subjects->prepend('Select Discipline', ''); @endphp
        {!! Form::select('subject_id', $subjects, old('subject_id'),['class'=>'form-control','id'=>'subject_id']) !!}<i></i>
    </div>
</div>
