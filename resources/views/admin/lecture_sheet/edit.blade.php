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
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\LectureSheetController@update',$lecture_sheet->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ $lecture_sheet->name }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">PDF</label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="pdf_file" value="{{ $lecture_sheet->pdf_file }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Lecture Sheet Folder (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    @php  $lecture_sheet_topics->prepend('Select Lecture Sheet Folder', ''); @endphp
                                    {!! Form::select('lecture_sheet_topic_id[]',$lecture_sheet_topics, isset($selected_lecture_sheet_topics)?$selected_lecture_sheet_topics:'' ,['class'=>'form-control select2','multiple'=>'multiple','required'=>'required','id'=>'lecture_sheet_topic_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>


                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/lecture-sheet') }}" class="btn btn-default">Cancel</a>
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

            $('.select2').select2({ });




        })
    </script>


@endsection
