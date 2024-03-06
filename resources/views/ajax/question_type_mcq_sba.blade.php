@if($question_type->mcq_number )
<div class="form-group">
    <label class="col-md-1 control-label">Add MCQs</label>
    <div class="col-md-11">
        {!! Form::select('mcq_question_id[]',array(), old('institute_id'),['class'=>'form-control mcqs2','multiple','required'=>'required']) !!}<i></i>
        <span style="color:red" class="mcq_count">Add {{ $question_type->mcq_number }} Questions</span>
        <input type="hidden" name="mcq_count" value="{{ $question_type->mcq_number }}">
        <input type="text" class="hidden" name="mcq_full">
    </div>
</div>
@endif

@if($question_type->sba_number)
<div class="form-group">
    <label class="col-md-1 control-label">Add SBAs</label>
    <div class="col-md-11">
        {!! Form::select('sba_question_id[]',array(), old('institute_id'),['class'=>'form-control sbas2','multiple','required'=>'required']) !!}<i></i>
        <span style="color:red" class="sba_count">Add {{ $question_type->sba_number }} Questions</span>
        <input type="hidden" name="sba_count" value="{{ $question_type->sba_number }}">
        <input type="text" class="hidden" name="sba_full">
    </div>
</div>
@endif
