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
    .bg {
        background: #a6ecc5;
        color: #0f77b7;
    }

</style>

<div class="container">


    <div class="row">
        @include('side_bar' )

        <div class="col-md-9">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">@yield('heading')</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="col-md-12 p-0">
                        <div class="portlet">
                            <div class="portlet-body">
                                @yield('section-content')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>
@endsection
