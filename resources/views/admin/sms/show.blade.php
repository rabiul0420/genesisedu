@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li> <i class="fa fa-angle-right"> </i> <a href="#">Sms</a></li>
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
                        <i class="fa fa-reorder"></i>Sms Details  <a href="{{ url('admin/sms-send-list/'.$smss->id) }}" class="btn btn-xs btn-success">Send SMS</a>
                    </div>
                </div>
                <div class="portlet-body form">
                    <div style="padding:20px;">
                        <h3><b>{{$smss->title}}</b></h3>

                        <?php 
                            
                            if ($smss->type=='I'){
                                echo "<h4>Sms type : Individual</h4><h4>Doctor List : </h4>"; 
                            } elseif ($smss->type=='B') {
                                echo "<h4>Sms type : Batch</h4>";
                            } else if($smss->type=='C'){
                                echo "<h4>Sms type : Course</h4>";
                            } else {
                                echo "<h4>Sms type : All</h4>";
                            }

                            if (isset($doctors)){
                                foreach ($doctors as $key => $doctor) { //echo "<pre>";print_r($doctor);exit;
                                    echo ++$key.". ".$doctor->doctor->name." (".$doctor->doctor->bmdc_no.")<br>";
                                }
                            }

                            

                        ?>
                        <hr>
                        <h4 style="color:Orange;">Sms : </h4>
                        <?php echo $smss->sms; ?>


                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

