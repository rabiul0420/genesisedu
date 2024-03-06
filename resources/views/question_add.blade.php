@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Add Question</h3></div>

                <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        

 
        
            
                        <div class="col-md-12 col-md-offset-0" style="">
                            <hr><h4><b>My Courses</b></h4>
                        </div>

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Video List</th>
                                            <th>Batch</th>
                                            <!-- <th>Reg. No.</th> -->
                                            <th>Year</th>
                                            <th>Session</th>
                                            <th>Course</th>
                                            <th>Discipline</th>
                                            
                                            
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($doc_info->doctorcourses as $k=>$value)
                                            <?php
                                                if (isset($value->batch->id)){
                                                    $temp_name = \App\LectureVideoBatch::select('*')
                                                    ->where('year', $value['year'])
                                                    ->where('session_id', $value->session->id ?? '')
                                                    ->where('course_id', $value->course->id ?? '')
                                                    ->where('batch_id', $value->batch->id ?? '')
                                                    ->get();
                                                    if ($temp_name) {
                                                        foreach ($temp_name as $key => $data_show) {
                                                            if ($data_show->batch_id==$value->batch->id) {      
                                                                ?>
                                                                <tr>
                                                                    <td>{{ $k+1 }}</td>
                                                                    {{-- <!-- <td>{{ $value['reg_no'] }}</td> --> --}}
                                                                    <td>
                                                                        {!! Form::open(['url'=>['question-submit'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}

                                                                            <input type="hidden" name="doctor_course_id" value="{{ $value['id'] }}">
                                                                            <?php
                                                                                if (isset($value->batch->id)){
                                                                                    $temp_name = \App\LectureVideoBatch::select('*')
                                                                                    ->where('year', $value['year'])
                                                                                    ->where('session_id', $value->session->id)
                                                                                    ->where('course_id', $value->course->id)
                                                                                    ->where('batch_id', $value->batch->id)
                                                                                    ->get();
                                                                                    foreach ($temp_name as $key => $value) {
                                                                                        //echo $value->year.'/'.$value->session_id.'/'.$value->course_id.'/'.$value->batch_id.'-'.$value->id;
                                                                                        echo "<select name='lecture_video_id' class='form-control' id='lecture_video_id' required='required' onchange='goToPage(this.value)'>";
                                                                                        echo "<option value=''>Select Video (class)</option>";
                                                                                        $temp_name = \App\Lecture_video_batch_lecture_video::select('*')->where('lecture_video_batch_id', $value->id)->get();
                                                                                            foreach ($temp_name as $lecture_video_ids){
                                                                                              $lecture_video_name =  isset($lecture_video_ids->lecture_video->name)?$lecture_video_ids->lecture_video->name:'';
                                                                                              if($lecture_video_name){
                                                                                                echo "<option value='{$lecture_video_ids->lecture_video_id}'>{$lecture_video_ids->lecture_video->name}</option>";
                                                                                              }
                                                                                            }
                                                                                        echo "</select>";
                                                                                        //echo "<input type='submit' value='Submit'>";
                                                                                    }
                                                                                }
                                                                            ?>

                                                                        {!! Form::close() !!}

                                                                    </td>
                                                                    <td>{{ (isset($value->batch->name))?$value->batch->name:'' }}</td>
                                                                    <td>{{ $value['year'] }}</td>
                                                                    <td>{{ (isset($value->session->name))?$value->session->name:'' }}</td>
                                                                    <td>{{ (isset($value->course->name))?$value->course->name:'' }}</td>
                                                                    <td>{{ (isset($value->subject->name))?$value->subject->name:'' }}</td>


                                                                </tr>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                }
                                            ?>
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


@section('js')

    <script type="text/javascript">
        $(document).ready(function() {

            $("body").on( "change", "[name='lecture_video_id']", function() {
                //window.location.href = "question-submit";
                this.form.submit();
            })

        })
    </script>
    

@endsection
