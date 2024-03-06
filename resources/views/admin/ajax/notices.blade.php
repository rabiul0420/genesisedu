<div class="form-group">
    <label class="col-md-3 control-label">Notice  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-3">
            @php  $notices->prepend('Select Notice', ''); @endphp
            {!! Form::select('notice_id[]',$notices, '',['class'=>'form-control select2','id'=>'notice_id','multiple'=>'multiple']) !!}<i></i>
    </div>
</div>