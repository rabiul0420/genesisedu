<div class="sessions">
    <div class="form-group mt-3">
        <label class="col-md-3 control-label">Sessions (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-3">
            @php  $sessions->prepend('--Select Session--', ''); @endphp
            {!! Form::select('session_id',$sessions,'',['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
        </div>
    </div>
</div>

<div class="faculties">
    <div class="form-group mt-3">
        <label class="col-md-3 control-label">{{ isset( $bcps_subjects ) && $bcps_subjects->count() ? 'Residency ':''  }} Faculty (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-3">
            @php  $faculties->prepend('--Select Faculty--', ''); @endphp
            {!! Form::select('faculty_id',$faculties,'',['class'=>'form-control','required'=>'required','id'=>'faculty_id']) !!}<i></i>
        </div>
    </div>
</div>

<input type="hidden" id="is_combined" value="{{ isset( $bcps_subjects ) && $bcps_subjects->count() ? 'yes':'no' }}">


@if( isset( $bcps_subjects ) && $bcps_subjects->count() )

    <div class="faculties">
        <div class="form-group mt-3">
            <label class="col-md-3 control-label">FCPS Part-1 Discipline (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
            <div class="col-md-3">
                @php $bcps_subjects->prepend( '--Select BCPS Subject--', '' ); @endphp
                {!! Form::select('bcps_subject_id', $bcps_subjects,'',[ 'class' => 'form-control', 'required'=>'required', 'id' => 'bcps_subject_id' ]) !!}<i></i>
            </div>
        </div>
    </div>

@endif
