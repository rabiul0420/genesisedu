<div class="form-group col-md-2">
    <h5>Session <span class="text-danger"></span></h5>
    <div class="controls">
        <select name="session_id" id="session_id" class=form-control required>
            <option disabled selected value="">Select Session</option>
            @if ($courseYear != null)
                @foreach($courseYear->course_year_session as $session)
                    <option  value=" {{$session->session->id ?? '' }}">
                        {{$session->session->name ?? '' }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
</div>
