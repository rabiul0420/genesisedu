@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li> <i class="fa fa-angle-right"> </i> <a href="#">Notice</a></li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Notice Details
                    </div>
                </div>
                <div class="portlet-body form">
                    <div style="padding:20px;">
                        <h3><b>{{$notices->title}}</b></h3>

                        <?php 
                            if (isset($notices->attachment)) {
                                echo "<a href='".url($notices->attachment)."' target='_blank'>View Attachment</a>"; 
                            } 

                            if ($notices->type=='I'){
                                echo "<h4>Notice type : Individual</h4><h4>Doctor List : </h4>"; 
                            } elseif ($notices->type=='B') {
                                echo "<h4>Notice type : Batch</h4>";
                            } else if($notices->type=='C'){
                                echo "<h4>Notice type : Course</h4>";
                            } else {
                                echo "<h4>Notice type : All</h4>";
                            }

                            if (isset($doctors)){
                                foreach ($doctors as $key => $doctor) {
                                    echo ++$key.". ".$doctor->doctorname->name." (".$doctor->doctorname->bmdc_no.")<br>";
                                }
                            }

                            

                        ?>
                        <hr>
                        <h4 style="color:Orange;">Notice : </h4>
                        <?php echo $notices->notice; ?>


                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

