<div class="form-group col-md-2">
    <h5>Courses<span class="text-danger"></span></h5>
    <div class="controls">
        <select name="course_id" id="course_id" class=form-control required>
            <option disabled selected value="">Select Course</option>
            @foreach($course_sessions as $course_session)
            <option  value=" {{$course_session->course->id ?? '' }}">
                {{$course_session->course->name ?? '' }}
            </option>
            @endforeach
        </select>
    </div>
</div>
