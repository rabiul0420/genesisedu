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

        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>{{ 'Lecture Videos' }}</h3></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                        <div class="col-md-9">
                            <div class="portlet">
                                <div class="portlet-body">
                                  <b><ul class="page-breadcrumb">
                                      <li>
                                          <i class="fa fa-home"></i> <a href="{{ url('/my-profile') }}"> Home</a></i>
                                      </li>
                                      <li> <i class="fa fa-angle-right"></i> <a href="/lecture-video">Lecture Videos</a></li>
                                      <li> <i class="fa fa-angle-right"></i> {{ '' }} </li>
                                  </ul></b>
                                    
                                  <div class="row">
                                    <div class="form-group">
                                        <input type="hidden" name="lecture_video_batch_id" value="{{ '' }}">
                                
                                        <div>
                                            <div class="input-icon right">
                                                @php  $lecture_video_topics->prepend('Select Class/Chapter', ''); @endphp
                                                {!! Form::select('topic_id',$lecture_video_topics, old('topic_id')?old('topic_id'):'' ,['class'=>'form-control','required'=>'required','id'=>'topic_id']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                  
                                    <div class="row">
                                       
                                        @foreach($lecture_video_batch as $lecture_video)
                                            <div class="col-md-4">
                                                    <h2><a href="{{ url('lecture-details/'.$lecture_video->id ) }}">{{ $lecture_video->title }}</a></h2>
                                                <div>{!! substr(strip_tags($lecture_video->description),0,100) !!}<br>
                                                    <a href="{{ url('lecture-details/'.$lecture_video->id ) }}">Continue reading...</a>
                                            </div>
                                        @endforeach
                                        
                                    </div>

                                    <div class="row">
                                        <!-- <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9"> -->
                                        <div>
                                            <div class="text-center">
                                                {{ $lecture_video_batch->links() }}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>

        <div class="col-md-3  col-md-offset-9">
            <div class="panel panel-default">
            <div class="panel-heading" style="background-color: #7fc9f6; text-align: center;"><h3><a href="#" style="text-decoration: none; color: #FFFFFF;">{{ '' }} Lecture Sheets</a></h3></div>

            <div class="panel-body">
                <div class="sidebar">
                        @foreach($doctor_courses as $doctor_course)
                            <a class="@php echo (Request::segment(1)=='lecture-details' && Request::segment(2)==$lecture_sheet->id )?'active':''  @endphp" href="{{url('lecture-details/'.$doctor_course->id)}}">{{ $doctor_course->course->name }}</a>				
                        @endforeach
                </div>
            </div>
		    </div>
        </div>

    </div>


</div>
@endsection
