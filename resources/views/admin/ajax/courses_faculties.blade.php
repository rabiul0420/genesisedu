<div class="form-group col-md-2">
    <label class="control-label">Faculty </label>
    <div class="controls">
        @php  $faculties->prepend('Select Faculty', ''); @endphp
        {!! Form::select('faculty_id',$faculties, old('faculty_id')?old('faculty_id'):'',['class'=>'form-control','id'=>'faculty_id']) !!}<i></i>
    </div>
</div>


@if( isset( $bcps_subjects ) && $bcps_subjects->count() )

<div class="form-group col-md-2">
    <label class="control-label">FCPS Part-1 Discipline</label>
    <div class="controls">
        @php $bcps_subjects->prepend( 'Select BCPS Subject', '' ); @endphp
        {!! Form::select('bcps_subject_id', $bcps_subjects,'',[ 'class' => 'form-control', 'required'=>'required', 'id' => 'bcps_subject_id' ]) !!}<i></i>
    </div>
</div>

@endif