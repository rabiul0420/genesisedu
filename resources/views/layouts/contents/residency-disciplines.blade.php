@php
    $residency_disciplinees->prepend( '--Select Batches--', '' );
@endphp


@if( ( $selection_only ?? false ) === true )

    {!! Form::select( $name, $residency_disciplinees ?? [], $selected_residency_discipline_id ?? '', [ 'class'=>'form-control','required'=>'required','id' => $id ]) !!}
@else

    <div class="form-group">
        <label class="col-md-{{$label_column_count ?? '3' }} control-label">{{ $label ?? 'Discipline'}} (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-{{$column_count ?? '3' }}">
            <div class="input-icon right">
                {!! Form::select( ( $name ?? 'residency_discipline_id' ), $residency_disciplinees, $selected_residency_discipline_id,
                    ['class'=>'form-control','required'=>'required','id' => ($id ?? 'batch_id')]) !!}
            </div>
        </div>
    </div>

@endif