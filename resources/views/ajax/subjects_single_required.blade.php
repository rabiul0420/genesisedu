<div class="form-group">
    <label class="col-md-3 control-label">Discipline (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)  </label>
    <div class="col-md-3">
        @php  $subjects->prepend('Select Discipline', ''); @endphp
        {!! Form::select('subject_id',$subjects, old('subject_id')?old('subject_id'):'',['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>
    </div>
</div>
