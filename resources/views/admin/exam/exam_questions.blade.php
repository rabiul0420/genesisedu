@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Exam Questions</li>
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
                        <i class="fa fa-globe"></i> Exam : {{ $exam->name }}<br> 
                        
                    </div>
                </div>
                <div class="portlet-body">

                    <div>
                        @if($exam->question_type->mcq_number > 0 && $mcqs < $exam->question_type->mcq_number )
                            <a href="{{ url('admin/add-exam-questions/'.$exam->id.'/1') }}" class="btn btn-xs btn-primary">Add MCQ</a>
                        @endif
                        @if($exam->question_type->sba_number > 0 && $sbas < $exam->question_type->sba_number)
                        <a href="{{ url('admin/add-exam-questions/'.$exam->id.'/2') }}" class="btn btn-xs btn-info">Add SBA</a>
                        @endif
                        @if($exam->question_type->mcq2_number > 0 && $mcq2s < $exam->question_type->mcq2_number)
                        <a href="{{ url('admin/add-exam-questions/'.$exam->id.'/3') }}" class="btn btn-xs btn-primary">Add MCQ2</a>
                        @endif
                        @if($exam->exam_questions()->count() == ( $exam->question_type->mcq_number + $exam->question_type->sba_number + $exam->question_type->mcq2_number ))
                        <a class="print-button btn btn-xs btn-success" href="{{ url('admin/print-exam/'.$exam->id) }}" ><i class="fa fa-print"></i> PRINT</a>
                        @endif
                        <br><br>
                    </div>
                    <div>
                        <table id="table_1" class="table table-striped table-bordered table-hover datatable">
                            <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Question ID</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if(isset($exam->exam_questions))
                                @foreach($exam->exam_questions as $k=>$question)
                                <tr>
                                    <td>{{ $k+1 }}</td>
                                    <td>
                                        <a target="_blank" href="{{ $question->question->path_url ?? '' }}/edit" class="text-info" 
                                            onclick="
                                                this.nextElementSibling.style.display = '';
                                                this.style.display = 'none';
                                            "
                                        >
                                            {{ $question->question->id }}
                                        </a>
                                        <a style="display: none;" class="print-button btn btn-xs btn-info" href="{{ url()->current() }}" >
                                            &#x21bb; Refresh
                                        </a>
                                    </td>
                                    <td style="position: relative;" class="text-left">
                                        <div
                                            onmouseover="this.nextElementSibling.style.display = ''"
                                            onmouseout="this.nextElementSibling.style.display = 'none'"
                                        >
                                            {!! $question->question->question_title !!}
                                        </div>
                                        <div style="display: none; position: absolute; top: 80%; left: 0%; background: #666; color: #fff; border-radius: 10px; z-index: 900;padding: 10px; text-align: left;">
                                            @foreach ($question->question->question_answers as $option)
                                            <div>{!! $option->answer ?? '' !!}</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>{{ ($question->question->type == "1") ? "MCQ" : ($question->question->type == "2" ? "SBA" : "")  }}</td>
                                    <td><a href="{{ url('admin/edit-exam-question/'.$question->id) }}" class="btn btn-xs btn-primary">Replace</a>
                                    {!! Form::open(['url' => url('admin/delete-exam-question/'.$question->id), 'style' => 'display:inline', 'method' => 'GET']) !!}
                                        <button onclick="return confirm('Are you Sure to delete? If you delete question serial will not be maintained !!!')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!}
                                </tr>
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

    <!-- Modal -->
    <div class="modal fade" id="question_answer" tabindex="-1" aria-labelledby="question_answer_header" aria-hidden="true">
        <div class="modal-dialog question_answer_dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="question_answer_header">Question Stamps</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body question_answer_body">
            ...
            </div>
            <!-- <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div> -->
        </div>
        </div>
    </div>


@endsection

@section('js')

<script>
$("#delete").click(function(){
    if(confirm("Are you sure you want to delete this?")){
        $(this).attr("href", "query.php?ACTION=delete&ID='1'");
    }
    else{
        return false;
    }
});
</script>

@endsection