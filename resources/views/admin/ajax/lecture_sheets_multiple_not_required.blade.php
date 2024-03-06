<div class="form-group">
    <label class="col-md-3 control-label">Lecture Sheet </label>
    <div class="col-md-3">
            @php  $lecture_sheets->prepend('Select Lecture Sheet', ''); @endphp
            {!! Form::select('lecture_sheet_id[]',$lecture_sheets, '',['class'=>'form-control select2','id'=>'lecture_sheet_id','multiple'=>'multiple']) !!}<i></i>
    </div>
</div>