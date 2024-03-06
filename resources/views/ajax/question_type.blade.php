<div class="form-group">
    <label class="col-md-1 control-label">Question Type</label>
    <div class="col-md-3">
        @php  $question_type->prepend('Select Question Type', ''); @endphp
        {!! Form::select('question_type_id',$question_type, old('question_type_id')?old('question_type_id'):'' ,['class'=>'form-control','required']) !!}<i></i>
    </div>
</div>


