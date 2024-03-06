
<?php if (isset($institute)) { ?>
	<div class="form-group">
	    <label class="col-md-2 control-label">Select Year </label>
	    <div class="col-md-3">
	    	{!! Form::select('year',$years, old('year')?old('year'):'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>                               
	    </div>
	</div>
	<div class="form-group">
	    <label class="col-md-2 control-label">Select Session </label>
	    <div class="col-md-3">
	    	@php  $sessions->prepend('Select Session', ''); @endphp
            {!! Form::select('session_id',$sessions, old('session_id')?old('session_id'):'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                                                        
	    </div>
	</div>
	<div class="form-group">
	    <label class="col-md-2 control-label">Select Institute </label>
	    <div class="col-md-3">
	       @php  $institute->prepend('Select Institute', ''); @endphp
           {!! Form::select('institute_id',$institute, old('institute_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
	    </div>
	</div>
<?php } ?>
        
