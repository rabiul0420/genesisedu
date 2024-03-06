<div class="form-group">
    <label class="col-md-3 control-label">Class/Chapter  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-3">
        @php  $topics->prepend('Select Class/Chapter', ''); @endphp
        {!! Form::select('topic_id',$topics, '',['class'=>'form-control','id'=>'topic_id']) !!}<i></i>
    </div>
</div>
