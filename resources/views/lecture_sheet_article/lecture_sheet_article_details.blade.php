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

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>{{ $lecture_sheet->topic->name }}</h3></div>

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
                                      <li> <i class="fa fa-angle-right"></i> <a href="/lecture-sheet-article">Lecture Sheet</a></li>
                                      <li> <i class="fa fa-angle-right"></i> {{ $lecture_sheet->topic->name }} </li>
                                  </ul></b>
                                  
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2>{{ $lecture_sheet->title }}</h2>
                                            <p>{!!  $lecture_sheet->description  !!}</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>

        <div class="col-md-3  col-md-offset-0">
        <div class="panel panel-default">
		<div class="panel-heading" style="background-color: #7fc9f6; text-align: center;"><h3><a href="#" style="text-decoration: none; color: #FFFFFF;">{{ $lecture_sheet->topic->name }} Lecture Sheets</a></h3></div>

		<div class="panel-body">
        <div class="sidebar">
            @foreach($lecture_sheets as $lecture_sheet)
                <a class="@php echo (Request::segment(1)=='lecture-sheet-article-details' && Request::segment(2)==$lecture_sheet->id )?'active':''  @endphp" href="{{url('lecture-sheet-article-details/'.$lecture_sheet->id)}}">{{ $lecture_sheet->title}}</a>
            @endforeach
        </div>
        </div>
		</div>

        </div>

    </div>


</div>
@endsection
