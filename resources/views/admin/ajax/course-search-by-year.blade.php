<div class="form-group">
    <label class="col-md-3 control-label">Courses</label>
    <div class="col-md-4">
        <select name="course_id" id="course_id" class=form-control required>
            <option value="">--Select Course--</option>
            @foreach($institutes as $institute)
            <optgroup label="{{ $institute->name ?? '' }}">
                @foreach($institute->courses as $course)
                <option  value="{{ $course->id }}">
                    {{ $course->name }}
                </option>
                @endforeach
            </optgroup>
            @endforeach
        </select>
    </div>
</div>
