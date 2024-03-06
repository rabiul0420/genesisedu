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
                Batch System Driven
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
                        <i class="fa fa-reorder"></i>Batch System Driven
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>'Admin\BatchController@system_driven_save','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Batch System Driven Settings</div>
                            <div class="panel-body">
                                <div class="form-group ">
                                    <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-6" id="id_div_system_driven">
                                    <div class="input-icon right">
                                        <select name="batch_id" required class="form-control batch">
                                                <option value="{{$batch->id}}" selected="selected" disabled>{{$batch->name.' - '.$batch->id }}</option>
                                        </select>
                                        <input type="hidden" name="batch_id" value="{{$batch->id}}" />
                                    </div>
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="col-md-3 control-label">System Driven Option (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3" id="id_div_system_driven">
                                        <span style="color:green;font-weight:700;vertical-align: baseline;padding:9px 12px;">{{ $batch->system_driven }}</span>
                                    </div>

                                </div>
                                @if($batch->system_driven != "No")
                                @if($batch->system_driven == "Optional")
                                <div class="form-group">

                                    <label class="col-md-3 control-label">Doctor selection maximum change (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-6" id="system_driven_change_count_max">
                                        <div class="input-icon right">
                                            <input type="number" name="system_driven_change_count_max" min="1" max="10" value="{{ $batch->system_driven_change_count_max ?? '1' }}" class="form-control" required >
                                        </div>
                                    </div>

                                </div>
                                @endif

                                <div class="form-group">

                                    <label class="col-md-3 control-label">System Driven Text (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-6" id="id_system_driven_text">
                                        
                                        <textarea  name="system_driven_text" id="system_driven_text" required >{{ $batch->system_driven_text }}</textarea>
                                        
                                    </div>

                                </div>
                                @endif
                                
                            </div>                            
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/batch') }}" class="btn btn-default">Cancel</a>
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
            CKEDITOR.replace( 'system_driven_text' );

        });

    </script>


@endsection
