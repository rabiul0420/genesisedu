<div class="form-body">

    @if( $has_class_id_column )
    <div style="border: 1px dashed #999; padding-top: 10px; margin-bottom: 10px">

        <div class="form-group">
            <label class="col-md-3 control-label">Class Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
            <div class="col-md-3">
                <div class="input-icon right">
                    {!!
                        Form::select( 'year', $years, old( 'year', $lecture_video->class->year ?? '' ),
                        [ 'class' => 'form-control year', 'id' => 'year', 'required' => 'required' ] )
                    !!}
                </div>
            </div>
        </div>

        <div id="institutes">
            {!! $institutes_view ?? '' !!}
        </div>

        <div id="courses">
            {!! $courses_view ?? '' !!}
        </div>

        <div id="sessions">
            {!! $sessions_view ?? '' !!}
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label">Class</label>
            <div class="col-md-3">
                <div class="input-icon right">
                    <select name="classes" class="form-control select2 topic-selection">
                        <option value="{{$lecture_video->class->id ?? ''}}" selected="selected">{{$lecture_video->class->name ?? ''}}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
@endif

    <div class="form-group">
        <label class="col-md-3 control-label">Lecture Video Type (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
        @php $video_types->prepend('--Select type--', '') @endphp
        <div class="col-md-3">
            {!! Form::select( 'type', $video_types, old( 'type', $lecture_video->type ?? '' ), [ 'class' => 'form-control' ] ) !!}<i></i>
        </div>
    </div>

    <div class="form-group mentor">
            <label class="col-md-3 control-label">Mentor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
            <div class="col-md-3">
                <div class="input-icon right">
                    @php $teachers->prepend( 'Select mentor', '' ) @endphp
                    {!! Form::select( 'teacher_id',$teachers, $lecture_video->teacher_id ??'', ['class'=>'form-control  select2 ','data-placeholder' => 'Select Faculty'] ) !!}<i></i>
                </div>
            </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Displaying Lecture Name  (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-3">
            <div class="input-icon right">
                <input type="text" name="name" required value="{{old('name',  $lecture_video->name ?? '') }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Information Details (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-3">
            <div class="input-icon right">
                <textarea name="description" class="form-control" cols="10" rows="4" required>{{ old( 'description', $lecture_video->description ?? '' ) }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Nick Name  (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-3">
            <div class="input-icon right">
                <input type="text" name="nickname" value="{{old('nickname',  $lecture_video->nickname ?? '') }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">PDF File </label>
        <div class="col-md-3">
            <div class="input-icon right">
                <input class="form-control" type="file" name="pdf">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Lecture Web Address (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-3">
            <div class="input-icon right">
                <input type="text" name="lecture_address" value="{{ old('lecture_address', $lecture_video->lecture_address ?? '' ) }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Password (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-3">
            <div class="input-icon right">
                <input type="text" name="password" value="{{ old( 'password', $lecture_video->password ?? '') }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Select Status  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
        <div class="col-md-3">
            {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $lecture_video->status ?? '',['class'=>'form-control']) !!}<i></i>
        </div>
    </div>

    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                <a href="{{ url('admin/lecture-video') }}" class="btn btn-default">Cancel</a>
            </div>
        </div>
    </div>

</div>