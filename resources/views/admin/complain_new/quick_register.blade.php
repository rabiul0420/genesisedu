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
                Quick Admission
            </li>
        </ul>
    </div>
    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))? Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i> Quick Admission
                    </div>
                </div>
                
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['url'=>'admin/complain/quick_register_submit','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group" style="display: flex; align-items:center;" >
                            <label class="col-md-3 control-label">Mobile Number(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">
                                    <div class="position-relative w-100 prefix">
                                        <span class="position-absolute border-bottom text-dark border-info bg-warning" style="padding:9px 9px; left: 0px;position: relative; top: 25px; background:#FFBF00;">+88</span>
                                        <input class="form-control" style="padding-left: 50px;" id="mobile_number" type="number" maxlength="11" id="sent"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  name="mobile_number" placeholder="01700000000"  required pattern="[0-9]{11}">
                                    </div>
                            </div>
                        </div> 
                        

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/discount') }}" class="btn btn-default">Cancel</a>
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
    function showPwd(id, el) {
        let x = document.getElementById(id);
        if (x.type === "password") {
            x.type = "text";
            el.className = 'fa fa-eye-slash showpwd';
        } else {
            x.type = "password";
            el.className = 'fa fa-eye showpwd';
        }
        }
        
        $(document).ready(function() {
                
                        
                
        });

    </script>

@endsection