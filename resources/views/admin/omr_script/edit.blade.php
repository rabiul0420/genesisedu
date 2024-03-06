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
                        <i class="fa fa-reorder"></i>{{ $module_name }} Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\OmrScriptController@update',$omr_script->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">SET OMR NAME</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="name" required value="{{ $omr_script->name }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">SET OMR PROPERTIES</div>
                            <div class="panel-body">
                                <div class="property">
                                    <style>
                                        .auto-width
                                        {
                                            width: 100%;                                    
                                            text-align: left;
                                        }
                                        .vertical-center
                                        {
                                            margin: 0;
                                            position: absolute;
                                            top: 50%;
                                            -ms-transform: translateY(-50%);
                                            transform: translateY(-50%);
                                        }
                                        .span-minus
                                        {
                                            padding-left: 5px;
                                            text-align: left;
                                            cursor: pointer;
                                        }
                                        .span-plus
                                        {
                                            padding-left: 5px;
                                            text-align: left;
                                            cursor: pointer;
                                        }
                                    </style>

                                    <table class="auto-width">
                                        @if(count($omr_script->properties) == null)
                                        <tr id="1">                                    
                                            <td>
                                                <div class="form-group">

                                                    <label class="col-md-1 control-label">
                                                        <span class="control-label span-minus remove"><i class="fa fa-minus-circle"> </i></span>
                                                        <span class="control-label span-plus add"><i class="fa fa-plus-circle"></i></span>
                                                    </label>                                            

                                                    <label class="col-md-1 control-label">Property (<i class="fa fa-asterisk ipd-star" style="font-size:7px;"></i>) </label>
                                                    <div class="col-md-3">
                                                        <div class="input-icon right">
                                                            @php  $omr_script_properties->prepend('Select Omr Script', ''); @endphp
                                                            {!! Form::select('omr_script_property_id[]',$omr_script_properties, old('omr_script_property_id[]') ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                                        </div>
                                                    </div>

                                                    <label class="col-md-1 control-label">Start Pos (<i class="fa fa-asterisk ipd-star" style="font-size:7px;"></i>) </label>
                                                    <div class="col-md-2">
                                                        <div class="input-icon right">
                                                            <input type="text" name="start_position[]" required value="{{ old('start_position[]') }}" class="form-control">
                                                        </div>
                                                    </div>

                                                    <label class="col-md-1 control-label">End Pos (<i class="fa fa-asterisk ipd-star" style="font-size:7px;"></i>) </label>
                                                    <div class="col-md-2">
                                                        <div class="input-icon right">
                                                            <input type="text" name="end_position[]" required value="{{ old('end_position[]') }}" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        @foreach($omr_script->properties as $k=>$property)
                                        <tr id="{{ $k }}">                                    
                                            <td>
                                                <div class="form-group">

                                                    <label class="col-md-1 control-label">
                                                        <span class="control-label span-minus remove"><i class="fa fa-minus-circle"> </i></span>
                                                        <span class="control-label span-plus add"><i class="fa fa-plus-circle"></i></span>
                                                    </label>                                            

                                                    <label class="col-md-1 control-label">Property (<i class="fa fa-asterisk ipd-star" style="font-size:7px;"></i>) </label>
                                                    <div class="col-md-3">
                                                        <div class="input-icon right">
                                                            @php  $omr_script_properties->prepend('Select Omr Script', ''); @endphp
                                                            {!! Form::select('omr_script_property_id[]',$omr_script_properties, $property->omr_script_property_id ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                                        </div>
                                                    </div>

                                                    <label class="col-md-1 control-label">Start Pos (<i class="fa fa-asterisk ipd-star" style="font-size:7px;"></i>) </label>
                                                    <div class="col-md-2">
                                                        <div class="input-icon right">
                                                            <input type="text" name="start_position[]" required value="{{ $property->start_position }}" class="form-control">
                                                        </div>
                                                    </div>

                                                    <label class="col-md-1 control-label">End Pos (<i class="fa fa-asterisk ipd-star" style="font-size:7px;"></i>) </label>
                                                    <div class="col-md-2">
                                                        <div class="input-icon right">
                                                            <input type="text" name="end_position[]" required value="{{ $property->end_position }}" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">SET OMR STATUS</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                    <div class="col-md-3">
                                        {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/omr-script') }}" class="btn btn-default">Cancel</a>
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

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $("body").on( "click", ".remove", function() {
                $("#"+$(this).closest('tr').prop('id')).remove();
            })

            $("body").on( "click", ".add", function() {
                var row = $(this).closest('tr').clone().insertAfter($(this).closest('tr'));
                row.attr("id",$(this).closest('table').find("tr").last().attr('id')+1);
            })


        })
    </script>


@endsection
