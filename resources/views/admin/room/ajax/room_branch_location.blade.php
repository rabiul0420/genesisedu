<div class="form-group">
    <label class="col-md-3 control-label">Location (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            @php $locations->prepend('Select Location', ''); @endphp
            {!! Form::select('location_id',$locations, '', ['class'=>'form-control','required'=>'required','id'=>'location_id']) !!}<i></i>
        </div>
    </div>
</div>



