<div class="form-group">
    <label class="col-md-1 control-label">Discipline </label>
    <div class="col-md-3">
        @php  $subjects->prepend('Select Discipline', ''); @endphp
        {!! Form::select('subject_id',$subjects, old('subject_id')?old('subject_id'):'',['class'=>'form-control','id'=>'subject_id']) !!}<i></i>
    </div>
</div>

