@extends('admin.layouts.app')

@section('content')

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
        </li>
    </ul>
</div>

@if(Session::has('message'))
<div class="alert alert-success" role="alert">
    <p> {{ Session::get('message') }}</p>
</div>
@endif


<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-globe"></i> Lecture Sheet Delivery
                </div>
            </div>
            <div>
                <div class="caption">

                </div>

            </div>
            <div class="portlet-body">
                        <div>
                            {!!
                            Form::open(['action'=>['Admin\DoctorsCoursesController@doctor_course_lecture_sheet'],'method'=>'POST','files'=>'true','class'=>'form-horizontal'])
                            !!}
                            <input type="hidden" name="lecture_sheet_number" value="{{ $count ?? 0 }}">
                            <input type="hidden" name="doctor_course_id" value="{{ $doctor_course->id }}">
                            <input type="hidden" name="lecture_sheet_number" value="{{ $count ?? 0 }}">
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-1 control-label">Courier(<i class="fa fa-asterisk ipd-star"
                                            style="font-size:9px;"></i>)</label>
                                        <div class="col-md-2">
                                            <div class="input-icon right">
                                                @php $couriers->prepend('Select Courier', ''); @endphp
                                                {!! Form::select('courier_id',$couriers, $doctor_course->courier_id
                                                ,['class'=>'form-control', 'id'=>'courier_id']) !!}<i></i>
                                            </div>
                                        </div>

                                        <label class="col-md-2 control-label">Courier memo number(<i class="fa fa-asterisk ipd-star"
                                            style="font-size:9px;"></i>)</label>

                                        <div class="col-md-2">
                                            <div class="input-icon right">
                                                <input class="form-control" type="text" id="courier_memo_no" name="courier_memo_no"
                                                 value="{{ $doctor_course->courier_memo_no }}">
                                            </div>
                                        </div>

                                        <label class="col-md-1 control-label">Quantity(<i class="fa fa-asterisk ipd-star"
                                            style="font-size:9px;"></i>) </label>
                                        <div class="col-md-2">
                                            <div class="input-icon right">
                                                @if(isset($first_shipment))
                                                        <select name="lecture_sheet_packet" class="form-control">
                                                            <option value="" {{ ($first_shipment->packet ?? '') == '' ? 'selected' : '' }}>Select Packet</option>
                                                            <option value="1 packet" {{ ($first_shipment->packet ?? '') == '1 packet' ? 'selected' : '' }}>1 packet</option>
                                                            <option value="2 packets" {{ ($first_shipment->packet ?? '') == '2 packets' ? 'selected' : '' }}>2 packets</option>
                                                            <option value="3 packets" {{ ($first_shipment->packet ?? '') == '3 packets' ? 'selected' : '' }}>3 packets</option>
                                                            <option value="4 packets" {{ ($first_shipment->packet ?? '') == '4 packets' ? 'selected' : '' }}>4 packets</option>
                                                            <option value="5 packets" {{ ($first_shipment->packet ?? '') == '5 packets' ? 'selected' : '' }}>5 packets</option>
                                                        </select>
                                                @else
                                                <select name="lecture_sheet_packet" class="form-control">
                                                    <option value="">Select Packet</option>
                                                    <option value="1 packet">1 packet</option>
                                                    <option value="2 packets">2 packets</option>
                                                    <option value="3 packets">3 packets</option>
                                                    <option value="4 packets">4 packets</option>
                                                    <option value="5 packets">5 packets</option>
                                                </select>
                                                @endif
                                            </div>
                                        </div>

                                        <div name="send_sms" value="Send Sms" class="btn btn-primary send_sms" style="margin:0px;"> Send
                                            SMS </div>

                                    </div>
                                </div>
                                @if (($doctor_course->batch->shipment ?? '') == 2)
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-md-1 control-label">Courier(<i class="fa fa-asterisk ipd-star"
                                            style="font-size:9px;"></i>) </label>
                                        <div class="col-md-2">
                                            <div class="input-icon right">
                                                 @php $couriers->prepend('Select Courier', ''); @endphp
                                                {!! Form::select('courier_id',$couriers, $doctor_course->courier_id
                                                ,['class'=>'form-control', 'id'=>'courier_id']) !!}<i></i>
                                                
                                            </div>
                                        </div>

                                        <label class="col-md-2 control-label">Courier memo number(<i class="fa fa-asterisk ipd-star"
                                            style="font-size:9px;"></i>)</label>

                                        <div class="col-md-2">
                                            <div class="input-icon right">
                                                <input class="form-control" type="text" id="courier_memo_no" name="courier_memo_no2"
                                                value="{{ $second_shipment->courier_memo_no ?? '' }}">
                                            </div>
                                        </div>


                                        <label class="col-md-1 control-label">Quantity(<i class="fa fa-asterisk ipd-star"
                                                style="font-size:9px;"></i>) </label>
                                        <div class="col-md-2">
                                            <div class="input-icon right">
                                                @if( isset($second_shipment))
                                                    <select name="lecture_sheet_packet2" class="form-control">
                                                        <option value="" {{ $second_shipment->packet == '' ? 'selected' : '' }}>Select Packet</option>
                                                        <option value="1 packet" {{ $second_shipment->packet == '1 packet' ? 'selected' : '' }}>1 packet</option>
                                                        <option value="2 packets" {{ $second_shipment->packet == '2 packets' ? 'selected' : '' }}>2 packets</option>
                                                        <option value="3 packets" {{ $second_shipment->packet == '3 packets' ? 'selected' : '' }}>3 packets</option>
                                                        <option value="4 packets" {{ $second_shipment->packet == '4 packets' ? 'selected' : '' }}>4 packets</option>
                                                        <option value="5 packets" {{ $second_shipment->packet == '5 packets' ? 'selected' : '' }}>5 packets</option>
                                                    </select>
                                                @else
                                                <select name="lecture_sheet_packet2" class="form-control">
                                                    <option value="">Select Packet</option>
                                                    <option value="1 packet">1 packet</option>
                                                    <option value="2 packets">2 packets</option>
                                                    <option value="3 packets">3 packets</option>
                                                    <option value="4 packets">4 packets</option>
                                                    <option value="5 packets">5 packets</option>
                                                </select>
                                                @endif
                                            </div>
                                        </div>
                                        <div name="send_sms" value="Send Sms" class="btn btn-primary send_sms2" style="margin:0px;"> Send
                                            SMS </div>

                                    </div>
                                </div>
                                @endif
                        </div>

                <table class="table table-striped table-bordered ">
                    <thead>
                        <tr>

                            <th style="text-align: center">Lecture Sheet</th>
                            <th style="text-align: center">Delivery Status ( <input type="checkbox"
                                    id="checkbox_lecture_sheet"> Select All ) </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( isset($lecture_sheets) )
                            @foreach($lecture_sheets as $lecture_sheet)
                            <tr>
                                <td style="{{ (boolean) $lecture_sheet->doctor_delivered ? 'background-color:#dcfdd7;' : '' }}">
                                    {{ $lecture_sheet->name }}
                                </td>
                                <td style="{{ (boolean) $lecture_sheet->doctor_delivered ? 'background-color:#dcfdd7;' : '' }}" >
                                    <input type="checkbox" class='btn btn-primary lecture_sheet_id'
                                        name="lecture_sheet_id[]" value="{{ $lecture_sheet->id }}"
                                        {{ (boolean) $lecture_sheet->doctor_delivered ? 'Checked' : '' }}
                                    >
                                </td>
                            </tr>

                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div class="">
                    <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="margin:5px;">
                    <input type="submit" name="submit_print" value="Submit & Print" class="btn btn-primary"
                        style="margin:5px;">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')



<script type="text/javascript">
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    $(document).ready(function () {

        $("body").on("click", "#checkbox_lecture_sheet", function () {
            //alert('Bismillah');
            if ($('#checkbox_lecture_sheet').is(':checked')) {
                $('.lecture_sheet_id').prop('checked', true);
            } else {
                $('.lecture_sheet_id').prop('checked', false);
            }
        });

        $("body").on("click", ".send_sms", function () {
            var doctor_course_id = $("[name = doctor_course_id]").val();
            var courier_id = $("[name = courier_id]").val();
            var courier_memo_no = $("[name = courier_memo_no]").val();
            var lecture_sheet_packet = $("[name = lecture_sheet_packet]").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/message',
                dataType: 'HTML',
                data: {
                    doctor_course_id: doctor_course_id,
                    courier_id: courier_id,
                    courier_memo_no: courier_memo_no,
                    lecture_sheet_packet: lecture_sheet_packet
                },
                success: function (data) {
                    alert('SMS send successfully.');
                    window.location.reload()
                }
            });
        });

        $("body").on("click", ".send_sms2", function () {
            var doctor_course_id = $("[name = doctor_course_id]").val();
            var courier_id = $("[name = courier_id]").val();
            var courier_memo_no = $("[name = courier_memo_no2]").val();
            var lecture_sheet_packet = $("[name = lecture_sheet_packet2]").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/message2',
                dataType: 'HTML',
                data: {
                    doctor_course_id: doctor_course_id,
                    courier_id: courier_id,
                    courier_memo_no: courier_memo_no,
                    lecture_sheet_packet: lecture_sheet_packet
                },
                success: function (data) {
                    alert('SMS send successfully.');
                    window.location.reload()
                }
            });
        });


    })
</script>

@endsection