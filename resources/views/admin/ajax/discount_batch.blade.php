<div class="form-group">
    <label class="col-md-3 control-label batch" >Batchs (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
    <div class="col-md-4"> 
        <div class="input-icon right">                            
        <select name="batch_id" id="batch_id" class="form-control" required batch2>
            <option disabled selected value="">Select Batch</option>
            @foreach($batches as $batch)
            <option  value=" {{$batch->id ?? '' }}">
                {{$batch->name ?? '' }}
            </option>
            @endforeach
        </select>                                
        </div>
    </div>
</div>
