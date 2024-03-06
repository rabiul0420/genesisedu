<div class="form-group">
    <label class="col-md-1 control-label">Discipline (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $subjects->prepend('Select Disciplines', ''); @endphp
        {!! Form::select('subject_id[]',$subjects, old('subject_id')?old('subject_id'):'',['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>
    </div>
</div>
