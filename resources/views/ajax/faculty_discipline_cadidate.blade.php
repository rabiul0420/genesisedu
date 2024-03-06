<div class="modal-body">
    @if ($institute_type == 1)
        <div class="form-group row mb-2">
            <label for="inputPassword" class="col-sm-4 col-form-label text-left">Faculty</label>
            <div class="col-sm-8">
                <input type="text" class="form-control faculty_id" required name="faculty_id" readonly
                    value={{ $doctor_course->faculty->name ?? '' }}>
               
            </div>
        </div>
    @endif
    <input type="hidden" class="form-control" required name="doctor_course_id" value="{{ $doctor_course->id }}" readonly>
    <div class="form-group row mb-2">
        <label for="inputPassword" class="col-sm-4 col-form-label text-left">Discipline</label>
        <div class="col-sm-8">
            <select name="subject_id" required class="form-control medical2" id="subject_id">
                <option class="form-control" value="">Select Discipline</option>
                @foreach ($subjects as $key => $subject)
                    <option class="form-control" value="{{ $key }}">{{ $subject }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="candidateType" class="form-group row mb-2 {{ $institute_type == 1 ? ' ' : 'd-none' }}">
        <label class="col-sm-4 col-form-label text-left">Candidate Type (<i class="fa fa-asterisk ipd-star"
                style="font-size:11px;"></i>) </label>
        <div class="col-sm-8">
            <select class="form-select" name="candidate_type">
                <option value="">Select Candidate Type</option>
                <option value="Autonomous/Private">Autonomous/Private</option>
                <option value="Government">Government</option>
                <option value="BSMMU">BSMMU</option>
                <option value="Armed Forces">Armed Forces</option>
                <option value="Others">Others</option>
            </select>
        </div>
    </div>

    @if ($is_combined)
        <div class="bcps-subjects">
            <div class="form-group">
                <select name="bcps_subject_id" class="form-control medical2" id="bcps_subject_id">
                    <option class="form-control" value="">FCPS Part-1 Discipline</option>
                    @foreach ($bcps_subjects as $key => $bcps_subject)
                        <option class="form-control" value="{{ $key }}">{{ $bcps_subject }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
</div>


<div class="modal-footer">
    <a href="javascript:void(0)" class="btn btn-secondary" id="close-button" data-bs-dismiss="modal">Close</a>
    <input type="submit" class="btn btn-primary" id="submit-button" value="Submit">
</div>


@section('js')
    <script type="text/javascript">
        $(document).ready(function() {

            




        })
    </script>
@endsection
