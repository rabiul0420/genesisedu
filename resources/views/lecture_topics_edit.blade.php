@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Lecture Sheet</h3></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <h4><b>Class/Chapter lecture sheets</b></h4>
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">Select Class/Chapter (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-11">

                                                @php  $topics->prepend('Select Class/Chapter', ''); @endphp
                                                {!! Form::select('topic_id',$topics, old('topic_id') ? old('topic_id') : '' ,['class'=>'form-control','required'=>'required','id'=>'topic_id']) !!}<i></i>
                                                <input type="hidden" name="year" value="{{ $doctor_course_info->year }}">
                                                <input type="hidden" name="session_id" value="{{ $doctor_course_info->session_id }}">
                                                <input type="hidden" name="institute_id" value="{{ $doctor_course_info->institute_id }}">
                                                <input type="hidden" name="course_id" value="{{ $doctor_course_info->course_id }}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="lecture_sheets">
                                        </div>
                                    </div>

                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination justify-content-end">
                                            <li class="page-item disabled">
                                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                                            </li>
                                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#">Next</a>
                                            </li>
                                        </ul>
                                    </nav>

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

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $("body").on( "change", "[name='topic_id']", function() {
                var topic_id = $(this).val();
                var year = $('[name="year"]').val();
                var session_id = $('[name="session_id"]').val();
                var institute_id = $('[name="institute_id"]').val();
                var course_id = $('[name="course_id"]').val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    //url: '/admin/courses-faculties',
                    //url: '/admin/'+$("[name='url']").val(),
                    //url: '/admin/courses-faculties-batches',
                    url: '/topic-lecture-sheets',
                    dataType: 'HTML',
                    data: {year:year,session_id:session_id,institute_id:institute_id,course_id: course_id,topic_id:topic_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.lecture_sheets').html('');
                        $('.lecture_sheets').html(data['lecture_sheets']);
                        
                    }
                });

            })
            

        })
    </script>


@endsection
