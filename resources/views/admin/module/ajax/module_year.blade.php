<div class="form-group">
    <label class="col-md-3 control-label">Year  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $years->prepend('Select Year', ''); @endphp
        {!! Form::select('year',$years, old('year'),['class'=>'form-control','required'=>'required']) !!}<i></i>
    </div>
</div>



