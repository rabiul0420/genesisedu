@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>{{ $topic->name }}</h3></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif





                        <div class="form-group">
                            <input type="hidden" name="lecture_sheet_batch_id" value="{{ $lecture_sheet_article_batch_id }}">
                            <div class="col-md-12">
                                <div class="input-icon right">
                                    @php  $lecture_sheet_article_topics->prepend('Select Class/Chapter', ''); @endphp
                                    {!! Form::select('topic_id',$lecture_sheet_article_topics, $topic->id ,['class'=>'form-control','required'=>'required','id'=>'topic_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <h4><b>Lecture Sheets</b></h4>

                                        <div class="row">
                                            @foreach($lecture_sheet_article_batch as $lecture_sheet_article)
                                                <div class="col-md-12">
                                                    <h2><a href="{{ url('lecture-sheet-article-details/'.$lecture_sheet_article->id ) }}">{{ $lecture_sheet_article->title }}</a></h2>
                                                    <div>{!! substr(strip_tags($lecture_sheet_article->description),0,100) !!}<br>
                                                        <a href="{{ url('lecture-sheet-article-details/'.$lecture_sheet_article->id ) }}">Continue reading...</a></div>
                                                </div>
                                            @endforeach

                                        </div>
                                </div>



                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="text-center">
                                            {{ $lecture_sheet_article_batch->links() }}
                                        </div>
                                    </div>
                                </div>

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
                var lecture_sheet_article_batch_id = $("[name='lecture_sheet_article_batch_id']").val();
                var topic_id = $(this).val();
                window.location.href = "{{ url('topic-lecture-sheet-articles' ) }}"+"/"+lecture_sheet_article_batch_id+"/"+topic_id;
            })

            $(".navbar-inverse .navbar-nav > li > a").css({'color':'#fff','background-color':'#92B7FE'});
            $(".navbar-inverse .navbar-nav > li > a").hover(function(){
                $(this).css({'color':'#fff','background-color':'#0006b1'});
                }, function(){
                $(this).css({'color':'#fff','background-color':'#92B7FE'});
            });

            $(".navbar-inverse .navbar-nav>.active>a, .navbar-inverse .navbar-nav>.active>a:focus, .navbar-inverse .navbar-nav>.active>a:hover ").css({'color':'#fff','background-color':'#0006b1'});

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
