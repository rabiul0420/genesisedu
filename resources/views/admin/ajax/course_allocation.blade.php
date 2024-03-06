<div class="form-group">
    <label class="col-md-3 control-label">Allocation Course (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
    <div class="col-md-6">
        <div class="input-icon right">
{{--            {{ old( 'allocation_course_id', $instituteAllocationSeat->allocation_course_id ?? '' )  }}--}}
            <select name="allocation_course_id" class="form-control">
                <option value="">-- Select Course --</option>
                @foreach ( $allocationCourses as $id => $allocationCourse )
                    <option {{ old( 'allocation_course_id', $instituteAllocationSeat->allocation_course_id ?? '' ) == $id ? 'selected' : '' }}
                            value="{{ $id  }}">{{ $allocationCourse }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>