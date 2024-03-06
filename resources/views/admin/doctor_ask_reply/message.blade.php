@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'"> '.$value.' </a></li>';
            }
            ?>
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
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Message 
                    </div>
                </div>
                <div class="portlet-body form">
                    @foreach($answer_info as $key => $answer)
                        <div class="col-md-12" style=" padding:10px; margin: 2px;
                        background-color:{{($answer->user_id!=0)?'#FFFFFF':'#E9E9E9'}};">
                            {{($answer->user_id!=0)?'Replied':'My Question'}} : 
                            @php echo strip_tags($answer->message); @endphp
                        </div>
                    @endforeach                    
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Message 
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\DoctorAskReplyController@reply_store'],'method'=>'POST','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Doctor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="hidden" name="doctor_id" required value="{{ $doctor_ask_reply->doctor_id ? $doctor_ask_reply->doctor_id :'' }}">                                   
                                    <input type="text" class="form-control" name="doctor_name" disabled required value="{{ $doctor_ask_reply->doctor_id ? $doctor_ask_reply->doctor->name :'' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">User (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="hidden" name="user_id" required value="{{ $user->id ? $user->id :'' }}">                                
                                    <input type="text" class="form-control" name="user_name" disabled required value="{{ $user->name ? $user->name :'' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label"> Lecture Video (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="hidden" name="doctor_ask_id" required value="{{ $doctor_ask_reply->doctor_ask_id ? $doctor_ask_reply->doctor_ask_id :'' }}">        
                                    <input type="text" class="form-control" name="doctor_ask_id_video_name" disabled required value="{{ $doctor_ask_reply->doctor_ask_id ? $doctor_ask_reply->doctor_ask->video->name :'' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Message</label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <textarea id="message" name="message">{{ old('message')?old('message'):'' }}</textarea>
                                </div>
                            </div>
                        </div>

                        

                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/doctor-ask-reply') }}" class="btn btn-default">Cancel</a>
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

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>    

    <script type="text/javascript">
        $(document).ready(function() {
            CKEDITOR.replace( 'message' );
        })
    </script>


@endsection
