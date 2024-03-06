@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'"> '.$value.' </a></li>';
            }
            ?>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <style>
        .header-horizontal-menu
        {
            padding-bottom: 5px;
        }
        .topic-contents-header
        {
            padding: 5px;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 17px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            color: white;
            background-color: #1e7b7b;
            text-decoration: none;
            cursor: pointer;
        }
        .topic-contents
        {
            border: 5px;
            border-radius: 50%;
            padding: 5px;
        }
        .topic-jumbotron
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: #00BFC9;
        }

        .topic-content-link:link
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: #00BFC9;
            text-decoration: none;
            cursor: pointer;
        }

        .topic-content-link
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: #00BFC9;
            text-decoration: none;
            cursor: pointer;
        }

        .topic-content-link:visited
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: #00BFC9;
            text-decoration: none;
            cursor: pointer;
        }
    </style>

    <div class="row">
        <div class="col-md-12">            
            <div class="header-horizontal-menu">
                @can('Topic')
                <a href="{{url('admin/topics')}}" class="btn btn-xs btn-primary">Topics</a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">            

            <div class="panel panel-primary"  style="border-color: #eee; ">
                <div class="topic-contents-header">                    
                    <div>Add Contents : {{ $topic->name }} </div>
                </div>
                <div class="panel-body">

                    @foreach($topic->schedule_topic_content_types() as $content)
                    <div class="col-md-3 topic-contents">
                        <div class="jumbotron topic-jumbotron">
                            <a class="topic-content-link" href="{{url('admin/topic-content-add/'.$topic->id.'/'.$content->id )}}">{{ $content->name }}</a>
                        </div>                                                                   
                    </div>        
                    @endforeach

                </div>
            </div>

        </div>
    </div>
    <!-- END PAGE CONTENT-->

    <div class="row">
        <div class="col-md-12"> 
            <style>
                .content-item
                {
                    border:0px solid blue;
                    border-radius: 10px;
                    margin : 10px;
                    padding: 5px;
                }

                table
                {
                    width: 100%;
                }

                table tr th:first-child,
                table tr td:first-child {
                    width: 8em;
                    min-width: 8em;
                    max-width: 8em;
                    word-break: break-all;
                }

                table tr
                {
                    border-bottom:3px solid blue;
                    border-radius: 10px;
                    margin : 10px;
                    padding: 5px;
                    text-align: left;
                }

                table td
                {
                    text-align: left;
                }
            </style>           

            <div class="panel panel-primary"  style="border-color: #eee; ">
                <div class="topic-contents-header">                    
                    <div>Topic Contents : {{ $topic->name }}</div>
                </div>
                <div class="panel-body">
                    <table>
                        @php $mentors = $topic->mentors(); @endphp
                        @if(isset($mentors))
                        <tr>
                        <td>
                            <b>Mentors</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($mentors as $mentor)
                        <span class="content-item">{{$mentor->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $lecture_videos = $topic->lecture_videos(); @endphp
                        @if(isset($lecture_videos))
                        <tr>
                        <td>
                            <b>Lecture Videos</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($lecture_videos as $lecture_video)
                        <span class="content-item">{{$lecture_video->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $exams = $topic->exams(); @endphp
                        @if(isset($exams))
                        <tr>
                        <td>
                            <b>Exams</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($exams as $exam)
                        <span class="content-item">{{$exam->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                    </table>

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

            //$('.select2').select2({ });


        })
    </script>


@endsection

