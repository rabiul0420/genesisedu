@extends('layouts.app')

@section('content')
    <style>

        .page-breadcrumb {
            display: inline-block;
            float: left;
            padding: 8px;
            margin: 0;
            list-style: none;
        }
        .page-breadcrumb > li {
            display: inline-block;
        }
        .page-breadcrumb > li > a,
        .page-breadcrumb > li > span {
            color: #666;
            font-size: 14px;
            text-shadow: none;
        }
        .page-breadcrumb > li > i {
            color: #999;
            font-size: 14px;
            text-shadow: none;
        }
        .page-breadcrumb > li > i[class^="icon-"],
        .page-breadcrumb > li > i[class*="icon-"] {
            color: gray;
        }

    </style>


    <div class="container">



        <div class="row">

            <div class="col-md-9 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Exam Status</h3></div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-header"><div id="timer" style="font-size:19px;font-weight:700;float:right;"></div><div hidden id="duration">Exam Status</div></div>
                                <div class="portlet-body">

                                    {{ $exam_status }}
                                    
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>


    </div>
@endsection
