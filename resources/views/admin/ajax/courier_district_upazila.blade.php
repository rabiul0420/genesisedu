<div class="form-group">
    <label class="col-md-3 control-label">Courier Upazila (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $upazilas->prepend('Select Upazila', ''); @endphp
        {!! Form::select('courier_upazila_id',$upazilas, old('courier_upazila_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
    </div>
</div>