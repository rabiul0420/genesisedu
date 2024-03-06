@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')


        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Online Exam </h3></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th>Exam Name </th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr>
                                            <td>DDP_P1_Foundation_2020</td>
                                            <td>
                                                <a href="https://banglamedexam.com/user-login-sif?name=Dr.%20Shahrin%20sultana&email=20118249&password=CEnuM8S3&bmdc=20118249&phone=01676779083&exam_comm_code=DDP_P1_Foundation_2020&topic_code=DDP_P1_Foundation_2020" target="_blank" class="btn btn-sm btn-primary">Click Here</a>
                                            </td>
                                        </tr>
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
