
@php
    $label = strtoupper( $name );
    $name =  strtolower( $name );
    $required_label =  '(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)';

    $num_field = $name . '_number';
    $mark_field = $name . '_mark';
    $ng_mark_field = $name . '_negative_mark';
    $ng_mark_range_field = $name . '_negative_mark_range';

    $num_value = old( $name . '_number', ($question_type->{$name . '_number'} ?? '') );
    $mark_value = old( $name . '_mark', ($question_type->{$name . '_mark'} ?? '') );
    $ng_mark_value = old( $name . '_negative_mark', ($question_type->{$name . '_negative_mark'} ?? '') );
    $ng_mark_range_value = old( $name . '_negative_mark_range', ($question_type->{$name . '_negative_mark_range'} ?? '') );

    $required = ($required ?? false) ? 'required':''

@endphp

<div class="form-group">
    <label class="col-md-2 control-label number-label">Number of {{$label}} {!! $required_label !!}</label>
    <div class="col-md-4">
        <div class="input-icon right">
            <input type="number" name="{{ $num_field }}" {{$required}} value="{{ $num_value }}" class="form-control">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label mark-label">Mark of {{$label}} {!! $required_label !!}</label>
    <div class="col-md-4">
        <div class="input-icon right">
            <input type="number" name="{{  $mark_field }}" {{$required}} value="{{ $mark_value }}" class="form-control">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label ng-mark-label">{{$label}} Negative Mark/stamp {!! $required_label !!}</label>
    <div class="col-md-4">
        <div class="input-icon right">
            <input type="number" name="{{ $ng_mark_field  }}" {{$required}} step="any" value="{{ $ng_mark_value }}" class="form-control">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label ng-mark-range-label">{{$label}} Negative Mark Range</label>
    <div class="col-md-4 nagetive_mark_range">
        <div class="input-icon right">
            <input type="text" name="{{ $ng_mark_range_field }}" step="any" value="{{ $ng_mark_range_value }}" class="form-control">
        </div>
    </div>
</div>


