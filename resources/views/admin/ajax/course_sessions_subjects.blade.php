<div class="sessions">
    <div class="form-group mt-3">
        <label class="col-md-3 control-label">Sessions (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-3">
            @php  $sessions->prepend('--Select Session--', ''); @endphp
            {!! Form::select('session_id',$sessions,'',['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
        </div>
    </div>
</div>
<div class="subjects">
    <div class="form-group mt-3">
        <label class="col-md-3 control-label">Discipline  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
        <div class="col-md-3">
            @php  $subjects->prepend('--Select Discipline--', ''); @endphp
            {!! Form::select('subject_id',$subjects,'',['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>
        </div>
    </div>
</div>


