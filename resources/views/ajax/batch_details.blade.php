<div class="form-group">
    <div class="col-md-3">
        <span class="btn btn-xs btn-primary" data-toggle='modal' data-target='#modal_batch_details' style="line-height:32px;">Batch Details</span>
        <div class='modal fade' id='modal_batch_details' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
            <div class='modal-dialog modal-dialog-centered' role='document'>
                <div class='modal-content'>

                    <div class='modal-header'>
                        {{ $batch->name }} Details
                    </div>
                    <div class='modal-body'> 
                            {!! $batch->details !!}                                                                   

                    </div>

                    <div class='modal-footer'>
                        <button type='button' class='btn btn-sm bg-red' data-dismiss='modal'>Close</button>
                    </div>
                </div>
            </div>
        </div>   
        {{-- <button type="button" class="btn btn-primary">Primary</button>    --}}
    </div>        
</div>
