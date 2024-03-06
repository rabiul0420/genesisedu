@if( $faculties )
    <div class="form-group">
        @if( isset( $faculty_label ) )
            <label class="col-md-3 control-label">{{  $faculty_label }} </label>
        @else
            <label class="col-md-3 control-label">{{ ( $is_combined ?? false ) ? 'Residency Faculty' : 'Faculty'}} </label>
        @endif
        <div class="col-md-3">
{{--            @php  request()->get( 'prepend' ) != 'false' ?  $faculties->prepend('Select Faculty', '') : null; @endphp--}}
            {!! Form::select('faculty_id[]',$faculties, old('faculty_id')?old('faculty_id'):'',['class'=>'form-control  select2 ',
                'data-placeholder' => 'Select Faculty', 'multiple' => 'multiple','id'=>'faculty_id']) !!}<i></i>
        </div>
        <input type="checkbox" data-target="#faculty_id" class="all-selector" > Select All
    </div>

    @if( isset($subjects) && $subjects !== null )
        <div class="form-group">

            @if( isset( $discipline_label ) )
                <label class="col-md-3 control-label">{{  $discipline_label }}</label>
            @else
                <label class="col-md-3 control-label">{{ ( $is_combined ?? false ) ? 'FCPS Part-1 Discipline' : 'Discipline'}} </label>
            @endif

            <div class="col-md-3">
{{--                @php  request()->get( 'prepend' ) != 'false' ?  $subjects->prepend('Select Faculties', '') : null; @endphp--}}
                {!! Form::select('subject_id[]',$subjects, old('subject_id')?old('subject_id'):'',['class'=>'form-control  select2 ',
                    'data-placeholder' => 'Select Discipline', 'multiple' => 'multiple','id'=>'subject_id']) !!}<i></i>
            </div>
            <input type="checkbox" data-target="#subject_id" class="all-selector" > Select All
        </div>
    @endif
    <script>App.handleSelect2AllSelector()</script>
@endif
