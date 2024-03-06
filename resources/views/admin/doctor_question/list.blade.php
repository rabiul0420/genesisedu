@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{$title}}</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>{{ $title }}
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="100">Date & Time</th>
                            <th>Doctor Name</th>
                            <th>Batch Name</th>
                            <th>Lecture Name</th>
                            <th>Question</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($question_info as $sl=>$question)
                        <tr>
                            <td>{{ $sl+1 }}</td>
                            <td>{{ $question->created_at }}</td>
                            <td>{{ $question->doctorname->name ??'' }} ({{$question->doctor_id}})</td>
                            <td>{{ $question->batchname->name ?? '' }}</td>
                            <td>{{ $question->lecturename->name ?? '' }}</td>
                            <td>{{ $question->question }}</td>
                            <td>
                                @if($question->status==1)
                                <a href="#" class="btn btn-xs btn-primary" style="background-color:red; border:0px;">No Replied</a>
                                @endif
                                @if($question->status==2)
                                <a href="#" class="btn btn-xs btn-primary" style="background-color:green; border:0px;">Replied</a>
                                @endif
                            </td>
                            <td><a href="{{ url('admin/question-reply/'.$question->id) }}" class="btn btn-xs btn-primary">Update</a></td>
                        </tr>
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

