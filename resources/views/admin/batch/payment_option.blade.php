@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>{{$title}}</li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <style>
        
    </style>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{$title}}
                        
                        <a href="{{ url('admin/batch/'.$batch->id.'/edit') }}" class="btn btn-xs btn-success"> Change Payment Times</a>
                        
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\BatchController@payment_option_save'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <input type="text" name="batch" value="{{ $batch->name }}" disabled>
                                    <input type="hidden" name="batch_id" value="{{ $batch->id }}" >
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Payment times (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <input type="text" name="installment_times" value="{{ $batch->payment_times }}" disabled>
                                </div>
                            </div>
                        </div>

                        @for($k = 1,$l = 0; $k <= $batch->payment_times; $k++,$l++)
                        @php $suff = ($k==1)?'st':(($k==2)?'nd':(($k==3)?'rd':'th')); @endphp
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{ $k.$suff }} Installment Last date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input type="text" id="payment_date[{{$k}}]" name="payment_date[{{$k}}]" value="{{ $batch->payment_options[$l]->payment_date ?? '' }}" class="form-control input-append date" autocomplete="off" required>
                                </div>
                            </div>

                            <label class="col-md-3 control-label">{{ $k.$suff }} Installment target amount (%) (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input type="number" id="amount[{{$k}}]" name="amount[{{$k}}]" value="{{ $batch->payment_options[$l]->amount ?? '' }}" required>
                                </div>
                            </div>
                        </div>
                        @endfor          

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/batch') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->

@endsection

@section('js')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function() {

            $("body").on( "focus", ".date", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    startDate: '',
                    endDate: '',
                }).on('changeDate', function(e){
                    $(this).datepicker('hide');
                });
            })

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-course',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.course').html(data);
                        $('.faculty').html('');
                        $('.subject').html('');
                    }
                });
            })


            $("body").on( "change", "[name='faculty_id']", function() {
                var faculty_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/faculty-subject',
                    dataType: 'HTML',
                    data: {faculty_id: faculty_id},
                    success: function( data ) {
                        $('.subject').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='fee_type']", function() {
                var fee_type = $(this).val();
                if(fee_type == "Discipline_Or_Faculty")
                {
                    $("[name='admission_fee']").prop("disabled", true);
                    $("[name='lecture_sheet_fee']").prop("disabled", true);
                    $("[name='discount_from_regular']").prop("disabled", true);
                    $("[name='discount_from_exam']").prop("disabled", true);
                }
                if(fee_type == "Batch")
                {
                    $("[name='admission_fee']").prop("disabled", false);
                    $("[name='lecture_sheet_fee']").prop("disabled", false);
                    $("[name='discount_from_regular']").prop("disabled", false);
                    $("[name='discount_from_exam']").prop("disabled", false);
                }
            });

            $("body").on("change", "[name='package_id']", function(){
                var package_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/shipment',
                    dataType: 'HTML',
                    data: {package_id: package_id},
                    success: function( data ) {
                        $('.shipments').html(data);
                    }
                });
            });
        })
    </script>

@endsection