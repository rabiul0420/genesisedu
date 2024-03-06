<div class="form-group">
    <label class="col-md-3 control-label">Delivery (<i class="fa fa-asterisk ipd-star"style="font-size:11px;"></i>) </label>

    <div class="col-md-3" id="id_div_lecture_sheet_collection">
        <label class="radio-inline">
            <input type="radio" class="home" name="delivery_status" required value="1"
                    {{  old('delivery_status') === "1" ? "checked" : '' }}> Courier Address
        </label>
        <label class="radio-inline">
            <input type="radio" class="home" name="delivery_status" required value="0"
                    {{  old('delivery_status') === "0" ? "checked" : '' }} > GENESIS Office Collection
        </label>
    </div>
</div>