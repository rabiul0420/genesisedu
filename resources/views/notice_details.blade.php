@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'Notice Details' }}</h2>
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
                                    <h3><b>{{$notice->title}}</b></h3>

                                    <?php 
                                        if ($notice->attachment) {
                                            echo "<a href='".url($notice->attachment)."' target='_blank'>View Attachment</a>"; 
                                        } 

                                        if ($notice->type=='I'){
                                            echo "<h5>Notice type : For me</h5>"; 
                                        } elseif ($notice->type=='B') {
                                            echo "<h5>Notice type : Batch</h5>";
                                        } else if($notice->type=="C"){
                                            echo "<h5>Notice type : Course</h5>";
                                        } else {
                                            echo "<h5>Notice type : All</h5>";
                                        }

                                        

                                    ?>
                                    <br>
                                    Date: {{$notice->created_at->format('d, M Y h:i:s:a')}}
                                    <hr>


                                    <h4 style="color:Orange;">Notice : </h4>
                                    <?php echo $notice->notice; ?>

                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>

    </div>


</div>
@endsection
