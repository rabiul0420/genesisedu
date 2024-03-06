@extends('layouts.app')
@section('content')

<section>
    <div class="container" style="padding-top: 20px; padding-bottom: 20px">
        <div class="big-demo" data-js="hero-demo">
            <div class="row">
                <div class="text-center">
                    <h2 style="background-color: #0f77b7; color: #FFF; border-radius: 20px; padding: 10px 20px">
                        {{ $available_batch->batch_name }} Admission Link</h2>
                    <p style="font-size: 25px">{{ $available_batch->course_name }}</p>
                </div>

                @if(Session::has('message'))
                    <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                        <p> {{ Session::get('message') }}</p>
                    </div>
                @endif

             
                
                <div class="row text-center">
                    <div class="col-12">
                        <div class="___class_+?6___">
                            <div class="py-3" style="width: 100%; overflow: auto;">
                                <table class="table table-bordered">
                                    @if( isset($links) && is_array($links) ) 
                                        @foreach($links as $link )
                                            @php $i = 0; @endphp
                                            <tr style="background-color: #80ff80">
                                                <th colspan="3">{{ $link[ 'headline' ] ?? '' }} </th>
                                            </tr>

                                            @foreach( (array) ($link['link_contents'] ?? []) as $link_content )
                                                <tr>
                                                    <td>{{ $link_content[ 'title' ] ?? '' }} </td>
                                                    <td>                  
                                                        <a href="{{ $link_content[ 'url' ] ?? '' }}" style="background-color:#ff9900; color:#404040;" class="btn  btn-sm" role="button">Enroll Now</a>
                                                    </td>
                                                </tr>
                                                @php $i++; @endphp
                                            @endforeach
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
