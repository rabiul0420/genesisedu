@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a></i>
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
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{$title}}
                    </div>
                </div>
                <div>
                    <?php
                    //echo '<pre>';
                    //print_r($institute);
                    ?>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->

{!! Form::open(['url'=>['admin/question-replied'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">

                            <div class="form-group">
                                <div class="col-md-1"><b>Question</b></div>
                                <div class="col-md-11">                                    
                                    
                                    <input type="hidden" name="question_id" required value="{{$question_id->id}}">
                                    <input type="hidden" name="reply_by" required value="{{Auth::id()}}">
                                    {{ $question_id->question }}
                                    
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-1"><b><font color="Orange">Replied</font></b></div>
                                <div class="col-md-11">                                    
                                    <?php
                                        $temp_name = \App\DoctorQuestionReply::select('*')->where('question_id', $question_id->id)->get();
                                            foreach ($temp_name as $i=>$replied){
                                                echo ++$i.". ".$replied->reply."<br>";
                                            }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-1 control-label">Reply (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-11">
                                    <div class="input-icon right">
                                        <input type="text" name="reply" required value="" class="form-control">
                                    </div>
                                </div>
                            </div>

                            
                    </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-9">
                                    <button type="submit" class="btn btn-info">Reply Submit</button>
                                    
                                </div>
                            </div>
                        </div>
                       {!! Form::close() !!}
                    <!-- END FORM-->
                </div>
            </div>

        </div>
    </div>



@endsection

@section('js')

    

    



@endsection