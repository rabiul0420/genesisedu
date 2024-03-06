@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'">'.$value.'</a> </li>';
            }
            ?>
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
                        <i class="fa fa-globe"></i><?php echo $module_name;?> List
                        @can('Question Source')
                        <a href="{{url('admin/question-reference-exam/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Faculty/Discipline</th>
                            <th>Session</th>
                            <th>Year</th>
                            <th>Exam Type</th>
                            <th>Reference Code</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($question_reference_exams as $question_reference_exam)
                            <tr>
                                <td>{{ $question_reference_exam->id }}</td>
                                <td>{{ isset($question_reference_exam->institute->name)?$question_reference_exam->institute->name:'' }}</td>
                                <td>{{ isset($question_reference_exam->course->name)?$question_reference_exam->course->name:'' }}</td>

                                @if(isset($question_reference_exam->course->type) && $question_reference_exam->course->type == 1)
                                    <td>{{ isset($question_reference_exam->faculty->name)?$question_reference_exam->faculty->name:'' }}</td>
                                @else
                                    <td>{{ isset($question_reference_exam->subject->name)?$question_reference_exam->subject->name:'' }}</td>
                                @endif
                                <td>{{ isset($question_reference_exam->session->name)?$question_reference_exam->session->name:'' }}</td>
                                <td>{{ $question_reference_exam->year }}</td>
                                <td>{{ isset($question_reference_exam->exam_type->name)?$question_reference_exam->exam_type->name:'' }}</td>
                                <td>{{ $question_reference_exam->reference_code }}</td>
                                <td>{{ ($question_reference_exam->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    @can('Question Source')
                                    <a href="{{ url('admin/question-reference-exam/'.$question_reference_exam->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan
                                    @can('Question Source')
                                    {!! Form::open(array('route' => array('question-reference-exam.destroy', $question_reference_exam->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!}
                                    @endcan
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                "columnDefs": [
                    { "searchable": false, "targets": 5 },
                    { "orderable": false, "targets": 5 }
                ]
            })
        })
    </script>

@endsection
