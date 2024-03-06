    <div class="form-group col-md-2">
        <h5>Batch <span class="text-danger"></span></h5>
        <div class="controls">
            @php  $batches->prepend('Select Batch', ''); @endphp
            {!! Form::select('batch_id',$batches, '' ,['class'=>'form-control batch2','required'=>'required','id'=>'batch_id']) !!}<i></i>
        </div>
    </div>
