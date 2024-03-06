@php $sessions->prepend('--Select--', ''); @endphp

@if( ( $selection_only ?? false ) === true )
    {!!
        Form::select( ( $name ?? 'session_id' ), $sessions, $selected_session_id,
        [ 'class' => 'form-control', 'required' => 'required', 'id' => ( $id ?? 'session_id' ) ])
    !!}
@else
    <div class="form-group">
        <label class="col-md-{{$label_column_count ?? 3}} control-label">{{ $label ?? 'Session' }} (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-{{$column_count ?? '3' }}">
            <div class="input-icon right">
                {!!
                    Form::select( ( $name ?? 'session_id' ), $sessions, $selected_session_id,
                    [ 'class' => 'form-control', 'required' => 'required', 'id' => ( $id ?? 'session_id' ) ])
                !!}
            </div>
        </div>
    </div>
@endif