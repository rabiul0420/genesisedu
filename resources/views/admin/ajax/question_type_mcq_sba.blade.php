@if($question_type->mcq_number )
    <div class="form-group">
        <label class="col-md-1 control-label">Add MCQs {{ $question_type->batch_type == 'combined' ? 'of Residency':''  }}</label>
        <div class="col-md-11">
            {!! Form::select('mcq_question_id[]',$mcqs ?? [], old('mcq_question_id', ($mcqs_ids ?? []) ),['class'=>'form-control mcqs2','multiple','data-name'=>'mcq']) !!}<i></i>
            <span style="color:red" class="mcq_count">Add {{ $question_type->mcq_number }} Questions</span>
            <input type="hidden" name="mcq_count" value="{{ $question_type->mcq_number }}">
            <input type="text" class="hidden" name="mcq_full">
        </div>
    </div>
@endif


@if($question_type->mcq2_number )
    <div class="form-group">
        <label class="col-md-1 control-label">Add MCQs {{ $question_type->batch_type == 'combined' ? 'of BCPS':''  }}</label>
        <div class="col-md-11">
            {!! Form::select('mcq2_question_id[]',$mcq2s ?? [], old('mcq2_question_id', ($mcq2s_ids ?? []) ),['class'=>'form-control another-mcqs2','multiple','data-name'=>'mcq2']) !!}<i></i>
            <span style="color:red" class="mcq2_count">Add {{ $question_type->mcq2_number }} Questions</span>
            <input type="hidden" name="mcq2_count" value="{{ $question_type->mcq2_number }}">
            <input type="text" class="hidden" name="mcq2_full">
        </div>
    </div>
@endif


@if($question_type->sba_number)
    <div class="form-group">
        <label class="col-md-1 control-label">Add SBAs {{ $question_type->batch_type == 'combined' ? 'of Residency':''  }}</label>
        <div class="col-md-11">
            {!! Form::select('sba_question_id[]', $sbas ?? [], old('sba_question_id', ($sbas_ids ?? []) ),['class'=>'form-control sbas2','multiple','data-name'=>'sba']) !!}<i></i>
            <span style="color:red" class="sba_count">Add {{ $question_type->sba_number }} Questions</span>
            <input type="hidden" name="sba_count" value="{{ $question_type->sba_number }}">
            <input type="text" class="hidden" name="sba_full">
        </div>
    </div>
@endif

