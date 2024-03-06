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
            <div class="col-md-12 shadow-sm mt-2">
                <div class="portlet">
                    <div class="portlet-title">
                        {{-- {{ $complain->complain_reply_new->complain_type_id }} --}}
                        <div class="caption">
                            <i class="fa fa-reorder"></i> Conversations  
                        </div>
                    </div>
                    
                    <style>
                      .complain-details a:link,.complain-details a:visited{
                            color:white;                            
                      }
                    </style>
                    <div class="portlet-body p-3 complain-details">
                        @foreach($doctor_complain_replies as $key => $doctor_complain_reply)
                            <div class="w-100 rounded-lg my-2 {{ $doctor_complain_reply->user_id!=0?'pl-5 text-right':'pr-5' }}">
                                <span class="image" style="font-size: 14px; display: inline-block; border-radius: 15px; padding:15px; margin-top: 5px;
                                    background-color:{{($doctor_complain_reply->user_id !=0 )?'#024dbcbc':'#aaaa'}}; color:{{($doctor_complain_reply->user_id!=0)?'#ffff':''}};">
                                    {!! $doctor_complain_reply->message !!}
                                </span><br>
                                <span style="font-size: 10px; padding: 0 8px;">{{ date('d M Y h:m a',strtotime($doctor_complain_reply->created_at))}}</span><br><span style="font-size: 10px; padding: 0 8px;">Batch Name: {{ isset($complain->batch->name) ? $complain->batch->name : ' '}}</span><br><span style="font-size: 10px; padding: 0 8px;">Complain Related: {{ ($complain->complain_reply_new->complain_type_id == "1") ? 'Lecture Video/Exam Solve Video' :  (($complain->complain_reply_new->complain_type_id == "2") ? 'Exam Link' :  (($complain->complain_reply_new->complain_type_id == "3") ? 'Publications (Lecture Sheet/Books)' :  (($complain->complain_reply_new->complain_type_id == "4") ? 'Technical & Payment Issue' :  (($complain->complain_reply_new->complain_type_id == "5") ? 'Others' : ' '))))}}</span>
                                <br>
                                <span class="image"
                                    style="font-size: 10px; padding: 0 8px; color:{{($doctor_complain_reply->user_id!=0)?'':'#ffff'}};">by
                                    {{ isset($doctor_complain_reply->user->name) ? $doctor_complain_reply->user->name :'' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i> Reply
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\DoctorComplainController@reply_complain'],'method'=>'POST','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Done Messagege</label>
                            <div class="col-md-3">
                                <label class="radio-inline">
                                    <input type="radio" id="done_message" name="done_message"  value="1"> Done
                                </label>
                               
                            </div>
                        </div>

                        <div class="without_done">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Doctor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="hidden" name="doctor_id" required id="doctor_id" value="{{ isset($doctor_complain_reply->doctor->name) ? $doctor_complain_reply->doctor_id :'' }}">
                                        <input type="text" class="form-control" name="doctor_name"  id="doctor_name" readonly  value="{{ isset($doctor_complain_reply->doctor->name) ? $doctor_complain_reply->doctor->name.' ( '.$doctor_complain_reply->doctor->id.' ) ' :'' }}">
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="col-md-3 control-label">BMDC No (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" class="form-control" name="doctor_name" readonly id="bmdc_no"   value="{{ $doctor_complain_reply->doctor->bmdc_no ?? ' ' }}">
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="col-md-3 control-label">Mobile No (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" class="form-control" name="mobile_number" id="mobile_number" readonly  value="{{ $doctor_complain_reply->doctor->mobile_number ?? ' ' }}">
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="col-md-3 control-label">User (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="hidden" name="user_id" required value="{{ isset($user->id) ? $user->id :'' }}">
                                        <input type="text" class="form-control" name="user_name" id="user_name" disabled  value="{{ isset($user->name) ? $user->name :'' }}">
                                    </div>
                                </div>
                            </div>
    
                            <input type="hidden" name="doctor_complain_id" required value="{{ isset($doctor_complain_reply->doctor_complain_id) ? $doctor_complain_reply->doctor_complain_id :'' }}">

                            <div class="form-group">
                                <label class="col-md-3 control-label">Auto Reply Tittle</label>
                                <div class="col-md-4">                                
                                    <div class="controls">
                                        @php  $question_tittle->prepend('Select Tittle', ''); @endphp
                                        {!! Form::select('title_id',$question_tittle, old('title_id'),['class'=>'form-control select2','id'=>'title_id']) !!}<i></i>
                                     </div>                           
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="col-md-3 control-label">Message (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-9">
                                    <div class="input-icon right">
                                        <textarea id="message" name="message">{{ old('message')?old('message'):'' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/doctor-complain-list') }}" class="btn btn-default">Cancel</a>
                                <a style="float: right" href="{{ url('admin/doctor-complain-list') }}" class="btn btn-warning">All Complains</a>
                                <a  style="float: right;margin-right:1px; background-color:#280869;color:#ffffff" href="{{ url('admin/doctor-complain-message/'. $doctor_complain_reply->doctor->mobile_number . '/' . $doctor_complain_reply->doctor_complain_id) }}" class="btn">Send Sms</a>
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
   
   document.getElementById('done_message').onclick = () => {
        document.querySelector(".without_done").style.display = "none";
    }


        
    $(document).ready(function() {
            
        CKEDITOR.replace( 'message' );

        $('#title_id').select2();


        $("body").on( "change", "#title_id", function() {

     
            var text = $(this).val();

            CKEDITOR.instances.message.insertText(text+' ');
            

          //  var messagedata =   CKEDITOR.instances.message.getData();
            

           // CKEDITOR.instances.message.setData( messagedata + text );

           
       
    

        })

    })
    
    </script>


@endsection
