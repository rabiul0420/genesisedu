<div class="form-group">
    <label class="col-md-3 control-label">Courier Address (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            <textarea class="form-control" rows="3" name="courier_address" required >{{ old('courier_address')?old('courier_address'):'' }}</textarea>
        </div>
    </div>
</div>