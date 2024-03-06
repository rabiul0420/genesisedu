@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Lecture Sheets</h3></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                     {{--   <div>
                            
                            <nav class="navbar navbar-inverse" style="background-color: rgb(127, 201, 246); color: rgb(255, 255, 255);">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                    <a class="navbar-brand" style="color:white;"> </a>
                                    </div>
                                    <div class="collapse navbar-collapse" id="myNavbar"  style="background-color: rgb(127, 201, 246); color: rgb(255, 255, 255);"> 
                                    <ul class="nav navbar-nav">
                                        @foreach($topics as $topic)
                                            <li class="{{ ($topic->id == $topic_id)?'active':'' }}"><a href="{{ url('topic-lecture-sheets/'.$doctor_course_info->id.'/'.$topic->id) }}">{{ $topic->name }}</a></li>                    
                                        @endforeach
                                    </ul>
                                    
                                    </div>
                                </div>
                            </nav> 
                        </div>--}}

                        <div class="form-group">
                            <input type="hidden" name="lecture_sheet_article_batch_id" value="{{ $lecture_sheet_article_batch_id }}">
                            
                            <div class="col-md-12">
                                <div class="input-icon right">
                                    @php  $lecture_sheet_article_topics->prepend('Select Class/Chapter', ''); @endphp
                                    {!! Form::select('topic_id',$lecture_sheet_article_topics, old('topic_id')?old('topic_id'):'' ,['class'=>'form-control','required'=>'required','id'=>'topic_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                        
                                        

                                        <div class="row">


                                            @foreach($lecture_sheet_article_batch as $lecture_sheet_article)
                                                <div class="col-md-4">
                                                        <h2><a href="{{ url('lecture-sheet-article-details/'.$lecture_sheet_article->id ) }}">{{ $lecture_sheet_article->title }}</a></h2>
                                                    <div>{!! substr(strip_tags($lecture_sheet_article->description),0,100) !!}<br>
                                                        <a href="{{ url('lecture-sheet-article-details/'.$lecture_sheet_article->id ) }}">Continue reading...</a>
                                                </div>
                                            @endforeach


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
            

        })
    </script>


@endsection
