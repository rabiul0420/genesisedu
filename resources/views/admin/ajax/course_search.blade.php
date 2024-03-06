<div class="form-group col-md-2">
    <label class="col-md-3 control-label">Courses</label>
    <div class="controls">
        <select name="course_id" id="course_id" class=form-control required>
            <option value="">--Select Course--</option>
            @foreach($institutes as $institute)
            <optgroup label="{{ $institute->name ?? '' }}">
                @foreach($institute->courses as $course)
                @if($course->status)
                <option  value="{{ $course->id }}">
                    {{ $course->name }}
                </option>
                @endif
                @endforeach
            </optgroup>
            @endforeach
        </select>
    </div>
</div>
