
<div class="form-group">
    <div class="col-md-12">
        <div class="input-icon right">
        	Video<br>
        	<select name="lecture_video_id" class="form-control" id="lecture_video_id" required="required" onchange="goToPage(this.value)">
        		<option value="">Select Video</option>
		        <?php 
					if (isset($video->id)){
						$temp_name = \App\Lecture_video_batch_lecture_video::select('*')->where('lecture_video_batch_id', $video->id)->get();
						foreach ($temp_name as $lecture_video_ids){
						echo "<option value='{$lecture_video_ids->lecture_video_id}'>{$lecture_video_ids->lecture_video->name}</option>";
						}
					}
				?>
			</select>
    	</div>
    </div>
</div>
<?php 
	if (isset($video->id)){
		$temp_name = \App\DoctorsCourses::select('*')->where('batch_id', $video->batch_id)->where('doctor_id', Auth::id())->get();
		foreach ($temp_name as $cid){
			$doctor_course_id = $cid->reg_no;
		}
	}
	if($doctor_course_id){
		echo "<input type='hidden' name='doctor_course_id' value='{$doctor_course_id}'>";
	} else {
		echo "<font color=red>You are Not Admitted in This Batch.</font><br><br><input class='hidden' type='text' name='doctor_course_id' value='' required>";
	}
?>
