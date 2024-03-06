@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                Doctor Course Payment Create
            </li>
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
                        <i class="fa fa-reorder"></i>Doctor Course Payment Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>'Admin\DoctorCoursePaymentController@store','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Select Doctor Information</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <input type="hidden" name="doctor_course_id" value="{{ $doctor_course->id}}">

                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Transaction ID (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)  </label>
                                    <div class="col-md-3">
                                        <input type="text" name="transaction_id" class="form-control" required>
                                    </div>
                                    <label class="col-md-1 control-label">Amount (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)  </label>
                                    <div class="col-md-3">
                                        <input type="number" name="amount" class="form-control" required>
                                    </div>
                                </div>



                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/doctor-course') }}" class="btn btn-default">Cancel</a>
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


            $('.doctor2').select2({
                minimumInputLength: 3,
                placeholder: "Please type doctor's name or bmdc no",
                escapeMarkup: function (markup) { return markup; },
                language: {
                    noResults: function () {
                        return "No Doctors found, for add new doctor please <a target='_blank' href='{{ url('admin/doctors/create') }}'>Click here</a>";
                    }
                },
                ajax: {
                    url: '/admin/search-doctors',
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
                                let title = item.name + " - " + (item.bmdc_no || "") + " - " + (item.phone || "");
                                return { id:item.id , text: title };
                            })
                        };
                    }
                }
            });


        })
    </script>


@endsection
