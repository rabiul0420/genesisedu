<div class="form-group">
    <label class="col-md-3 control-label">Package (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $packages->prepend('Select Package', ''); @endphp
        {!! Form::select('package_id',$packages, old('package_id'),['class'=>'form-control','required'=>'required','id'=>'package_id']) !!}<i></i>
    </div>
</div>
