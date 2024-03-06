
<div class="form-group">
    <label class="col-md-3 control-label couese" >Courses</label>
    <div class="col-md-4"> 
        <div class="input-icon right">                            
        <select name="course_id" id="course_id" class=form-control required>
            <option disabled selected value="">Select Course</option>
            @foreach($doctor_courses as $doctor_course)
            <option  value=" {{$doctor_course->course->id ?? '' }}">
                {{$doctor_course->course->name ?? '' }}
            </option>
            @endforeach
        </select>                                 
        </div>
    </div>
</div>

 