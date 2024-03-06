<div class="subject">
    <div class="form-group col-md-2">
        <h5>Discipline <span class="text-danger"></span></h5>
        <div class="controls">
            @php  $subjects->prepend('Select Discipline', ''); @endphp
            {!! Form::select('subject_id',$subjects, '' ,['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>
        </div>
    </div>
</div>
