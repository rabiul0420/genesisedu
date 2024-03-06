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
                        {!! Form::open(['action'=>['Admin\CoursesController@update',$course->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">

                            <div class="form-group">
                                <label class="col-md-2 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" name="name" required value="{{ $course->name?$course->name:'' }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        <div class="form-body">

                            <div class="form-group">
                                <label class="col-md-2 control-label">Priority (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="number" name="priority" required value="{{ $course->priority?$course->priority:'' }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Course Detail</label>
                                <div class="col-md-10">
                                    <div class="input-icon right">
                                        <textarea id="course_detail" name="course_detail">{{ $course->course_detail ? $course->course_detail :'' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Course Code (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" name="course_code" required value="{{ $course->course_code ? $course->course_code : '' }}"  maxlength="2" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">BKash Marchent Number ( 11 digit )</label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" name="bkash_marchent_number" value="{{ $course->bkash_marchent_number ? $course->bkash_marchent_number : '' }}" maxlength="11" minlength="11"  pattern="[0-9]{11}" class="form-control">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-2 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php $institutes->prepend('Select Institute',''); @endphp
                                        {!! Form::select('institute_id', $institutes, $course->institute_id, ['class'=>'form-control', 'required'=>'required']) !!}<i></i>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-2 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $course->status,['class'=>'form-control']) !!}<i></i>
                                </div>
                            </div>
                    </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn btn-info"><?php echo $submit_value;?></button>
                                    <a href="{{ url('admin/courses') }}" class="btn btn-default">Cancel</a>
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

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $('.select2').select2();

        });

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
        CKEDITOR.replace( 'course_detail' );
            $("body").on( "change", "[name='year']", function() {
                var year = $(this).val();
                // alert(year);
                $.ajax({
                    type: "GET",
                    url: '/admin/session-course-search',
                    dataType: 'HTML',
                    data: {year : year },
                    success: function( data ) {
                         $('.session').html(data); 
                         $('.select2').select2();
                    }
                });
            })
        })
    </script>




@endsection
