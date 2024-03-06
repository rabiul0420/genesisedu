<div class="form-group">
    <label class="col-md-1 control-label">Reg No.</label> 
    <div class="col-md-3">
        <div class="input-group">
            <span class="input-group-addon">{{ $reg_no_first_part }}</span>
            <input type="hidden" name="reg_no_first_part" required value="{{ $reg_no_first_part }}">
            <input type="text" name="reg_no_last_part" value="" required class="form-control" placeholder="_ _ _" >
            {{--<input type="text" name="reg_no_last_part" value="" class="form-control" placeholder="_ _ _" minlength="3" maxlength="3">--}}
        </div>
    </div>
</div>




