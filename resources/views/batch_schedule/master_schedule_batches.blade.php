@extends('layouts.app')

@section('content')
<style>

.page-breadcrumb {
  display: inline-block;
  float: left;
  padding: 8px;
  margin: 0;
  list-style: none;
}
.page-breadcrumb > li {
  display: inline-block;
}
.page-breadcrumb > li > a,
.page-breadcrumb > li > span {
  color: #666;
  font-size: 14px;
  text-shadow: none;
}
.page-breadcrumb > li > i {
  color: #999;
  font-size: 14px;
  text-shadow: none;
}
.page-breadcrumb > li > i[class^="icon-"],
.page-breadcrumb > li > i[class*="icon-"] {
  color: gray;
}
.bg {
    background: #a6ecc5;
    color: #0f77b7;
}

</style>

<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        {{-- <h2 class="h2 brand_color">{{ 'Schedule' }}</h2> --}}

                        <div class="row" style="margin-left: 50px;">
                            <div class="col col-lg-10">
                                <h2 class="h2 brand_color">{{ 'Schedule' }}</h2>
                            </div>
                            {{-- <div class="col col-lg-2">
                                <div class="btn-group">
                                     <button class="btn brand_color btn-sm dropdown-toggle btn btn-outline-info" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        OUTLIER
                                    </button>

                                     <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ url('lecture-video') }}">Lecture Links</a>
                                        <a class="dropdown-item" href="{{ url('online-exam') }}">Exam & Results</a>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                
                <div class="my-3">
                    @include('components.subscription_add')
                </div>

                <style>
                    #headerAddSection1 {
                        background-color: blueviolet;
                        color: #fff;
                        padding: 24px 8px;
                        font-size: 20px;
                        line-height: 1.5;
                        text-align: center;
                        border-radius: 4px;
                    }

                    #headerAddSection1 .button {
                        background-color: #fff;
                        color: #D22B2B;
                        padding: 4px 16px;
                        border-radius: 10px;
                        font-size: 20px;
                        font-weight: 700;
                        box-shadow: 1px 1px 1px 1px #cccc;
                        text-align: center;
                        margin-left: 10px;
                    }
                </style>                    

                <div class="panel-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif


                    <div class="col-md-12 p-0">
                        <div class="portlet">
                            <div class="portlet-body">

                                <div class="row mx-0">
                                    @foreach( $schedules as $schedule )

                                        <div class="col-md-6">
                                            @php $batchInactive = ( $schedule->batch->status ?? '' ) == 0; @endphp
                                            <a title="{{ ($schedule->course->name ?? '').' : '. ( $schedule->batch->name ?? '' ) }}"
                                                class="w-100 col-md-12 px-3 py-2 my-1 border bg rounded-lg shadow-sm {{ $batchInactive == 1 ? '' : 'batch-schedule-id' }}" data-id="{{ $schedule->id }}"
                                                href="{{ $batchInactive == 1 ? 'javascript:void(0)' : url('new-schedule/'. ( $schedule->id ?? '' ).'/'.$schedule->doctor_course_id ) }}" data-batch_id="{{ $schedule->batch_id }}"
                                                data-doctor_id="{{ Auth::guard('doctor')->id() }}" data-status="{{ $schedule->batch->status }}" data-batch-id="{{ $schedule->batch_id }}" data-doctor-course-id="{{ $schedule->doctor_course_id }}" data-system-driven="{{ $schedule->batch->system_driven ?? '' }}" data-doctor-course-system-driven-changed="" data-doctor-course-system-driven="{{ $schedule->doctor_course_system_driven ?? '' }}"
                                            >
                                                <span class=" btn-info mb-2 btn btn-secondary"> Go To Schedule </span>
                                                <h6 class="bg" >{{ ( $schedule->course->name ?? '').' : '.($schedule->batch->name ?? '') }}</h6>

                                                @if(  $schedule->batch->expired_at  )
                                                <br>
                                                {{-- <span class="badge bg-info">Batch will be expired after, {{ $schedule->batch->expired_at  }} </span> --}}
                                                <span class="badge bg-info">Batch will be inactivated after: {{ \Carbon\Carbon::parse($schedule->batch->expired_at)->format('d M, Y') }} </span> . <br>
                                                <span class="badge bg-info">{{ $schedule->batch->expired_message }} </span>
                                                @endif
                                                {{-- @if( ( $schedule->batch->status ?? '' ) == 0 )
                                                    <span class="badge bg-danger">Batch Inactive</span>
                                                @endif --}}
                                            </a>
                                        </div>


                                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">{{ ($schedule->course->name ?? '') . ' : ' . ($schedule->batch->name ?? '') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal_body" style="padding: 20px;">
                                                    {!! $schedule->terms_and_condition !!}
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <a type="button" class="btn btn-primary" href="{{ ($schedule->batch->status ?? '') === 0 ? 'javascript:void(0)' : url('new-schedule/' . ($schedule->id ?? '').'/'.$schedule->doctor_course_id) }}">Open</a>
                                                </div>
                                            </div>
                                            </div>
                                        </div>



                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                @if($combined_schedule)
                <div class="my-3">
                    <div id="headerAddSection1">
                        <b>New Schedule</b> is Available now. <a class="button" href="{{ url('doctor-course-list-in-schedule') }}">Click Here</a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modal-id"
        aria-hidden="true">

            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-id">Please update your batch informations</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close">&times;</button>
                    </div>

                    <form id="modal-form" action="" method="POST">

                    </form>

                </div>
            </div>

        </div>

        <!-- Modal -->
        <div class="modal fade" id="system_driven" tabindex="-1" role="dialog" aria-labelledby="system_driven_header" aria-hidden="false">
            <div class="modal-dialog system_driven_dialog">
            <div class="modal-content">
                <!-- <div class="modal-header">
                <h5 class="modal-title" id="system_driven_header">Syestem Driven</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="false"></span>
                </button>
                </div>
                <div class="modal-body system_driven_body">
                ...
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div> -->
            </div>
            </div>
        </div>

    </div>


</div>
@endsection

@section('js')
    <script type="text/javascript">

        function get_terms_and_conditions(data)
        {
            if(data['status'])
                {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "GET",
                        url: '/discipline-terms-condition',
                        dataType: 'JSON',
                        data: {
                            batch_id: data['batch_id'],
                            doctor_id: data['doctor_id'],
                            schedule_id: data['schedule_id'],
                            doctor_course_id: data['doctor_course_id']
                        },
                        success: function(data) {


                            $('#modal-form').html(data.view);
                            $('#modal-id').html(data.title);
                            $('#myModal').modal('show');

                            console.log(data.isset_faculty_discipline);


                            console.log("ModalForm", $('#modal-form'))

                            $('#modal-form').on("submit", function(e) {

                                e.preventDefault();

                                if (!data.isset_faculty_discipline) {
                                    var doctor_course_id = $("[name='doctor_course_id']").val();
                                    var subject_id = $("[name='subject_id']").val();
                                    var candidate_type = $("[name='candidate_type']").val();
                                    var bcps_subject_id = $("[name='bcps_subject_id']").val();


                                    $.ajax({
                                        headers: {
                                            'X-CSRF-TOKEN': $(
                                                    'meta[name="csrf-token"]')
                                                .attr('content')
                                        },
                                        type: "POST",
                                        url: '/doctor-course-information-update',
                                        dataType: 'JSON',
                                        data: {
                                            doctor_course_id,
                                            subject_id,
                                            candidate_type,
                                            bcps_subject_id,
                                            schedule_id
                                        },
                                        success: function(updateData) {
                                            if (updateData.success) {
                                                $('#modal-form').html(updateData.terms_and_condition);
                                                $('#modal-id').html(updateData.title);
                                                data.isset_faculty_discipline = true;
                                                data.reload = 'new-schedule/' + schedule_id+'/'+doctor_course_id;
                                            }
                                        }
                                    });


                                } else {
                                    window.location = data.reload;
                                }
                            });



                        }
                    })
                }
        }

        var batch_id = "";
        var doctor_id = "";
        var schedule_id = "";
        var status = "";
        var system_driven = "";
        var doctor_course_id = "";
        var doctor_course_system_driven = "";
        var prev_ajax = {};

        $(document).ready(function() {


            $("body").on("click",".closed",function(){
                $('#system_driven').modal('hide');
                get_terms_and_conditions(prev_ajax);
            });

            $(document).on('change', '[name="system_driven"]', function(e) {
                e.preventDefault();
                var operation = "";
                if($(this).val() == "Yes") {
                    operation = "insert";
                }
                else if($(this).val() == "No")
                {
                    operation = "delete";
                }
                var batch_id = batch_id;
                var doctor_course_id = doctor_course_id;

                $.ajax({
                    type: "POST",
                    url: '/add-system-driven',
                    dataType: 'HTML',
                    data: {batch_id : batch_id,doctor_course_id:doctor_course_id, operation : operation, _token: '{{ csrf_token() }}' },
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.batch-schedule-id').attr("data-doctor-course-system-driven-changed",'changed');
                        if(data['success_status'] == "insert_success")
                        {
                            console.log("Insert Successfulley");
                        }
                        if(data['success_status'] == "delete_success")
                        {
                            console.log("Deleted Successfulley");
                        }
                        if(data['success_status'] == "insert_completed")
                        {
                            if($("[name='system_driven']:checked"))
                            $("[name='system_driven']:checked").removeAttr('checked');
                            alert(data['message']);
                        }
                        $( ".closed" ).prop( "disabled", false );

                    }
                });
            });

            $("body").on("click", ".batch-schedule-id", function(e) {
                e.preventDefault();
                batch_id = $(this).data('batch_id');
                doctor_id = $(this).data('doctor_id');
                schedule_id = $(this).data('id');
                status = $(this).data('status');
                system_driven = $(this).data('system-driven');
                doctor_course_id = $(this).data('doctor-course-id');
                doctor_course_system_driven = $(this).data('doctor-course-system-driven');
                doctor_course_system_driven_changed = $(this).data('doctor-course-system-driven-changed');
                prev_ajax = {batch_id:batch_id,doctor_id:doctor_id,schedule_id:schedule_id,status:status,doctor_course_id:doctor_course_id};
                if( system_driven == "Optional" )
                {
                    if(doctor_course_system_driven_changed == "" && doctor_course_system_driven=="")
                    {
                        $('#system_driven .modal-content').load('/system-driven',{batch_id : batch_id,doctor_course_id:doctor_course_id, _token: '{{ csrf_token() }}'},function(){
                            $('#system_driven').modal('show');
                        });
                    }
                    else
                    {
                        get_terms_and_conditions(prev_ajax);
                    }

                }
                else
                {
                    get_terms_and_conditions(prev_ajax);
                }


            });




        })
    </script>
@endsection
