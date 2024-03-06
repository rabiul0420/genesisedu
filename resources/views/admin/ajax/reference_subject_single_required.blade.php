<div class="form-group">
    <label class="col-md-3 control-label">Subject (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            @php  $reference_subjects->prepend('Select Subject', ''); @endphp
            {!! Form::select('subject_id',$reference_subjects, old('subject_id')?old('subject_id'):'' ,['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>

        </div>
    </div>
</div>