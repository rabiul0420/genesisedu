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
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Edit
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
                        {!! Form::open(['action'=>['AppInfoController@update',$data->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Version (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="version" required value="{{ $data->version?$data->version:'' }}" class="form-control">
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-md-3 control-label">What's new (<i style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="whats_new" value="{{ $data->version?$data->whats_new:'' }}"  class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Play Store Link (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="play_store_ref" required value="{{ $data->version?$data->play_store_ref:'' }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Web Link (<i style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="web_ref" value="{{ $data->version?$data->web_ref:'' }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                    </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn btn-info"><?php echo $submit_value;?></button>
                                    <a href="{{ url('admin/app-info') }}" class="btn btn-default">Cancel</a>
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

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

               // $('.select2').select2();

        })
    </script>




@endsection
