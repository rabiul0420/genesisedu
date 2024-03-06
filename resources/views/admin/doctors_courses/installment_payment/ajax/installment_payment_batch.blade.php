<div class="form-group col-md-2">
    <h5>Session <span class="text-danger"></span></h5>
    <div class="controls">
        @php  $batches->prepend('Select Batch', ''); @endphp
        {!! Form::select('batch_id',$batches, old('batch_id'),['class'=>'form-control select2','required'=>'required']) !!}<i></i>
    </div>
</div>



