
    <label class="col-md-1 control-label">Discipline</label>
    <div class="col-md-2">
        @php  $subject->prepend('Select Discipline', ''); @endphp
        {!! Form::select('subject_id', $subject, old('subject_id'),['class'=>'form-control']) !!}<i></i>
    </div>
</div>
