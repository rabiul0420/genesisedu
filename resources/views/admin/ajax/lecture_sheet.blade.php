@if($batch->is_show_lecture_sheet_fee == "Yes")
<div class="form-group ">
    <label class="col-md-3 control-label">Lecture Sheet (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
    <div class="col-md-3" id="id_div_include_lecture_sheet">
        <label class="radio-inline">
            <input type="radio" name="include_lecture_sheet" required value="1" {{  old('include_lecture_sheet') === "1" ? "checked" : '' }}  > Yes
        </label>
        <label class="radio-inline">
            <input type="radio" name="include_lecture_sheet" required  value="0" {{  old('include_lecture_sheet') === "0" ? "checked" : '' }} > No
        </label>
    </div>
</div>
@endif
