<div class="form-group">
    <label class="col-md-3 control-label">Courier Division (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        @php  $divisions->prepend('Select Division', ''); @endphp
        {!! Form::select('courier_division_id',$divisions, old('courier_division_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
    </div>
</div>