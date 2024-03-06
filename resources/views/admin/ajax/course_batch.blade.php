
    <div class="batch">
        <div class="form-group col-md-2">
            <label class="control-label">Batch</label>
            <div class="controls">
                <select name="batch_id" id="batch_id" class=form-control required data-placeholder="--Select Batch--" data-allow-clear="true">
                    <option value="">--Select Batch--</option>
                    @foreach($batches as $id=>$name)
                        <option  value="{{ $id }}">{{$name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div> 
