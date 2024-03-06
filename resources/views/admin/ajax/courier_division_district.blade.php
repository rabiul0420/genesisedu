<div class="form-group">
    <label class="col-md-3 control-label">Courier District (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $districts->prepend('Select District', ''); @endphp
        {!! Form::select('courier_district_id',$districts, old('courier_district_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
    </div>
</div>