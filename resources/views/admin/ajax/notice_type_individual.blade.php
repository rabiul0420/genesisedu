
<?php if (isset($doctors)) { ?>
	<div class="form-group">
	    <label class="col-md-2 control-label">Search Doctor </label>
	    <div class="col-md-10">
	        {!! Form::select('doctor_id[]',array(), old('doctor_id'),['class'=>'form-control doctor_list','multiple','required'=>'required']) !!}<i></i>
	    </div>
	</div>
<?php } ?>


        
