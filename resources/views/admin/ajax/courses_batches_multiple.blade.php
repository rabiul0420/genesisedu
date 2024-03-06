<div class="form-group">
    <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        {!! Form::select('batch_id[]',$batches, old('batch_id')?old('batch_id'):'',[
                'class'=>'form-control select2',
                'required'=>'required',
                'id'=>'batch_id',
                'multiple' => 'multiple',
                'data-placeholder' => '--select--'
            ])
        !!}<i></i>
    </div>
</div>