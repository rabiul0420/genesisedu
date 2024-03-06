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
                    {!! Form::open(['action'=>['Admin\PackageController@update',$package->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ $package->name }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Amount (BDT) (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input class="form-control" type="number" placeholder="1.00" step="0.01" min="0" max="10000000" required name="amount_bdt" value="{{ $package->amount_bdt ? $package->amount_bdt :'' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Amount (USD) (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input class="form-control" type="number" placeholder="1.00" step="0.01" min="0" max="10000000" required name="amount_usd" value="{{ $package->amount_usd ? $package->amount_usd :'' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Start Date (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="start_date" required value="{{ $package->start_date ? trim($package->start_date,"00:00:00") :'' }}" autocomplete="off"  class="form-control input-append date" id="datepicker">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">End Date (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="end_date" required value="{{ $package->end_date ? trim($package->end_date,"00:00:00") :'' }}" autocomplete="off"  class="form-control input-append date" id="datepicker2">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Description</label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <textarea id="description" name="description">{{ $package->description ? $package->description :'' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="exams">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Exam</label>
                                <div class="col-md-3">
                                    @php  $exams->prepend('Select Exam', ''); @endphp
                                    {!! Form::select('exam_id[]', $exams,  $selected_exams ,['class'=>'form-control select2','id'=>'exam_id','multiple'=>'multiple']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="institutes">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    @php  $institutes->prepend('Select Institute', ''); @endphp
                                    {!! Form::select('institute_id',$institutes, $package->institute_id ? $package->institute_id :'' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="courses">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $courses->prepend('Select Course', ''); @endphp
                                {!! Form::select('course_id',$courses, isset($package->course_id) ? $package->course_id : '',['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
                            </div>
                        </div>
                    </div>

                    @if(isset($package->institute->type))
                        @if($package->institute->type==1)
                            <div class="faculties">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Faculty (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                    <div class="col-md-3">
                                        @php  $faculties->prepend('Select Faculty', ''); @endphp
                                        {!! Form::select('faculty_id',$faculties, isset($package->faculty_id) ? $package->faculty_id : '' ,['class'=>'form-control','required'=>'required','id'=>'faculty_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>

                            <div class="subjects">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Discipline (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                    <div class="col-md-3">
                                        @php  $subjects->prepend('Select Discipline', ''); @endphp
                                        {!! Form::select('subject_id',$subjects, isset($package->subject_id) ? $package->subject_id : '',['class'=>'form-control', 'required' => 'required','id'=>'subject_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if(isset($package->institute->type))
                        @if($package->institute->type!=1)
                            <div class="subjects">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Discipline (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                    <div class="col-md-3">
                                        @php  $subjects->prepend('Select Discipline', ''); @endphp
                                        {!! Form::select('subject_id',$subjects, isset($package->subject_id) ? $package->subject_id : '',['class'=>'form-control', 'required' => 'required','id'=>'subject_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="form-group">
                        <label class="col-md-3 control-label">Select Status  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                        <div class="col-md-3">
                            {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $package->status ? $package->status :'',['class'=>'form-control']) !!}<i></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/package') }}" class="btn btn-default">Cancel</a>
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

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d',time()-86400*365) }}',
                endDate: '{{ date('Y-m-d',time() + 86400*365*2) }}',
            }).on('changeDate', function(e){
                $(this).datepicker('hide');
            });

            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d',time()-86400*365) }}',
                endDate: '{{ date('Y-m-d',time() + 86400*365*2) }}',
            }).on('changeDate', function(e){
                $(this).datepicker('hide');
            });

            CKEDITOR.replace( 'description' );

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-courses-in-package',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.courses').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $("[name='course_id']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-changed-in-package',
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.faculties').html(data);

                    }
                });
            });

            $("body").on( "change", "[name='faculty_id']", function() {
                var faculty_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/faculty-changed-in-package',
                    dataType: 'HTML',
                    data: {faculty_id: faculty_id},
                    success: function( data ) {
                        $('.subjects').html(data);
                    }
                });
            });

            $('.select2').select2({ });


        })
    </script>


@endsection

