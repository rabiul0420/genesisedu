<div class="form-group">
    <label class="col-md-3 control-label couese" >Session</label>
    <div class="col-md-4"> 
        <div class="input-icon right">                            
        <select name="session_id" id="session_id" class=form-control required>
            <option disabled selected value="">Select Session</option>
            @foreach($sessions as $session)
            <option  value=" {{$session->session->id ?? '' }}">
                {{$session->session->name ?? '' }}
            </option>
            @endforeach
        </select>                                 
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label batch" >Batchs</label>
    <div class="col-md-4"> 
        <div class="input-icon right">                            
        <select name="batch_id[]" id="batch_id" class="form-control" required batch2>
            <option disabled selected value="">Select Batch</option>
            @foreach($batchs as $batch)
            <option  value=" {{$batch->id ?? '' }}">
                {{$batch->name ?? '' }}
            </option>
            @endforeach
        </select>                                
        </div>
    </div>
</div>