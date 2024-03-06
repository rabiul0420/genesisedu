
<?php if (isset($batches)) { ?>
	<div class="form-group">
	    <label class="col-md-2 control-label">Select Batch </label>
	    <div class="col-md-3">
	       @php  $batches->prepend('Select Batch', ''); @endphp
           {!! Form::select('batch_id',$batches, old('batch_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
	    </div>
	</div>
<?php } ?>
        
