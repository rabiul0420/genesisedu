@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Discount Edit</li>
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
                        <i class="fa fa-reorder"></i>Discount Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->

                        {!! Form::open(['action'=>['Admin\DiscountController@update',$discounts->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Doctor Name</label>
                                <div class="col-md-4">
                                    <input type="text" name="doctor_name" required value="{{ $discounts->name}}" class="form-control " readonly>
                                </div>
                            </div>

                             <div class="form-group">
                                <label class="col-md-3 control-label">Batch Name</label>
                                <div class="col-md-4">
                                    <input type="text" name="batch_name" required value="{{ $discounts->batch_name }}" class="form-control "readonly>
                                    
                                </div>
                            </div>



                            <div class="topic">

                            <div class="form-group">
                                <label class="col-md-3 control-label">Amount</label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="text" name="amount" required value="{{ $discounts->amount }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                           

                            <div class="form-group">
                                <label class="col-md-3 control-label">Code Duration <small>(Hour)</small></label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="number" name="code_duration" required value="{{ $discounts->code_duration }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $discounts->status,['class'=>'form-control','required'=>'required']) !!}<i></i>
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

        </div>
    </div>



@endsection

@section('js')

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {


            $("body").on( "change", "[name='ls_chapter_id']", function() {
                var ls_chapter_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/lschapter-topic',
                    dataType: 'HTML',
                    data: {ls_chapter_id: ls_chapter_id},
                    success: function( data ) {
                        $('.topic').html(data);
                    }
                });
            })

                $('.select2').select2();

        })
    </script>




@endsection