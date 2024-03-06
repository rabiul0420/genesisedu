@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

  {{--        @include('side_bar')--}}

        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Course Result</h3></div>

                <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="col-md-12 col-md-offset-0" style="">
                            <h4 style="color: orange;">Course Registration no. : <b>{{ $course_reg_no->reg_no }} </b></h4>
                            <hr>
                        </div>
                            @php $institute = (isset($course_reg_no->institute->name))?$course_reg_no->institute->name:'' @endphp

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Exam Name</th>
                                            <th>Discipline</th>
                                            <th>Batch</th>
                                            @if($institute=='BSMMU')
{{--                                                <th>Candidate</th>--}}
                                                <th>Candidate Type</th>
                                            @endif
                                            <th>Session</th>
                                            <th>Year</th>
                                            <th>Obtained Mark</th>
                                            <th>Exam Highest</th>
                                            <th>Overall Position</th>
                                            <th>Discipline Position</th>
                                            <th>Batch Position</th>
                                            @if($institute=='BSMMU' || $institute == "Combined (BSMMU+BCPS)")
                                                <th>Candidate Position</th>
                                            @endif
                                            @if($institute=='BCPS')
                                                <th>Pass/Fail</th>
                                            @endif
                                            <th>Wrong Ans</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($results as $index => $result)
                                            <tr>
                                                <td>{{ $result->exam_id }}</td>
                                                <td>{{ (isset($result->exam->name))?$result->exam->name:'' }}</td>
                                                <td>{{ (isset($result->subject->name))?$result->subject->name:'' }}</td>
                                                <td>{{ (isset($result->batch->name))?$result->batch->name:'' }}</td>
                                                @if($institute=='BSMMU')
{{--                                                    <td>--}}
{{--                                                        {{ (($result->candidate_code =='A')?'Govt':(($result->candidate_code=='B')?'Private':(($result->candidate_code=='C')?'BSMMU':''))) }}--}}
{{--                                                    </td>--}}
                                                    <td>
                                                        {{ $result->doctor_course->candidate_type ?? '' }}
                                                    </td>
                                                @endif
                                                <td>{{ (isset($result->exam->sessions->name))?$result->exam->sessions->name:'' }}</td>
                                                <td>{{ (isset($result->exam->year))?$result->exam->year:'' }}</td>
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
                                                <td>{{ $result->overallPosition() }}</td>
                                                <td>{{ $result->getDisciplinePosition() }}</td>
                                                <td>{{ $result->getBatchPosition() }} </td>
                                                <td>{{ $result->getCandidatePosition() }}</td>  
                                                <td>{{ $result->wrong_answers }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection
