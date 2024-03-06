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

        {{--@include('side_bar')--}}

        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>{{ '' }}</h3></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                  <b><ul class="page-breadcrumb">
                                      <li>
                                          <i class="fa fa-home"></i> <a href="{{ url('/my-profile') }}"> Home</a></i>
                                      </li>
                                      <li> <i class="fa fa-angle-right"></i> <a href="/lecture-video">Lecture Video</a></li>
                                      <li> <i class="fa fa-angle-right"></i> {{ '' }} </li>
                                  </ul></b>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="page-breadcrumb">
                                            <h5><b>Video :</b> {{ $link->name }}</h5>

                                            @if($link->password)
                                                <h5><b>Video Password :</b> {{ $link->password }}</h5>
                                            @endif
                                        </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-12">                                       
                                            <div class="col-md-6">
                                                @if($browser == 'UCBrowser')
                                                    <p>Sorry this video does not support UC browser. Please use another browser.</p>
                                                @else
                                                    <iframe width='100%' height='400' src='{{ $link->lecture_address }}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <iframe width='100%' height='500' src="{{ url('pdf/'.$link->pdf_file) }}"></iframe>
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
