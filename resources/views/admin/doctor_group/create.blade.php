@extends('admin.layouts.app')
@section('doctor-group-class','active')
@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li></li>
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
                        <i class="fa fa-group"></i>
                        Doctor Special Group
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {{-- {!! Form::open(['action'=>['Admin\DController@store'],'files'=>true,'class'=>'form-horizontal']) !!} --}}

                    @if(($action??'create') == 'edit')
                        {!! Form::open(['action'=>['Admin\DoctorGroupController@update',$id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    @else
                        {!! Form::open(['action'=>['Admin\DoctorGroupController@store' ],'method'=>'POST','files'=>true,'class'=>'form-horizontal']) !!}
                    @endif

                        <div class=" col-12 col-lg-6">

                            {{ csrf_field() }}
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Select Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-5">
                                        {!! Form::select('year',$years, old( 'year', ($special_batch->year ?? '') ),['class'=>'form-control','required'=>'required', 'id'=>'year']) !!}<i></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Select Courses(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-5">
                                        @php  $courses->prepend('Select Courses', ''); @endphp
                                        {!! Form::select('course_id',$courses, old('course_id', ($special_batch->course_id ?? '')),['class'=>'form-control','required'=>'required', 'id'=>'course_id']) !!}<i></i>
                                    </div>
                                </div>

                                
                                <div class="session">

                                </div>
                                <div class="batch">
                                    @if ( isset( $selected_batches) )
                                        @include('admin.ajax.doctor_group_batch')
                                    @endif

                                    {{-- @include('admin.components.schedule_year_course_session') --}}

                                    @if( ($action ?? 'create') == 'edit' )

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Target Batch(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-5">
                                                <h3>
                                                    {{ $special_batch->batch->name }}
                                                </h3>
                                            </div>
                                        </div>

                                    @endif

                                </div>


                                <div class="form-group">
                                    <label class="col-md-4 control-label">Select Marks(Percent)(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="mark" 
                                        value="{{ old('mark', ($special_batch->average_obtained_mark_percent ?? '') ) }}" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Minimum Exam Attend(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="minimum_exam_attened" 
                                            value="{{ old('minimum_exam_attened', ($special_batch->minimum_exam_attentded ?? '') ) }}" >
                                    </div>
                                </div>

{{--                                <div class="form-group">--}}
{{--                                    <label class="col-md-4 control-label">Select Group(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>--}}
{{--                                    <div class="col-md-5">--}}
{{--                                        @php  $groups->prepend('Select Group', ''); @endphp--}}
{{--                                        {!! Form::select('group_id',$groups, old('group_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>--}}
{{--                                    </div>--}}
{{--                                </div>--}}




                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-4 col-md-8">
                                            <button type="submit" class="btn btn-info">Submit</button>
                                            <a href="{{ url('admin/batch') }}" class="btn btn-default">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class=" col-12 col-lg-6 px-4">
                            <h2>Matched Doctor List</h2>
                            <div class="table-responsive">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Bmdc No</th>
                                            <th>Batch</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td colspan="3">No Data</td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                        </div> --}}

                    {!! Form::close() !!}

                    {{-- {!! Form::close() !!} --}}
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
    
<script>
    $( document ).ready(function(){

        function loadMatchedData( page ){
            page = page || 1;

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: '/admin/doctor-matched-in-group',
                dataType: 'HTML',
                data: {year: year , course_id: course_id},
                success: function( data ) {
                    // $('.batch').html('');
                    $( '.batch' ).html( data );
                }
            })

        }



        $("body").on( "click", "#checkbox_batch", function() {
            if($("#checkbox_batch").is(':checked') ){
                $(".batch .select2 > option").prop("selected","selected");
                $(".batch .select2").trigger("change");
            }else{
                $(".batch .select2 > option").removeAttr("selected");
                $(".batch .select2").trigger("change");
            }
        });

        $("body").on("change","[name='course_id'],[name='year']", function(){
            var year =$('#year').val( );
            var course_id =$("[name='course_id']").val( );

            if( year &&  course_id ) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/doctor-group-search-course?prepend=false&action={{ $action ?? "create" }}',
                    dataType: 'HTML',
                    data: {year: year , course_id: course_id},
                    success: function( data ) {
                        // $('.batch').html('');
                        $('.batch').html(data);
                        $('.select2').select2();
                    }
                })
            }

        });

        $('.select2').select2();

    })
       
</script>
@endsection