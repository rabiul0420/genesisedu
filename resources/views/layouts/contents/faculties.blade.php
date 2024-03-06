@if( $visibility ?? false )
    @php
        $faculties->prepend( '--Select Faculty--', '' );
    @endphp


    @if( ( $selection_only ?? false ) === true )

        {!! Form::select( $name, $faculties, $selected_faculty_id, [ 'class'=>'form-control','required'=>'required','id' => $id ]) !!}
    @else

        <div class="form-group">

            <label class="col-md-{{$label_column_count ?? '3' }} control-label">{{ $label ?? 'Faculty'}} (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>

            <div class="col-md-{{$column_count ?? '3' }}">
                <div class="input-icon right">
                    {!! Form::select( ( $name ?? 'faculty_id' ), $faculties, $selected_faculty_id, [ 'class'=>'form-control','required'=>'required', 'id' => ($id ?? 'faculty_id')]) !!}
                </div>
            </div>

        </div>

    @endif
@endif