@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Coursewise Online Lecture Links</h3></div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        <div class="col-md-12 col-md-offset-0" style="">
                            <hr><h4><b>Coursewise Online Lecture Links</b></h4>
                        </div>

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Reg. No.</th>
                                            <th><b>Online Lecture Links</b></th>
                                            <!-- <th><b>Show Online Exam Results</b></th> -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php $i=1; @endphp
                                        @foreach ($online_lecture_links as $key=>$online_lecture_link)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $key }}</td>
                                                <td>
                                                @foreach($online_lecture_link as $keys=>$links)
                                                    @foreach($links as $key=>$link)
                                                        <!--<a href="{{ $link->lecture_address }}" target="_blank" class="btn btn-sm btn-primary">{{ $link->name }}</a><br>-->
                                                            <span class="btn btn-sm btn-primary" data-toggle='modal' data-target='#myModal_{{$link->id}}'>{{ $link->name }}</span>
                                                            <div class='modal fade' id='myModal_{{$link->id}}' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
                                                                <div class='modal-dialog' role='document' style='width: 100%;'>
                                                                    <div class='modal-content'>

                                                                        <div class='modal-header'>
                                                                            <h4 class='modal-title' id='myModalLabel'>{{ $link->name }}</h4>

                                                                            @if($link->password)

                                                                                <h4><b>Video Password:</b> {{ $link->password }}</h4>
                                                                            @endif
                                                                        </div>
                                                                        <div class='modal-body'>
                                                                            <div class="col-md-6">
                                                                                @if($browser == 'UCBrowser')
                                                                                    <p>Sorry this video does not support UC browser. Please use another browser.</p>
                                                                                @else
                                                                                    <iframe width='100%' height='400' src='{{$link->lecture_address}}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
                                                                                @endif
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <iframe width='100%' height='500' src="pdf/{{$link->pdf_file}}"></iframe>
                                                                            </div>

                                                                        </div>

                                                                        <div class='modal-footer'>
                                                                            <button type='button' class='btn btn-sm bg-red' data-dismiss='modal'>Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @endforeach
                                                    @endforeach


                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>






                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection


