
<div class="form-group">
    <div class="col-md-12">
        <div class="input-icon right">
			Select Lecture <br>
		    
		    <select name="lecture_id" class="form-control" required>
				<option value="" disabled selected>Select Lecture</option>
				<?php
			    	foreach ($lectures as $lecture){
	                $temp_name = \App\OnlineLectureAddress::select('*')->where('id', $lecture->lecture_address_id)->get();    
	                    foreach ($temp_name as $lname){
		                    echo "<option value='{$lname->id}'>".$lname->name."</option>";
		                }
	                }
	            ?>
	        </select>

		</div>
	</div>
</div>





