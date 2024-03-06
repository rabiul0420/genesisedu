<div class="form-group">
    <label class="col-md-3 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            @php $sessions->prepend( 'Select sessions', '' ) @endphp
            {!! Form::select( 'session_id[]',$sessions, '', ['class'=>'form-control  select2 ','data-placeholder' => 'Select Faculty','multiple' => 'multiple',] ) !!}<i></i>
        </div>
    </div>
</div>
 