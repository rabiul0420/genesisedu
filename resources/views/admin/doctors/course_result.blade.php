@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Doctor's Course Result</li>
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
                        <i class="fa fa-globe"></i>Doctor's Course Result</b>
                    </div>
                </div>

                <div class="portlet-body">

                    <div class="col-md-10">
                        <h4 style="color:Orange;">Course Registration No.: <b>{{ $course_reg_no->reg_no }}</b></h4>

                    </div>

                    <div class="col-md-12">
                        <hr>
                    </div>

                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Exam Name</th>
                            <th>Discipline</th>
                            <th>Session</th>
                            <th>Year</th>
                            <th>Correct Mark</th>
                            <th>Negative Mark</th>
                            <th>Obtain Mark</th>
                            <th>Wrong Ans.</th>
                            <th>Exam Highest</th>
                            <th>Discipline Position</th>
                            <th>Batch Position</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($results as $k=>$result)
                            <tr>
                                <td>{{ $k+1 }}</td>
                                <td>
                                    @php
                                        //Exam Name
                                        $temp_name = \App\Exam::select('*')->where('id', $result->exam_id)->get();
                                        foreach ($temp_name as $exam){
                                        echo $exam->name;
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        //Discipline Name
                                        $temp_name = \App\Subjects::select('*')->where('id', $result->subject_id)->get();
                                        foreach ($temp_name as $subject){
                                        echo $subject->name;
                                        }
                                    @endphp
                                </td>
                                <td>{{ $result->session }}</td>
                                <td>{{ $result->year }}</td>
                                <td>{{ $result->correct_mark }}</td>
                                <td>{{ $result->negative_mark }}</td>
                                <td>{{ $result->obtained_mark }}</td>
                                <td>{{ $result->wrong_answers }}</td>

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
                                    @php
                                        //Discipline wise Position
                                        $temp_name = \App\Result::select('*')->where('doctor_course_id', $course_id)->get();
                                        foreach ($temp_name as $p => $result_info){
                                            $result_info->exam_id;
                                            $result_info->subject_id;
                                        }
                                        $temp_name = \App\Result::select('*')->where('exam_id', $result_info->exam_id)->where('subject_id', $result_info->subject_id)->orderBy('obtained_mark', 'desc')->get();
                                        foreach ($temp_name as $p => $exam_result){
                                            $position = $p+1;
                                            if ($course_id == $exam_result->doctor_course_id){
                                                $my_position = $position;
                                                $th = ($position==1)?'st':(($position==2)?'nd':(($position==3)?'rd':'th'));
                                            }
                                        }
                                        echo $my_position.$th;
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        //Batch wise Position
                                        $temp_name = \App\Result::select('*')->where('exam_id', $result_info->exam_id)->where('batch_id', $result_info->batch_id)->orderBy('obtained_mark', 'desc')->get();
                                        foreach ($temp_name as $p => $exam_result){
                                            $position = $p+1;
                                            if ($course_id == $exam_result->doctor_course_id){
                                                $my_position = $position;
                                                $th = ($position==1)?'st':(($position==2)?'nd':(($position==3)?'rd':'th'));
                                            }
                                        }
                                        echo $my_position.$th;
                                    @endphp
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

    <script type="text/javascript">

        $(document).ready(function() {
               ////////////
        })
    </script>

@endsection
