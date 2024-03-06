@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            @if (isset($solve_class_id) && $solve_class_id)
                <div class="col-md-9 col-md-offset-0 px-0 d-block" style="margin-top: 40px">
                    <a href="{{ url('doctor-course-class/' . $solve_class_id . '/' . $doctor_course_id) }}"
                        class="btn btn-warning float-right" style="margin-left: 30px">
                        Solve Class
                    </a>
                </div>
            @endif

            <div class="col-md-9 col-md-offset-0 px-0">
                <div class="panel panel-default px-0">
                    <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;">
                        <h3>{{ $exam->name }}</h3>
                    </div>
                    <div class="panel-body px-0">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="col-md-12 px-0">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <table class="table table-bordered" style="table-layout: auto;">
                                        <tr>
                                            <th>Questions</th>
                                            <th>Your Answer</th>
                                            <th>Correct Answer</th>
                                            <th>Remark</th>
                                        </tr>
                                        @foreach ($exam->exam_questions as $exam_question)
                                            @php $i=0; @endphp
                                            <div id="question">
                                                @if (isset($exam_question->question->question_title))
                                                    <tr>
                                                        <td colspan="4">
                                                            <h4 class='modal-title' id='myModalLabel'>
                                                                {!! '(' .
    ($exam->exam_questions->search($exam_question) + 1) .
    ' of
                                                    ' .
    $exam->exam_questions->count() .
    ' )
                                                    ' .
    $exam_question->question->question_title !!}</h4>
                                                        </td>
                                                    </tr>
                                                    @if ($exam_question->question->type == '1')
                                                        @foreach ($exam_question->question->question_answers as $k => $answer)
                                                            @if ($k < session('stamp'))
                                                                @if ($given_answers[$exam_question->question->id][$answer->sl_no] == $answer->correct_ans)
                                                                    <tr>
                                                                        <td>
                                                                            {!! isset($answer->answer) ? $answer->answer : '' !!}
                                                                        </td>

                                                                        <td>
                                                                            <div class="radio"
                                                                                style="overflow:auto;width:30px;">
                                                                                <label><input type='radio'
                                                                                        name="{{ $exam_question->question->id . $answer->sl_no }}"
                                                                                        value='T'
                                                                                        {{ isset($given_answers[$exam_question->question->id]) &&$given_answers[$exam_question->question->id][$answer->sl_no] == 'T'? 'checked': '' }}>
                                                                                    T </label>
                                                                                <label><input type='radio'
                                                                                        name="{{ $exam_question->question->id . $answer->sl_no }}"
                                                                                        value='F'
                                                                                        {{ isset($given_answers[$exam_question->question->id]) &&$given_answers[$exam_question->question->id][$answer->sl_no] == 'F'? 'checked': '' }}>
                                                                                    F </label>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="radio"
                                                                                style="overflow:auto;width:30px;">
                                                                                <label><input type='radio'
                                                                                        name="{{ $exam_question->question->id . $answer->sl_no . $answer->sl_no }}"
                                                                                        value='T'
                                                                                        {{ $answer->correct_ans == 'T' ? 'checked' : '' }}>
                                                                                    T
                                                                                </label>
                                                                                <label><input type='radio'
                                                                                        name="{{ $exam_question->question->id . $answer->sl_no . $answer->sl_no }}"
                                                                                        value='F'
                                                                                        {{ $answer->correct_ans == 'F' ? 'checked' : '' }}>
                                                                                    F
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="radio"
                                                                                style="overflow:auto;">
                                                                                <label style="color:green;"><i
                                                                                        class="fa fa-check"
                                                                                        aria-hidden="true"></i></label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td>
                                                                            {!! isset($answer->answer) ? $answer->answer : '' !!}
                                                                        </td>

                                                                        <td>
                                                                            <div class="radio"
                                                                                style="overflow:auto;width:30px;">
                                                                                <label><input type='radio'
                                                                                        name="{{ $exam_question->question->id . $answer->sl_no }}"
                                                                                        value='T'
                                                                                        {{ isset($given_answers[$exam_question->question->id]) &&$given_answers[$exam_question->question->id][$answer->sl_no] == 'T'? 'checked': '' }}>
                                                                                    T </label>
                                                                                <label><input type='radio'
                                                                                        name="{{ $exam_question->question->id . $answer->sl_no }}"
                                                                                        value='F'
                                                                                        {{ isset($given_answers[$exam_question->question->id]) &&$given_answers[$exam_question->question->id][$answer->sl_no] == 'F'? 'checked': '' }}>
                                                                                    F </label>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="radio"
                                                                                style="overflow:auto;width:30px;">
                                                                                <label><input type='radio'
                                                                                        name="{{ $exam_question->question->id . $answer->sl_no . $answer->sl_no }}"
                                                                                        value='T'
                                                                                        {{ $answer->correct_ans == 'T' ? 'checked' : '' }}>
                                                                                    T
                                                                                </label>
                                                                                <label><input type='radio'
                                                                                        name="{{ $exam_question->question->id . $answer->sl_no . $answer->sl_no }}"
                                                                                        value='F'
                                                                                        {{ $answer->correct_ans == 'F' ? 'checked' : '' }}>
                                                                                    F
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="radio"
                                                                                style="overflow:auto;">
                                                                                @if ($given_answers[$exam_question->question->id][$answer->sl_no] != '.' && $given_answers[$exam_question->question->id][$answer->sl_no] != '')
                                                                                    <label style="color:red;"><i
                                                                                            class="fa fa-times"
                                                                                            aria-hidden="true"></i></label>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @foreach ($exam_question->question->question_answers as $k => $answer)
                                                            @if ($k < session('stamp'))
                                                                <tr>
                                                                    <td>
                                                                        {!! isset($answer->answer) ? $answer->answer : '' !!}
                                                                    </td>
                                                                    <td>
                                                                        <div class="radio"
                                                                            style="overflow:auto;width:30px;">
                                                                            <label>
                                                                                <input type='radio'
                                                                                    name='ans_sba{{ $exam_question->question->id }}1'
                                                                                    value='{{ $answer->sl_no }}1'
                                                                                    {{ $given_answers[$exam_question->question->id] == $answer->sl_no ? 'checked' : '' }}>
                                                                                {{ $answer->sl_no }}
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="radio"
                                                                            style="overflow:auto;width:30px;">
                                                                            <label>
                                                                                <input type='radio'
                                                                                    name='ans_sba{{ $exam_question->question->id }}2'
                                                                                    value='{{ $answer->sl_no }}2'
                                                                                    {{ $answer->correct_ans == $answer->sl_no ? 'checked' : '' }}>
                                                                                {{ $answer->sl_no }}
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    @if ($given_answers[$exam_question->question->id] == $answer->correct_ans && $i++ == 0)
                                                                        <td rowspan="5">
                                                                            <div class="radio"
                                                                                style="overflow:auto;">
                                                                                <label style="color:green;">
                                                                                    <i class="fa fa-check"
                                                                                        aria-hidden="true"></i>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    @elseif($given_answers[$exam_question->question->id] != $answer->correct_ans && $given_answers[$exam_question->question->id] != '.' && $given_answers[$exam_question->question->id] != '' && $i++ == 0)
                                                                        <td rowspan="5">
                                                                            <div class="radio"
                                                                                style="overflow:auto;">
                                                                                <label style="color:red;">
                                                                                    <i class="fa fa-times"
                                                                                        aria-hidden="true"></i>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    <tr>
                                                        <td colspan="4">
                                                            <button type="button"
                                                                style="background-color: #0e86ce;color: white;border-color:#0e86ce; border-radius: 5px; padding: 4px 8px;"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modal_batch_details_{{ $exam_question->question->id }}1">
                                                                Reference
                                                            </button>


                                                            <button type="button" data-bs-toggle="modal"
                                                                style="margin-left:10px;background-color: #3e8f3e;color: white;border-color:#3e8f3e; border-radius: 5px; padding: 4px 8px;"
                                                                data-bs-target="#discussion{{ $exam_question->question->id }}"
                                                                style="background-color: #0e86ce;color: white;border-color:#0e86ce;">
                                                                Discussion
                                                            </button>

                                                            @if (isset($exam_question->question->question_video_links))
                                                                @foreach ($exam_question->question->question_video_links as $item)
                                                                    @if ($item->video_link)
                                                                        <button
                                                                            style="margin-left:10px; background-color:#4c2491; color:white; border:#4c2491; border-radius: 5px; padding: 4px 8px;"
                                                                            class="btn-primary venobox"
                                                                            title="{{ 'Password: ' . $item->video_password }}"
                                                                            data-autoplay="true" data-gall="gallery01"
                                                                            data-vbtype="video"
                                                                            href="{{ $item->video_link }}">
                                                                            Video link
                                                                        </button>
                                                                    @endif
                                                                @endforeach
                                                            @endif

                                                            <!-- Modal -->
                                                            <div class="modal fade"
                                                                id="modal_batch_details_{{ $exam_question->question->id }}1"
                                                                data-bs-backdrop="static" data-bs-keyboard="false"
                                                                tabindex="-1" aria-labelledby="staticBackdropLabel"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            @if(!$exam_question->question->reference_books->count())
                                                                            <div>
                                                                                {!! $exam_question->question->reference !!}
                                                                            </div>
                                                                            @endif

                                                                            @foreach ($exam_question->question->reference_books as $reference_book)
                                                                                <a target="_blank"
                                                                                    href="{{ route('reference-book-detail', [$reference_book->reference_book_id, $reference_book->page_no]) }}"
                                                                                    style="cursor: pointer">
                                                                                    [Ref: {{ $reference_book->reference_book->name ?? '' }}/P-{{ $reference_book->page_no ?? '' }}]
                                                                                </a>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Modal Reference Book -->
                                                            <div class="modal fade"
                                                                id="modal_reference_book_details_{{ $$exam_question->question->reference_book->reference_book_id ?? '' }}"
                                                                data-bs-backdrop="static" data-bs-keyboard="false"
                                                                tabindex="1" aria-labelledby="staticBackdropLabel"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class='modal-header'>
                                                                                <h5> Book </h5>
                                                                            </div>
                                                                            {{ $exam_question->reference_book ?? '' }}

                                                                            @foreach ($exam_question->question->reference_books as $item)
                                                                                {{ $item->reference_book->reference_book_page->body ?? '' }}
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="modal fade"
                                                                id="discussion{{ $exam_question->question->id }}"
                                                                data-bs-backdrop="static" data-bs-keyboard="false"
                                                                tabindex="-1" aria-labelledby="staticBackdropLabel"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class='modal-header'>
                                                                                <h5> Discussion</h5>
                                                                            </div>
                                                                            <div>
                                                                                {!! $exam_question->question->question_title !!}
                                                                            </div>
                                                                            <div class='modal-body'>
                                                                                {!! $exam_question->question->discussion !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class='modal fade'
                                                                id='modal_batch_details_{{ $exam_question->question->id }}3'
                                                                tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
                                                                <div class='modal-dialog modal-dialog-centered'
                                                                    role='document'>
                                                                    <div class='modal-content'>

                                                                        <div class='modal-header'>
                                                                            Video Link : {!! $exam_question->question->question_title !!}
                                                                        </div>

                                                                        <div class="header text-center py-3">
                                                                            <h2 class="h2 brand_color">Video Password :
                                                                                {{ $exam_question->question->video_password ?? '' }}
                                                                            </h2>
                                                                        </div>

                                                                        <div class='modal-body'>
                                                                            @if ($exam_question->video_link)
                                                                                <a class="venobox"
                                                                                    href="{{ $exam_question->question->video_link }}">name</a>
                                                                            @endif

                                                                        </div>

                                                                        <div class='modal-footer'>
                                                                            <button type='button' class='btn btn-sm bg-red'
                                                                                data-dismiss='modal'>Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @endif
                                            </div>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @if (isset($solve_class_id) && $solve_class_id)
                <div class="col-md-9 col-md-offset-0 px-0 d-block" style=" margin-bottom: 50px">
                    <a href="{{ url('doctor-course-class/' . $solve_class_id . '/' . $doctor_course_id) }}"
                        class="btn btn-warning float-right" style="margin-left: 30px">
                        Solve Class
                    </a>
                </div>
            @endif

        </div>


    </div>
@endsection

@section('js')
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src=" {{ asset('js/venobox.min.js') }}"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.venobox').venobox();
            // $('*').click(function(e){e.preventDefault();});

        })
    </script>
@endsection
