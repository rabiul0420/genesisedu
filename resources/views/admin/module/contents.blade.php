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
        .module-contents-header
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
        .module-contents
        {
            border: 5px;
            border-radius: 50%;
            padding: 5px;
        }
        .module-jumbotron
        {
            border-radius: 50%;
            font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size:16px;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: #00BFC9;
        }

        .module-content-link:link
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

        .module-content-link
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

        .module-content-link:visited
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
                @can('Module')
                <a href="{{url('admin/module')}}" class="btn btn-xs btn-primary">Module</a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">            

            <div class="panel panel-primary"  style="border-color: #eee; ">
                <div class="module-contents-header">                    
                    <div>Add Contents : {{ $module->name }}</div>
                </div>
                <div class="panel-body">

                    @foreach($module->schedule_module_content_types() as $content)
                    @if(($module->module_type_id == 1 && $content->id == 2) || ($module->module_type_id == 1 && $content->id == 3)  || ($module->institute->type == 0 && $content->id == 2) || ($module->institute->type == 1 && $content->id == 3 && $module->institute->id != 16))
                    @php continue; @endphp
                    @endif
                    <div class="col-md-3 module-contents">
                        <div class="jumbotron module-jumbotron">
                            <a class="module-content-link" href="{{url('admin/module-content-add/'.$module->id.'/'.$content->id )}}">{{ $content->name }}</a>
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
                <div class="module-contents-header">                    
                    <div>Module Contents : {{ $module->name }}</div>
                </div>
                <div class="panel-body">
                    <table>
                        @php $batches = $module->batches();@endphp
                        @if(isset($batches))
                        <tr>
                        <td>
                            <b>Batches</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($batches as $batch)
                        <span class="content-item">{{$batch->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $topics = $module->topics(); @endphp
                        @if(isset($topics))
                        <tr>
                        <td>
                            <b>Topics</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($topics as $topic)
                        <span class="content-item">{{$topic->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $faculties = $module->faculties(); @endphp
                        @if(isset($faculties))
                        <tr>
                        <td>
                            <b>Faculties</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($faculties as $faculty)
                        <span class="content-item">{{$faculty->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $subjects = $module->subjects(); @endphp
                        @if(isset($subjects))
                        <tr>
                        <td>
                            <b>Subjects</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($subjects as $subject)
                        <span class="content-item">{{$subject->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $program_types = $module->program_types(); @endphp
                        @if(isset($program_types))
                        <tr>
                        <td>
                            <b>Program types</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($program_types as $program_type)
                        <span class="content-item">{{$program_type->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $media_types = $module->media_types(); @endphp
                        @if(isset($media_types))
                        <tr>
                        <td>
                            <b>Media types</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($media_types as $media_type)
                        <span class="content-item">{{$media_type->name??''}}</span><br>
                        @endforeach
                        </td>
                        </tr>
                        @endif
                        @php $programs = $module->programs(); @endphp
                        @if(isset($programs))
                        <tr>
                        <td>
                            <b>Programs</b>
                        </td>
                        <td>
                            <b>:</b>
                        </td>
                        <td>
                        @foreach($programs as $program)
                        <span class="content-item">{{$program->name??''}}</span><br>
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

