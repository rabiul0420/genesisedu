<div class="form-group col-md-2">
    <label class="col-md-3 control-label">Classes </label>
    <div class="controls">
        @php  $classes->prepend('Select Classes', ''); @endphp
        {!! Form::select('class_id', $classes, '' ,['class'=>'form-control','required'=>'required','id'=>'class_id']) !!}<i></i>
    </div>
</div>  