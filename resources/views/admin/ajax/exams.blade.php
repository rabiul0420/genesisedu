<div class="form-group">dds
    <label class="col-md-3 control-label">Exam  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-3">
            @php  $online_exams->prepend('Select Online Exam', ''); @endphp
            {!! Form::select('exam_id[]',$online_exams, '',['class'=>'form-control select2','id'=>'online_exam_id','multiple'=>'multiple']) !!}<i></i>
    </div>
</div>