<div class="form-group">
    <label class="col-md-3 control-label">Topic (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-4">
        @php  $topics->prepend('Select Topic', ''); @endphp
        {!! Form::select('topic_id',$topics, '',['class'=>'form-control','required'=>'required','id'=>'topic_id']) !!}<i></i>
    </div>      
</div>


