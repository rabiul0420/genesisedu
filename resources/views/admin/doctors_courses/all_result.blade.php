@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Doctors All Result</li>
        </ul>
    </div>
 
    <div class="col-md-12 col-md-offset-0">
        <div class="panel panel-default">
            <div class="panel-heading" style="background-color:#35363A; color: #f9f9f9;"><h3>Doctors All Result</h3></div>

            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="col-md-12 col-md-offset-0" style="">
                    <div class="row">
                        <button type="text" id="print" class="btn btn-info">Print</button>
                        <div class="col-md-4">
                            <h4 style="color: orange;">Doctor Name. : <b>{{ $doctor_course->doctor->name ?? '' }} </b></h4>
                            <h4 style="color: orange;">Phone. : <b>{{ $doctor_course->doctor->mobile_number ?? '' }} </b></h4>
                            <h4 style="color: orange;">BMDC. : <b>{{ $doctor_course->doctor->bmdc_no ?? '' }} </b></h4>
                            <h4 style="color: orange;">Course Registration no. : <b>{{ $doctor_course->reg_no }} </b></h4>
                        </div>
                        @php 
                            $institute = $doctor_course->institute->name ?? '';
                        @endphp
                        <div class="col-md-4">
                            <h4 style="color: orange;">Discipline. :
                                <b>{{ (isset($results[0]->subject->name))?$results[0]->subject->name:'' }}</b>
                            </h4>
                            <h4 style="color: orange;">Batch. :
                                <b>{{ (isset($results[0]->batch->name))?$results[0]->batch->name:'' }}</b>
                            </h4>
                            <h4 style="color: orange;">Session. : 
                                <b>{{ (isset($results[0]->exam->sessions->name))?$results[0]->exam->sessions->name:'' }}</b>
                            </h4>
                            <h4 style="color: orange;">Year. :
                                <b>{{ (isset($results[0]->exam->year))?$results[0]->exam->year:'' }}</b>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Exam Name</th>
                                <th>Exam Date</th>
                                <th>Obtained Mark</th>
                                <th>Exam Highest</th>
                                <th>Overall Position</th>
                                <th>Discipline Position</th>
                                <th>Batch Position</th>
                                @if($institute=='BSMMU')
                                    <th>Candidate Position</th>
                                @endif
                                @if($institute=='BCPS')
                                    <!-- <th>Pass/Fail</th> -->
                                @endif
                                <th>Wrong Ans</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $index => $result)
                                <tr>
                                    <td>{{ $result->exam_id }}</td>
                                    <td>{{ (isset($result->exam->name))?$result->exam->name:'' }}</td>
                                    <td>{{ (isset($result->exam->created_at))?$result->exam->created_at->format('d M Y'):'' }}</td>
                                    <td>{{ $result->obtained_mark }}</td>
                                    <td>
                                        @php
                                            //Exam Highest mark
                                            $temp_name = \App\Result::select('*')->where('exam_id', $result->exam_id)->orderBy('obtained_mark', 'desc')->take(1)->get();
                                            foreach ($temp_name as $exam_top){
                                            echo $exam_top->obtained_mark;
                                            }
                                        @endphp
                                    </td>        
                                    <td>
                                        {{ $result->overallPosition() }}
                                    </td>
                                    <td>
                                        {{ $result->getDisciplinePosition() }}
                                    </td>
                                    <td>
                                        {{ $result->getBatchPosition() }}
                                    </td>
                                    @if($institute=='BSMMU')
                                    <td>
                                        {{ $result->getCandidatePosition() }}
                                    </td>
                                    @endif
                                    <td>
                                        {{ $result->wrong_answers }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        document.getElementById('print').onclick = () => {
            const doctorCourseId = `{{ $doctor_course->id }}`;

            const url = `/admin/view-course-result/${doctorCourseId}/print`;

            const pw = window.open(url, '_blank', "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1200,height=800" );

            pw.print();
        }

        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                processing: true,
                "pageLength": 50,
            })
        });
    </script>

@endsection