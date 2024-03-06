@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li> <i class="fa fa-angle-right"> </i> <a href="#">Sms</a></li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Sms Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\SmsController@update', $sms->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">


                        <div class="form-group">
                            <label class="col-md-2 control-label">Title <i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i></label>
                            <div class="col-md-10">
                                <div class="input-icon right">
                                    <input type="text" name="title" required value="{{$sms->title}}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Sms <i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i></label>
                            <div class="col-md-10">
                                <div class="input-icon right">
                                    <textarea name="sms"  style="width:100%;height:100%;" required>{{ $sms->sms }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Sms Type <i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i></label>
                            <div class="col-md-3">
                                {!! Form::select('type', [''=>'Select Type', 'I' => 'Individual', 'A' => 'All', 'B' => 'Batch', 'C'=>'Course'], $sms->type, ['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="next_data">
                            @if ($sms->type=='I')
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Select Doctors <i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i> </label>
                                    <div class="col-md-3">
                                        @php  $doctors->prepend('Select Doctor', ''); @endphp
                                        {!! Form::select('doctor_id[]',$doctors, $doctor_ids, ['class'=>'form-control','multiple'=>'multiple','required'=>'required','id'=>'doctor_id']) !!}<i></i>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Sms Event<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i> </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    @php  $sms_events->prepend('Select Sms Event', ''); @endphp
                                    {!! Form::select('sms_event_id', $sms_events, $sms->sms_event_id??'',['class'=>'form-control','required'=>'required']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Doctor course option <i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i> </label>
                            <div class="col-md-3">
                                {!! Form::select('sms_send_option', ['1'=>'All', '2' => 'Completed', '3' => 'No Payment', '4'=> 'In Progress'], $sms->sms_send_option??'',['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Status <i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i> </label>
                            <div class="col-md-3">
                                {!! Form::select('status', [''=>'Select Status', '1' => 'Active', '0' => 'InActive'], $sms->status, ['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>
                                
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-9">
                                <button type="submit" class="btn btn-info">Update</button>
                                <a href="{{ url('admin/sms') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

@section('js')    

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script> 

    <script type="text/javascript">
        $(document).ready(function() {

            if ($( "#doctor_id" ).length) {
                $('#doctor_id').select2({});
            }

            $("body").on( "change", "[name='type']", function() {
                var type = $(this).val();
                if(type=='I')
                {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/admin/sms-type',
                        dataType: 'HTML',
                        data: {type : type},
                        success: function( data ) {
                            $('.next_data').html(data);
                            $('.course').html('');
                            $('.batch').html('');
                            $('#doctor_id').select2({
                                minimumInputLength: 3,
                                placeholder: "Please type doctor's Name or Mobile  Number or BMDC No",
                                escapeMarkup: function (markup) { return markup; },
                                language: {
                                    noResults: function () {
                                        return "No Doctors found, for add new doctor please <a target='_blank' href='{{ url('admin/doctors/create') }}'>Click here</a>";
                                    }
                                },
                                ajax: {
                                    url: '/admin/sms-search-doctors',
                                    dataType: 'json',
                                    type: "GET",
                                    quietMillis: 50,
                                    data: function (term) {
                                        return {
                                            term: term
                                        };
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: $.map(data, function (item) {
                                                $('.select2-selection__rendered').attr('data-id' , item.id);
                                                return { id:item.id , text: item.name_bmdc };
                                            })
                                        };
                                    }
                                }
                            }).trigger('change');
                        }
                    });
                }
                else
                {
                    $('.next_data').html('');
                }
                
            })

            $('#doctor_id').select2({
                minimumInputLength: 3,
                placeholder: "Please type doctor's Name or Mobile  Number or BMDC No",
                escapeMarkup: function (markup) { return markup; },
                language: {
                    noResults: function () {
                        return "No Doctors found, for add new doctor please <a target='_blank' href='{{ url('admin/doctors/create') }}'>Click here</a>";
                    }
                },
                ajax: {
                    url: '/admin/sms-search-doctors',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                $('.select2-selection__rendered').attr('data-id' , item.id);
                                return { id:item.id , text: item.name_bmdc };
                            })
                        };
                    }
                }
            }).trigger('change');

        })
    </script>


@endsection