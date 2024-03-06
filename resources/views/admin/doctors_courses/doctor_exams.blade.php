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
                Doctor exam reopen
            </li>
        </ul>

    </div>

    @if(Session::has('message') || $message )
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message').''.($message??'') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Doctor exam reopen
                    </div>
                </div>
                <div class="portlet-body">
                    
                    
                    <div class="body">
                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Doctor Exam list</div>
                                <div class="panel-body">
                                <table id="table_1" class="table table-striped table-bordered table-hover datatable">
                                    <thead>
                                    <tr>
                                        <th style="width: 15%;">Serial No</th>
                                        <th style="width: 70%;">Exam</th>
                                        <th style="width: 15%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($doctor_exams))
                                            @foreach($doctor_exams as $doctor_exam)
                                            <tr><td>{{ $doctor_exam->exam->id }}</td><td>{{ $doctor_exam->exam->name }}</td><td><a href="{{ url('/admin/doctor-exam-reopen/'.$doctor_exam->doctor_course_id.'/'.$doctor_exam->exam_id)}}" class="btn btn-info">Reopen</a></td></tr>
                                            @endforeach
                                        @endif                        
                                    </tbody>
                                </table>
                                <div style="padding: 100px;"></div>                    
                                
                            </div>                            
                        </div>
                    </div>
                    
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
