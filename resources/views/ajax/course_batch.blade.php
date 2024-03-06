<div class="form-group">
    <div class="col-md-12">
        <div class="input-icon right">
        	Batch<br>
	        @php  $batch->prepend('Select Batch', ''); @endphp
	        {!! Form::select('batch_id',$batch, old('batch_id')?old('batch_id'):'',['class'=>'form-control','required'=>'required','id'=>'batch_id']) !!}<i></i>
    	</div>
    </div>
</div>