@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        @include('side_bar')
        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;">
                    <h3>Complain Box</h3>
                </div>
                <div class="panel-body">
                    @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                    @endif
                    <!-- <div class="col-md-12 col-md-offset-0" style="">
                                <h4><b>My Complains</b></h4>
                            <div class="portlet">
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th width="100">Date</th>
                                            <th>Complain</th>
                                            
                                            <th width="140">Action</th>
                                            
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($complain_info as $complain)
                                            
                                            <tr>
                                                <td>{{substr($complain->created_at,0,10)}}</td>
                                                <td>
                                                    <?php
                                                        //$complains = App\DoctorComplainReply::select('*')->where('doctor_complain_id', $complain->id)->orderBy('id', 'asc')->first();
                                                        //echo $complains->message;
                                                    ?>
                                                </td>
                                               
                                                <td>
                                                    <a href="complain-details/{{$complain->id}}" class="btn btn-sm btn-primary">
                                                    View Reply</a>
                                                </td>
                                            </tr>
                                        
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div> -->

                    <div class="col-md-12 col-md-offset-0" style="">
                        <hr>
                        <h4 style="color:orange"><b>New Complain</b></h4>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            {!!
                            Form::open(['url'=>['submit-complain'],'method'=>'post','files'=>true,'class'=>'form-horizontal'])
                            !!}
                            <div class="form-body">
                              <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="input-icon right">
                                            Type Complain : <br>
                                            <textarea name="description" value="" class="form-control"
                                                required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions" style="margin-top: 20px;">
                                    <div class="row">
                                        <div class="col-md-offset-0 col-md-9">
                                            <button type="submit" class="btn btn-info ">Submit Complain</button>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                                <!-- END FORM-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('js')
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function () {
            CKEDITOR.replace('description');
            // $('.select2').select2();
        })
    </script>

    @endsection