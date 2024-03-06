@php
    $slot = $slot ?? new \App\ScheduleTimeSlot();

    $date = ($slot->datetime ?? '') ? $slot->datetime->format( 'Y-m-d' ):'';
    $time = ($slot->datetime ?? '') ? $slot->datetime->format( 'h:i A' ):'';

@endphp

<div class="container-fluid schedule-slots" style="border: 1px solid rgb(204, 204, 204); border-radius: 5px; padding-bottom: 16px; margin-bottom: 20px;box-shadow: 0 2px 5px #ccbccb60">

    <div class="form-group" style="margin-bottom: 0px;
                                background-color: #e7e7e9;
                                padding-top: 10px;
                                padding-bottom: 10px;
                                box-shadow: 0 2px 5px #eee;
                                border-bottom: 1px solid #ccc;
                                border-radius: 5px 5px 0 0;
    ">

        <label class="col-md-1 control-label" style="width: auto;">Date</label>
        <div class="col-md-2">

            @if( $action == 'edit' )
                <input type="hidden" class="slot_id" name="details[0][slot_id]" value="{{ $slot->id ?? '' }}">
            @endif

            <input required="" class="form-control item-date" type="text" name="details[0][date]" placeholder="Date" value="{{ $date }}">
        </div>

        <label class="col-md-1 control-label" style="width: auto;">Time</label>

        <div class="col-md-2">
            <input required="" class="form-control timepicker" type="text" name="details[0][time]" placeholder="Time" value="{{ $time }}">
        </div>

        <div class="pull-right" style="margin-right: 15px;">
            <a class="btn btn-warning btn-sm remove-slot" href="">Remove</a>
        </div>

    </div>

    <div style="width: 100%; text-align: center;">
        <div class="schedule-contents">

            @if( ($slot->schedule_details ?? [])  instanceof \Illuminate\Support\Collection )
                @foreach( ($slot->schedule_details ?? []) as $content )
                    @include( 'admin.batch_schedules.schedule_slot.content', [ 'content' => $content, 'class_type' => 1 ] )
                @endforeach
            @endif

        </div>
        <a href="" class="btn btn-info add-schedule-content" style="margin-top: 20px;" >+Add More</a>
    </div>

</div>