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
                Doctor Course System Driven
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
                        <i class="fa fa-reorder"></i>Doctor Course System Driven
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>'Admin\DoctorsCoursesController@system_driven_save','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Select Doctor</div>
                            <div class="panel-body">
                                <div class="form-group ">
                                    <label class="col-md-3 control-label">Doctor</label>
                                    <div class="col-md-3" id="id_div_system_driven">
                                    <div class="input-icon right">
                                        <select name="doctor_course_id" required class="form-control doctor2">
                                                <option value="{{$doctor_course->id}}" selected="selected" disabled>{{$doctor_course->doctor->name.' - '.$doctor_course->doctor->bmdc_no}}</option>
                                        </select>
                                        <input type="hidden" name="doctor_course_id" value="{{$doctor_course->id}}" />
                                    </div>
                                    </div>
                                </div>

                                <div  id="system_driven">
                                    <div class="form-group ">
                                        <label class="col-md-3 control-label">System Driven</label>
                                        <div class="col-md-3" id="id_div_system_driven">
                                            <label class="radio-inline">
                                                <input type="radio" name="system_driven"  value="Yes" {{ $doctor_course->system_driven == "Yes"?"checked": ""}}  > Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="system_driven"  value="No"  {{ $doctor_course->system_driven == "No"?"checked": ""}} > No
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label class="col-md-3 control-label">System Driven Change Count</label>
                                        <div class="col-md-3" id="id_div_system_driven_count">
                                            <label class="input-icon right">
                                                <input type="number" name="system_driven_count" max="{{ $doctor_course->batch->system_driven_change_count_max??'100' }}" value="{{ $doctor_course->system_driven_count ?? '' }}"   >
                                            </label>
                                        </div>
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
                                <a href="{{ url('admin/doctors-courses') }}" class="btn btn-default">Cancel</a>
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

        }

    </script>


@endsection
