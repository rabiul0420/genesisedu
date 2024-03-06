
    <div class="form-group col-md-2">
        <label for="">Batch</label>
        <div class="controls">
            @php  $batches->prepend('--Select Batch--', ''); @endphp
            {!! Form::select('batch_id',$batches, '' ,['class'=>'form-control batch','required'=>'required','id'=>'batch_id']) !!}<i></i>
        </div>
    </div>