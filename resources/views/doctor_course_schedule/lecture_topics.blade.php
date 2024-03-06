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

        .page-breadcrumb>li {
            display: inline-block;
        }

        .page-breadcrumb>li>a,
        .page-breadcrumb>li>span {
            color: #666;
            font-size: 14px;
            text-shadow: none;
        }

        .page-breadcrumb>li>i {
            color: #999;
            font-size: 14px;
            text-shadow: none;
        }

        .page-breadcrumb>li>i[class^="icon-"],
        .page-breadcrumb>li>i[class*="icon-"] {
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


            {{-- $updated_schedules --}}

            <div class="col-md-9">
                <div class="panel panel-default pt-2">
                    <div class="panel_box w-100 bg-white rounded shadow-sm">
                        <div class="header text-center py-3">
                            <h2 class="h2 brand_color">{{ 'My Schedule' }}</h2>
                        </div>
                    </div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        <div class="col-md-12 p-0">
                            <div class="portlet">
                                <div class="portlet-body">
                                    @if (Request::segment(1) == 'doctor-course-batch-schedule')
                                        @if (isset($batch_schedule_batch))
                                            <div class="row mx-0">
                                                <div class="col-md-12 px-0 py-2">
                                                    <table
                                                        class="bg-white table text-center table-striped table-bordered rounded py-1 table-hover datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>SL</th>
                                                                <th>Batch Name</th>
                                                                <th>Schedule Name</th>
                                                                <th>Course</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            @foreach ($batch_schedule_batch as $k => $value)
                                                                @if (isset($value->id))

                                                                    <tr>
                                                                        @php $batchInactive = isset( $doctor_course->batch->status ) && $doctor_course->batch->status == 0  @endphp
                                                                        <td>{{ $k + 1 }}</td>
                                                                        <td>
                                                                            {{ $value->batch->name }}
                                                                            @if ($batchInactive) <span class="badge bg-danger">Batch Inactive</span> @endif
                                                                        </td>
                                                                        <td>{{ $value->name }}</td>
                                                                        <td>{{ $value->course->name }}</td>
                                                                        <td>

                                                                            <a href="{{ $batchInactive ? 'javascript:void()' : url('doc-profile/print-batch-schedule/' . $value->id) }}"
                                                                                class="btn btn-sm btn-primary {{ $batchInactive ? 'disabled' : '' }}"
                                                                                target="_blank">View Schedule
                                                                            </a>


                                                                            @if ((request()->server('HTTP_HOST') == 'www.genesisedu.info' || request()->server('HTTP_HOST') == 'genesisedu.info') === false)

                                                                                <a href="{{ $batchInactive ? 'javascript:void()' : url('new-schedule/' . $value->id) }}"
                                                                                    class="btn btn-sm btn-primary {{ $batchInactive ? 'disabled' : '' }}"
                                                                                    target="_blank">New Schedule
                                                                                </a>

                                                                            @endif


                                                                        </td>
                                                                    </tr>
                                                                @endif

                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                                    <div class="text-center">
                                                        {{ $batch_schedule_batch->links() }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @elseif( Request::segment(1)=='schedule' )
                                        <div class="row mx-0">
                                            @foreach ($doctor_courses as $doctor_course)
                                                <div class="col-md-6">
                                                    <a title="{{ ($doctor_course->course->name ?? '') . ' : ' . ($doctor_course->batch->name ?? '') }}"
                                                        class="w-100 col-md-12 px-3 py-4 my-1 border bg rounded-lg shadow-sm "
                                                        href="{{ isset($doctor_course->batch->status) && $doctor_course->batch->status == 0 ? 'javascript:void(0)' : url('doctor-course-batch-schedule/' . $doctor_course->id) }}">

                                                        <h6 class="bg">
                                                            {{ ($doctor_course->course->name ?? '') . ' : ' . ($doctor_course->batch->name ?? '') }}
                                                        </h6>

                                                        @if (isset($doctor_course->batch->status) && $doctor_course->batch->status == 0)
                                                            <span class="badge bg-danger">Batch Inactive</span>
                                                        @endif
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                @if (Request::segment(1) == 'schedule')
                    <div class="panel panel-default pt-2">
                        <div class="panel_box w-100 bg-white rounded shadow-sm">
                            <div class="header text-center py-3">
                                <h2 class="h2 brand_color">{{ 'Updated Schedule' }}</h2>
                            </div>
                        </div>
                        @if (Session::has('message'))
                            <div class="alert {{ Session::get('class') ? Session::get('class') : 'alert-success' }}"
                                role="alert">
                                <p> {{ Session::get('message') }}</p>
                            </div>
                        @endif
                        <div class="panel-body">

                            <div class="col-md-12 p-0">
                                <div class="portlet">
                                    <div class="portlet-body">

                                        <div class="row mx-0">
                                            @foreach ($updated_schedules as $schedule)

                                                <div class="col-md-6">

                                                    <a title="{{ ($schedule->course->name ?? '') . ' : ' . ($schedule->batch->name ?? '') }}"

                                                        class="w-100 col-md-12 px-3 py-4 my-1 border bg rounded-lg shadow-sm batch-schedule-id"
                                                        data-id="{{ $schedule->id }}"
                                                        href="{{ ($schedule->batch->status ?? '') == 0 ? 'javascript:void(0)' : url('new-schedule/' . ($schedule->id ?? '')) }}" 
                                                        data-batch_id="{{ $schedule->batch_id }}"
                                                        data-doctor_id="{{ Auth::guard('doctor')->id() }}"
                                                        data-status="{{ $schedule->batch->status }}"
                                                        >

                                                        <h6 class="bg">
                                                            {{ ($schedule->course->name ?? '') . ' : ' . ($schedule->batch->name ?? '') }}
                                                        </h6>

                                                        @if (($schedule->batch->status ?? '') == 0)
                                                            <span class="badge bg-danger">Batch Inactive</span>
                                                        @endif
                                                    </a>
                                                </div>

                                                <!-- Modal -->
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
                                                        <a type="button" class="btn btn-primary" href="{{ ($schedule->batch->status ?? '') === 0 ? 'javascript:void(0)' : url('new-schedule/' . ($schedule->id ?? '')) }}">Open</a>
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

                @endif

            </div>


        </div>


    </div>


@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {

            $("body").on("click", ".batch-schedule-id", function(e) {
                e.preventDefault();
                var batch_id = $(this).data('batch_id');
                var doctor_id = $(this).data('doctor_id');
                var schedule_id = $(this).data('id');
                var status = $(this).data('status');
                if(status)
                {
                    $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "GET",
                    url: '/discipline-terms-condition',
                    dataType: 'JSON',
                    data: {
                        batch_id: batch_id,
                        doctor_id: doctor_id,
                        schedule_id: schedule_id
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
                                var doctor_course_id = $("[name='doctor_course_id']")
                                    .val();
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
                                            data.reload = 'new-schedule/' + schedule_id
                                        }
                                    }
                                });


                            } else {
                                window.location = data.reload;
                            }
                        });



                    }
                });

                }

                
            });




        })
    </script>
@endsection
