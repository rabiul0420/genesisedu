@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                Answer Create
            </li>
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
                        <i class="fa fa-reorder"></i>Answer Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\AnswersController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Book</label>
                            <div class="col-md-4">
                                @php  $questions->prepend('Select Question', ''); @endphp
                                {!! Form::select('question_id',$questions, (Request::segment(4))?Request::segment(4):old('question_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Answer</label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="text" name="answer_title" required value="{{ old('answer_title') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">True or False</label>
                            <div class="col-md-4">
                                {!! Form::select('tf', ['' => 'Select True or False','T' => 'True','F' => 'False'], old('tf'),['class'=>'form-control']) !!}<i></i>
                            </div>
                        </div>




                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/answer') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
        })
    </script>


@endsection