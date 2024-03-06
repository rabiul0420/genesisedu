{{-- <div class="form-group">
    <label class="col-md-3 control-label">Session(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="controls">
            <select name="session_id" id="session_id" class=form-control required>
                <option disabled selected value="">Select Session</option>
                    @foreach($sessions as $key=>$value)
                            <option  value=" {{ $id?? '' }}">{{$value ?? ''}}</option>   
                    @endforeach
            </select>
        </div>
    </div>
</div>
{{-- <div class="form-group">
    <label class="col-md-3 control-label">Session(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="controls">
            <select name="session_id" id="session_id" class=form-control required>
                <option disabled selected value="">Select Session</option>
                    @foreach($courseYear->course_year_session as $session)
                        @if($session->session)
                            <option  value=" {{$session->session->id ?? '' }}">{{$session->session->name ?? '' }}</option>   
                        @endif
                    @endforeach
            </select>
        </div>
    </div>
</div> --}}

<div class="form-group">
    <label class="col-md-3 control-label">Session(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="controls">
            <select name="session_id" id="session_id" class=form-control required>
                <option disabled selected value="">Select Session</option>
                    @foreach($sessions as $key=>$session)
                            <option  value=" {{ $key ?? '' }}">{{ $session ?? "" }}</option>   
                    @endforeach
            </select>
        </div>
    </div>
</div>

