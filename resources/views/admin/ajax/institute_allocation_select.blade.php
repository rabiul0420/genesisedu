<div class="form-group">
	<label class="col-md-3 control-label">Institute Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
	<div class="col-md-6">
		<div class="input-icon right">
			<select name="institute_allocation_id" class="form-control">
				<option value="">-- Select Institute --</option>
				@foreach ($instituteAllocations as $key => $instituteAllocation)
					<option {{ old( 'institute_allocation_id', $instituteAllocationSeat->institute_allocation_id ?? '' ) == $key ? 'selected' : '' }}
							value="{{ $key }}">{{ $instituteAllocation }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>