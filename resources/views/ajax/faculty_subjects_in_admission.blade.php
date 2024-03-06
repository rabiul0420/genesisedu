<div class="form-group mt-3">
    <label class="col-md-3 control-label">{{ ($is_combined ?? request()->is_combined) == 'yes' ? 'Residency ':'' }}Discipline  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-3">
        @php  $subjects->prepend('--Select Discipline--', ''); @endphp
        {!! Form::select('subject_id',$subjects,'',['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>
    </div>
</div>