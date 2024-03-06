@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo ' <li> <i class="fa fa-angle-right"> </i> <a href="'.url('/').substr_replace($urls, "", -1).' "> '.$value.' </a></li> ';
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
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>'Admin\CoursesController@store','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Priority (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="priority" required value="{{ old('priority') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Course Detail</label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <textarea id="course_detail" name="course_detail">{{ old('course_detail')?old('course_detail'):'' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Course Code (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="course_code" required value="{{ old('course_code') }}" maxlength="2" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">BKash Marchent Number ( 11 digit )</label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="bkash_marchent_number" value="{{ old('bkash_marchent_number') }}" maxlength="11" minlength="11" pattern="[0-9]{11}" class="form-control">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                        @php $institute->prepend('Select Institute',''); @endphp
                                        {!! Form::select('institute_id', $institute, old('institute_id'), ['class'=>'form-control', 'required'=>'required']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <label class="col-md-3 control-label">Session Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <select name="year" id="" class="form-control">
                                        <option value="">-- Select Year --</option>
                                        @for ($year = date('Y') - 1; $year <= date('Y') + 2; $year++)
                                        <option value="{{ $year }}">
                                            {{ $year }}
                                        </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div> --}}
                        <div class="session"></div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status')?old('status'):'1',['class'=>'form-control']) !!}<i></i>
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
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script type="text/javascript">
    
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


<script type="text/javascript">

</script>



@endsection
