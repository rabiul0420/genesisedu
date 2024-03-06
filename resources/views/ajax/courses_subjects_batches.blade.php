<div class="form-group">
    <label class="col-md-1 control-label">Discipline </label>
    <div class="col-md-3">
        @php  $subjects->prepend('Select Discipline', ''); @endphp
        {!! Form::select('subject_id',$subjects, old('subject_id')?old('subject_id'):'',['class'=>'form-control','id'=>'subject_id']) !!}<i></i>
    </div>
</div>

<div class="form-group">
    <label class="col-md-1 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $batches->prepend('Select Batch', ''); @endphp
        {!! Form::select('batch_id',$batches, old('batch_id')?old('batch_id'):'',['class'=>'form-control','required'=>'required','id'=>'batch_id']) !!}<i></i>
    </div>
</div>
