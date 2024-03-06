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
                Discount Create
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
                        <i class="fa fa-reorder"></i>Discount Create
                    </div>
                </div>               
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\DiscountController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Doctor Name(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <select name="phone[]" multiple required class="form-control doctor2">
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="col-md-3 control-label">Year(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">                                
                                <div class="controls">
                                        {!! Form::select('year',$years, '' ,['class'=>'form-control year','required'=>'required','id'=>'year']) !!}<i></i>
                                 </div>                           
                            </div>
                        </div>                      
                        <div class="courses">

                        </div>
                        <div class="sessions">

                        </div>
                        <div class="batches">
                            
                        </div>                      
                        <div class="form-group">
                            <label class="col-md-3 control-label">Amount(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="amount" required value="{{ old('amount') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Code Duration <small>(Hour)</small> (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="code_duration" required value="{{ old('code_duration') }}" class="form-control">
                                </div>
                            </div>
                        </div>                     
                        <div class="form-group">
                            <label class="col-md-3 control-label">Reference Name(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="text" name="reference" required class="form-control">
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
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
            })

            $("body").on( "change", "[name='year']", function() { 
                var year = $(this).val();
    
                $.ajax({
                    type: "GET",
                    url: '/admin/course-search-by-year',
                    dataType: 'HTML',
                    data: {
                        year : year
                    },
                    success: function( data ) {
                        $('.courses').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='course_id']", function() { 
                var course_id = $(this).val();
                var year = $("[name='year']").val();
                $.ajax({
                    type: "POST",
                    url: '/admin/session-batch',
                    dataType: 'HTML',
                    data: {
                        course_id : course_id, year : year
                        },
                    success: function( data ) {
                        $('.sessions').html(data);
                        $('.batchs').html(data);

                    }
                });
            });

            $("body").on( "change", "[name='session_id']", function() { 
                var session_id = $(this).val();
                var course_id = $("[name='course_id']").val();
                var year = $("[name='year']").val();
                $.ajax({
                    type: "POST",
                    url: '/admin/discount-batch',
                    dataType: 'HTML',
                    data: {
                        session_id : session_id,
                        course_id : course_id,
                        year : year
                        },
                    success: function( data ) {
                        $('.batches').html(data);
                        $('#batch_id').select2();
                    }
                });
            });
            
            $('.doctor2').select2({
                minimumInputLength: 3,
                tags:true,
                placeholder: "Please type doctor's name or bmdc no or phone",
                escapeMarkup: function (markup) { return markup; },
                ajax: {
                    url: '/admin/search-doctors',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term
                        };
                    },

                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                let title = item.name + " - " + (item.bmdc_no || "") + " - " + (item.phone || "");
                                return { id:item.id , text: title };
                            })
                        };
                    }
                }
            });
                                 
    });
    </script>
@endsection