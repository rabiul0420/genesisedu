<div class="form-group">
    <label class="col-md-3 control-label">Sms  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-3">
            @php  $smss->prepend('Select Sms', ''); @endphp
            {!! Form::select('sms_id[]',$smss, '',['class'=>'form-control select2','id'=>'sms_id','multiple'=>'multiple']) !!}<i></i>
    </div>
</div>