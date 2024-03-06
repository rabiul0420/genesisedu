<div class="modal-header">
<h5 class="modal-title" id="system_driven_header">System Driven Rules & Regulations :</h5>
</div>
<div class="modal-body system_driven_body">
    <div>
        <div><h4>{!! $batch->name !!}</h4></div>
               
        <div>
            {!! $batch->system_driven_text !!}
        </div>                        
    </div>
</div>

<div class="modal-footer">
<label class="text text-info"><span style="font-weight:bold;">System Driven : {{ $batch->system_driven }}</span></label>
<label class="text doctor-course-id"  data-batch-id="{{$batch->id}}" data-doctor-course-id="{{$doctor_course->id}}" >Do you agree ? </label>
<label class="radio-inline"  style="cursor:pointer;" ><input type="radio" name="system_driven" value="Yes" {{ $doctor_course->system_driven == "Yes"?'checked':'' }}> Yes </label>
<label class="radio-inline"  style="cursor:pointer;" ><input type="radio" name="system_driven" value="No" {{ $doctor_course->system_driven == "No"?'checked':'' }}> No </label>                                    
<button type="button" class="btn btn-xs btn-secondary closed" style="color:white;background-color:green" data-dismiss="modal" {{ $doctor_course->system_driven == ("No" || "Yes") ?:'disabled' }}>Next</button>
</div>
