@php
    $batches->prepend( '--Select Batches--', '' );
@endphp


@if( ( $selection_only ?? false ) === true )

    {!! Form::select( $name, $batches, $selected_batch_id, [ 'class'=>'form-control','required'=>'required','id' => $id ]) !!}
@else

    <div class="form-group">
        <label class="col-md-{{$label_column_count ?? '3' }} control-label">{{ $label ?? 'Batches'}} (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-{{$column_count ?? '3' }}">
            <div class="input-icon right">
                {!! Form::select( ( $name ?? 'batch_id' ), $batches, $selected_batch_id, ['class'=>'form-control','required'=>'required','id' => ($id ?? 'batch_id')]) !!}
            </div>
        </div>
    </div>

@endif