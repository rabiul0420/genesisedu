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
                        {!! Form::open(['action'=>['Admin\DiscountController@update',$discount->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">

                            <div class="form-group">
                                <label class="col-md-3 control-label">Discount Name</label>
                                <div class="col-md-4">
                                    <input type="text" name="discount_name" required value="{{ $discount->discount_name }}" class="form-control">
                                </div>
                            </div>

                            <div class="topic">

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Discount Type</label>
                                    <div class="col-md-4">
                                        {!! Form::select('discount_type', ['Cash' => 'Cash','Percentage' => 'Percentage'], $discount->discount_type,['class'=>'form-control']) !!}<i></i>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Amount</label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="text" name="amount" required value="{{ $discount->amount }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Discount Code</label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="text" name="discount_code" required value="{{ $discount->discount_code }}" class="form-control">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-3 control-label">Status</label>
                                <div class="col-md-4">
                                    {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $discount->status,['class'=>'form-control']) !!}<i></i>
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