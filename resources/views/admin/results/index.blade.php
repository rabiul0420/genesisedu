@extends('admin.layouts.app')

@section('content')
    @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif



    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="portlet" style="max-width: max-content;">
        <div class="portlet-title">
            <div class="caption"
                style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div>
                    <i class="fa fa-globe"></i>
                    <b>{{ $batch->name ?? '' }}</b>
                </div>
                <div>
                    <a href="{{ route('admin.results.excel.download', $batch->id) }}" class="btn btn-info btn-xs">Excel
                        Download</a>
                </div>
            </div>
        </div>

        <div class="portlet-body">
            <table id="resultTable" class="table table-striped table-bordered table-hover datatable"
                style="min-width: max-content;">
                <thead style="position: sticky; top:0;">
                    <tr>
                        <th style="position: sticky; left: 0; background: #fff; vertical-align: bottom; z-index: 900; padding: 0;"
                            rowspan="2">
                            <table class="table table-striped table-bordered table-hover" style="margin: 0;">
                                <tr>
                                    <td rowspan="3" class="text-left text-info">
                                        <div>FM = Full Mark</div>
                                        <div>PM = Pass Mark</div>
                                        <div>HM = Highest Mark</div>
                                        <div>OM = Obtained Mark</div>
                                        <div>MP = Merit Position</div>
                                        <div>WA = Wrong Answer</div>
                                    </td>
                                    <td
                                        style="width: 40px; height: 150px; writing-mode: vertical-rl; vertical-align: middle; word-break: break-all !important; white-space: normal !important;">
                                        <div style="transform: rotate(180deg);" class="text-danger text-left">Exams</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 40px;" title="Full Mark">FM</td>
                                </tr>
                                <tr>
                                    <td style="width: 40px;" title="Pass Mark">PM</td>
                                </tr>
                                <tr>
                                    <td class="text-left">
                                        <div class="text-danger text-left">
                                            Doctor Info
                                        </div>
                                    </td>
                                    <td style="width: 40px;" title="Highest Mark">HM</td>
                                </tr>
                            </table>
                        </th>
                        @foreach ($exams as $exam)
                            <th
                                style="height: 150px; writing-mode: vertical-rl; vertical-align: middle; word-break: break-all !important; white-space: normal !important;">
                                <div style="transform: rotate(180deg);">{{ $exam->name }}</div>
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($exams as $exam)
                            <td class="text-left" style="padding: 0;">
                                <table class="table table-striped table-bordered table-hover" style="margin: 0;">
                                    <tr>
                                        <th>{{ $exam->question_type->full_mark ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ $exam->question_type->pass_mark ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ $exam->highest ?? '' }}</th>
                                    </tr>
                                </table>
                            </td>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($doctor_courses as $doctor_course)
                        <tr>
                            <td
                                style="position: sticky; left: 0; background: #fff; text-align: left; z-index: 900; padding: 0;">
                                <table class="table table-striped table-bordered table-hover" style="margin: 0;">
                                    <tr>
                                        <td rowspan="3" class="text-left">
                                            <div><b>{{ $doctor_course->doctor->name ?? '' }}</b></div>
                                            <div>Reg.: <b>{{ $doctor_course->reg_no ?? '' }}</b></div>
                                            <div>BMDC: <b>{{ $doctor_course->doctor->bmdc_no ?? '' }}</b></div>
                                            <div>Phone: <b>{{ $doctor_course->doctor->phone ?? '' }}</b></div>
                                        </td>
                                        <td style="width: 40px;" title="Obtained Mark">OM</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40px;" title="Merit Position">MP</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40px;" title="Wrong Answer">WA</td>
                                    </tr>
                                </table>
                            </td>
                            @foreach ($exams as $exam)
                                @if ($result = $data[$exam->id][$doctor_course->id] ?? false)
                                    <td class="text-left" style="padding: 0;">
                                        <table class="table table-striped table-bordered table-hover" style="margin: 0;">
                                            <tr>
                                                <td>{{ $result->obtained_mark ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ $result->position ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ $result->wrong_answers ?? '' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                @else
                                    <td>
                                        <div class="text-danger text-center">
                                            A
                                        </div>
                                        <span class="sms_container_{{ $batch->id }}_{{ $exam->id }}_ sms_container_{{ $batch->id }}_{{ $exam->id }}_{{ $doctor_course->doctor_id }}">
                                            @if(in_array("sms_container_{$batch->id}_{$exam->id}_{$doctor_course->doctor_id}", $identifiers))
                                            <i class="fa fa-envelope text-success"></i>
                                            @else
                                            <i onclick="sendSMS({{ $batch->id }}, {{ $exam->id }}, {{ $doctor_course->doctor_id }})" class="fa fa-envelope" style="cursor: pointer"></i>
                                            @endif
                                        </span>
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function sendSMS(batchId, examId, doctorId = '') {
            document.querySelectorAll(`.sms_container_${batchId}_${examId}_${doctorId}`).forEach(container => {
                container.innerHTML = '';
            });

            axios.get(`/admin/send-sms-for-absent-in-exam/${batchId}/${examId}/${doctorId}`)
                .then(function (response) {
                    document.querySelectorAll(`.sms_container_${batchId}_${examId}_${doctorId}`).forEach(container => {
                        container.innerHTML = `<i class="fa fa-envelope text-success"></i>`;
                    });
                })
                .catch(function (error) {
                    console.log(error);
                })
        }


        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                "columnDefs": []
            })
        })
    </script>
@endsection
