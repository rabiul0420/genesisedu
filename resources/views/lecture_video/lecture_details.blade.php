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

        @include('side_bar')

        <div class="col-md-9">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h3 class="brand_color">{{$link->name}}</h3>
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
                                    
                                    <div class="row mx-0">
                                        <div class="col-md-12">
                                            <div class="page-breadcrumb">
                                            {{-- <h5><b>Video :</b> {{ (isset($link->name))?$link->name:'' }}</h5> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mx-0">

                                        <div class="col-md-12">                                       
                                            {{-- <div class="col-md-6">
                                                @if($browser == 'UCBrowser')
                                                    <p>Sorry this video does not support UC browser. Please use another browser.</p>
                                                @else
                                                    <iframe width='100%' height='400' src='{{ $link->lecture_address }}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
                                                @endif
                                            </div> --}}

                                            {{-- <span class="btn px-2 py-1 btn-primary venobox" 
                                            @if( $link->password)
                                                title="{{ '<h6 class="text-warning"> Password: '. $link->password .'</h6>'  }}"
                                            @endif
                                            data-autoplay="true" data-gall="gallery01" data-vbtype="video"
                                            href="{{ $link->lecture_address }}">
                                            play
                                            </span> --}}


                                            <div class="col-md-12">
                                                    <iframe width='100%' height='750' src="{{ url('pdf/'.$link->pdf_file) }}"></iframe>
                                            </div>    
                                            
                                                {{-- @php
                                                $str = $link->lecture_address;
                                                $explode = explode("/",rtrim($str , "/"));  
                                                @endphp
                                                <iframe src="https://vimeo.com/event/{{ end($explode) }}/chat/" width="100%" height="100%" frameborder="0"></iframe>
                                            </div> --}}
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
@section('js')    
<script type="text/javascript" src=" {{ asset('js/venobox.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('.venobox').venobox(); 
    });
</script>
@endsection
