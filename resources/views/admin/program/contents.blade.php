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
        .program-contents-header
        {
            padding: 5px;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 17px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            color: white;
            background-color: #1e7b7b;
            text-decoration: none;
            cursor: pointer;
        }
        .program-contents
        {
            border: 5px;
            border-radius: 50%;
            padding: 5px;
        }
        .program-jumbotron
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size:16px;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: #00BFC9;
        }

        .program-content-link:link
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size:16px;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: #00BFC9;
            text-decoration: none;
            cursor: pointer;
        }

        .program-content-link
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size:16px;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: #00BFC9;
            text-decoration: none;
            cursor: pointer;
        }

        .program-content-link:visited
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size:16px;
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
                <a href="{{url('admin/program')}}" class="btn btn-xs btn-primary">Program</a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">            

            <div class="panel panel-primary"  style="border-color: #eee; ">
                <div class="program-contents-header">                    
                    <div>Add Contents : {{ $program->name }}</div>
                </div>
                <div class="panel-body">

                    @foreach($program->schedule_program_content_types() as $content)
                    @if(($program->program_type_id == 1 && $content->id == 5) || ($program->program_type_id == 1 && $content->id == 6) || ($program->program_type_id == 2 && $content->id == 5) || ($program->program_type_id == 2 && $content->id == 6) || ($program->program_type_id == 3 && $content->id == 5) || ($program->program_type_id == 3 && $content->id == 6) || ($program->program_type_id == 5 && $content->id == 5) || ($program->program_type_id == 5 && $content->id == 6) || 
                        ($program->program_type_id == 4 && $content->id == 4) || ($program->program_type_id == 4 && $content->id == 6) ||
                        ($program->program_type_id == 4 && !in_array("Online",$program->media_types()) && $content->id == 5) || 
                        (($program->program_type_id == 1 || $program->program_type_id == 2 || $program->program_type_id == 3 || $program->program_type_id == 5) && ( !in_array("Recorded",$program->media_types()) ) && $content->id == 4)
                        )
                    @php continue; @endphp
                    @endif
                    <div class="col-md-3 program-contents">
                        <div class="jumbotron program-jumbotron">
                            <a class="program-content-link" href="{{url('admin/program-content-add/'.$program->id.'/'.$content->id )}}">{{ $content->name }}</a>
                        </div>                                                                   
                    </div>        
                    @endforeach

                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12"> 
            <style>
                .content-item
                {
                    border:3px solid blue;
                    border-radius: 10px;
                    margin : 10px;
                    padding: 5px;
                }
                table tr,td
                {
                    margin: 10px;
                    padding: 10px;
                    text-align: left;
                }
            </style>           

            <div class="panel panel-primary"  style="border-color: #eee; ">
                <div class="program-contents-header">                    
                    <div>Program Contents : {{ $program->name }}</div>
                </div>
                <div class="panel-body">
                    <table >
                        @php $media_types = $program->media_types(); @endphp
                        @if(isset($media_types))
                        <tr>
                        <td>
                            <b>Media Types</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($media_types as $media_type)
                        <span class="content-item">{{$media_type}}</span>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $topics = $program->topics(); @endphp
                        @if(isset($media_types))
                        <tr>
                        <td>
                            <b>Topics</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($topics as $topic)
                        <span class="content-item">{{$topic}}</span>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $mentors = $program->mentors(); @endphp
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
                        <span class="content-item">{{$mentor->name??''}}</span>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $lecture_videos = $program->lecture_videos(); @endphp
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
                        <span class="content-item">{{$lecture_video->name??''}}</span>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $exams = $program->exams(); @endphp
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
                        <span class="content-item">{{$exam->name??''}}</span>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                    </table>

                </div>
            </div>

        </div>
    </div>
    <!-- END PAGE CONTENT-->


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

