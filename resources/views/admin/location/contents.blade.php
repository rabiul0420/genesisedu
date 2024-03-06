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
                    @if(($module->institute->type == 0 && $content->id == 2) || ($module->institute->type == 1 && $content->id == 3 && $module->institute->id != 16))
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

