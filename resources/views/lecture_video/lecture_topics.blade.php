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

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'Online Lecture Links' }}</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">

                                    
                                    @if(Request::segment(1)=='doctor-course-lecture-video')
                                    <div class="my-2 ml-auto" style="max-width: 360px">
                                        <input id="data-input" type="text" class="form-control" placeholder="Search by title : Type at least 3 letters" onkeyup="dataSearch()">
                                    </div>
                                    @if(isset($lecture_video_batch)) 
                                    <div id="data-field">
                                        <div class="row mx-0">                             
                                            <div class="col p-0 pt-3">
                                                <table id="example" class="table table-striped table-bordered rounded">
                                                    <thead>
                                                        <tr>
                                                            <th>SL</th>
                                                            <th>Date</th>
                                                            <th>Action</th>
                                                            <th>Video Title</th>
                                                        </tr>
                                                    </thead> 
                                                    <tbody>
                                                        @foreach($lecture_video_batch as $k => $lecture_video)
                                                        <tr>
                                                            <td class="pt-3">{{ $k + $lecture_video_batch->firstItem() }}</td>
                                                            <td class="pt-3">{{ date('d-m-Y',strtotime($lecture_video->created_time)) }}</td>
                                                            <td class="text-center">
                                                                {{-- <a title="{{ $lecture_video->name }}" 
                                                                    class="btn btn-info btn-sm text-white" 
                                                                    href="{{ url( 'lecture-video-details/'.$lecture_video->id ) }}">
                                                                    Play
                                                                </a> --}}

                                                                <span class="btn px-2 py-1 btn-primary venobox" 
                                                                @if( $lecture_video->password)
                                                                    title="{{ '<h6 class="text-warning"> Password: '. $lecture_video->password .'</h6>'  }}"
                                                                @endif
                                                                data-autoplay="true" data-gall="gallery01" data-vbtype="video"
                                                                href="{{ $lecture_video->lecture_address }}">
                                                                play
                                                                </span>

                                                                {{-- <a class="btn px-2 py-1 btn-info" href="{{ $lecture_video->pdf_file }}">
                                                                view pdf
                                                                </a> --}}

                                                                {{-- lecture-video-details/{id} --}}


                                                                @if($lecture_video->pdf_file)
                                                                    <a class="btn px-2 py-1 btn-info" href="{{url('lecture-video-details/'.$lecture_video->id)}}">PDF</a>
                                                                @endif



                                                            </td>
                                                            <td class="pt-3 text-left">{{ $lecture_video->name }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    
                                                </table>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="row mx-0">
                                            <div class="col-12 ">                                        
                                                <div class="text-center">
                                                    <div class="w-100 pt-2 pb-4">
                                                        {{ $lecture_video_batch->links('components.paginator') }}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                        @endif
                                  
                                    @elseif(Request::segment(1)=='lecture-video')
                                    <div class="row mx-0">
                                        @foreach($doctor_courses as $doctor_course)
                                        <div class="col-md-6 p-1">                                                                           
                                            <a title="{{ $doctor_course->course->name ?? ''.' - '.$doctor_course->batch->name }}" 
                                                class="w-100 bg px-3 py-4 border rounded-lg" 
                                                href="{{ isset($doctor_course->batch->status) && $doctor_course->batch->status == 0 ? 'javascript:void(0)' : url('doctor-course-lecture-video/'.$doctor_course->id) }}">
                                                <h6 class="bg" >{{  $doctor_course->course->name ?? ' '}} : {{  $doctor_course->batch->name ?? '' }}</h6>
                                                @if ( isset($doctor_course->batch->status) && $doctor_course->batch->status == 0 )
                                                    <span class="badge bg-danger">Batch Inactive</span>
                                                @endif
                                            </a>
                                        </div>
                                        @endforeach                                            
                                    </div>
                                    @endif

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
@if(Request::segment(1)=='doctor-course-lecture-video')
    <script type="text/javascript" src=" {{ asset('js/venobox.min.js') }}"></script>
    <script>
        const dataField = document.getElementById('data-field')
        const dataFieldOld = document.getElementById('data-field').innerHTML
        const dataInput = document.getElementById('data-input')
        const url = '/doctor-course-lecture-video-ajax/'+{{ $doctor_course_id }}

        function dataSearch(){

            if(dataInput.value.length > 2){
                axios.get(url, {
                    params: {
                    text: dataInput.value
                    }
                })
                .then(function (response) {
                    console.log(response);
                    dataField.innerHTML = ''
                    dataField.innerHTML = response.data
                    $('.venobox').venobox(); 
                })
                .catch(function (error) {
                    console.log(error);
                });
            }else{
                dataField.innerHTML = dataFieldOld
            }
        }
        $(document).ready(function(){
        $('.venobox').venobox(); 
    });
    </script>

@endif
@endsection
