
<?php if (isset($courses)) { ?>
	<div class="form-group">
	    <label class="col-md-2 control-label">Select Course </label>
	    <div class="col-md-3">
	       @php  $courses->prepend('Select Course', ''); @endphp
           {!! Form::select('course_id',$courses, old('course_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
	    </div>
	</div>
<?php } ?>
        
