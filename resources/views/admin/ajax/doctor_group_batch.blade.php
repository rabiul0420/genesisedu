<div class="form-group">
    <label class="col-md-4 control-label">Select Batches(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-5">
        {!! Form::select('batch_id[]',$batches, old('batch_id', $selected_batches ?? '' ),['class'=>'form-control select2', 'data-placeholder' => 'Select Batch', 'multiple' => 'multiple','id'=>'batch_id']) !!}<i></i>
    </div>
    <input type="checkbox" id="checkbox_batch" > Select All
</div>

@if( ( $action ?? request()->action ) != 'edit' )  
    <div class="form-group">
        <label class="col-md-4 control-label">Target Batch(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-5">
            @php 

                $special_batches->prepend( '--Select--', '' );
                $specialBathData = [
                    'class'=>'form-control select2',
                    'data-placeholder' => 'Select Special Batch',
                    'id'=>'special_batch_id' 
                ];

                if( isset( $special_batch ) && $special_batch->id ) {
                    $specialBathData[ 'readonly' ] = 'readonly';
                }
            @endphp
            {!! Form::select('special_batch_id', $special_batches,  old('special_batch_id', isset($special_batch) ? ( $special_batch->batch_id ?? '' ):'' ) , $specialBathData) !!}<i></i>
        </div>
    </div>
@endif


