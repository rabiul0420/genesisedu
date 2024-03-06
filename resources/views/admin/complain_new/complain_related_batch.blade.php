@if ($batches)
    
<div class="form-group">
    <label class="col-md-3 control-label">Your Complain Related To Which Batch(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
    <div class="col-md-4">
        <div class="input-icon right">
            <select class="form-control batch_id_select" name="batch_id" {{ ($complain_related_id == '1' ||   $complain_related_id == '2') ? 'required' : '' }} >
                <option value="">---Select Batch---</option>
                @foreach ($batches as $batch)
                    <option value="{{ $batch->id  }}">{{ $batch->name  }}</option>
                @endforeach
                
            </select>
        </div>
    </div>
</div> 
@endif

<div class="form-group">
    <label class="col-md-3 control-label"> Type Complain : (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
    <div class="col-md-4">
        <div class="input-icon right">
            <textarea style="margin-top: 10px;" name="description" value="" class="form-control" ></textarea>
        </div>
    </div>
</div>

{{-- @if ($batches)
<div class="form-group">
    <label class="col-md-4 control-label">Your Complain Related To Which Batch</label>
    <div class="col-md-4">
        <div class="input-icon right">
            <select class="form-control batch_id_select" name="batch_id" {{ ($complain_related_id == '1' ||   $complain_related_id == '2') ? 'required' : '' }} >
                <option value="">---Select Batch---</option>
                @foreach ($batches as $batch)
                    <option value="{{ $batch->id ?? ''  }}">{{ $batch->name ?? '' }}</option>
                @endforeach
                
            </select>
        </div>
    </div>
</div>
@endif

<div class="form-body">
    <div class="form-group">
        <div class="col-md-12">
            <div class="input-icon right">
                Type Complain : <br>
                <textarea style="margin-top: 10px;" name="description" value="" class="form-control" required></textarea>
            </div>
        </div>
    </div>
</div> --}}



