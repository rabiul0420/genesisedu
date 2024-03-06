<div class="batches">
    <div class="form-group">
        <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-3">
            <div class="input-icon right">
                {!! Form::select('batch_id[]',$batches, $selected_batches ?? [] ,
                [ 'class' => 'form-control select2', 'required' => 'required', 'id' => 'batch_id', 'multiple' => 'multiple', 'data-placeholder' => '--select--' ]) !!}<i></i>
            </div>
        </div>
    </div>
</div>