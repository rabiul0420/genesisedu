@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Conversation List</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ Session::get('class') ?? 'alert-success' }} " role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Conversation List
                        <a href="{{ action('Admin\ConversationSmsController@create') }}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mobile Number</th>
                            <th>Sms</th>
                            <th>Question Title</th>
                            <th>Question Link</th>
                            <th>SMS Sender</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/conversation-sms-list",
                    type: 'GET',
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'mobile_number',name:'d1.mobile_number'},
                    {data: 'sms',name:'d1.short_sms'},
                    {data: 'question_title',name:'d2.title'},
                    {data: 'question_link',name:'d2.question_link'},
                    {data: 'sender_name',name:'d3.name '},
                ]
            })
        })
    </script>

@endsection
