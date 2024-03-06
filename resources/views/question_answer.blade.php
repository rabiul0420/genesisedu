@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Question Answer</h3></div>

                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                        <div class="col-md-12 col-md-offset-0" style="">
                            
                            <div class="portlet">
                                <div class="portlet-body">
                                    
                                       <h4>Doctor Ask ID : {{ $doctor_ask_id }}</h4> 

                                        <div class="portlet-body form">
                                            <!-- BEGIN FORM-->
                                            {!! Form::open(['url'=>['question-submit-final'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                                                <div class="form-body">
                                                    
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <div class="input-icon right">
                                                                Type Question : <br>
                                                                <textarea name="description" required></textarea>
                                                                <input type="hidden" name="ask_id" value="{{$doctor_ask_id}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-actions">
                                                        <div class="row">
                                                            <div class="col-md-offset-0 col-md-9">
                                                                <button type="submit" class="btn btn-info">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                            {!! Form::close() !!}
                                            <!-- END FORM-->
                                            </div>
                                        </div>

                                </div>
                            </div>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection


@section('js')

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            CKEDITOR.replace( 'description' );
            // $('.select2').select2();
        })
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            $("body").on( "change", "[name='batch_id']", function() {
                var batch_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/batch-lecture',
                    dataType: 'HTML',
                    data: {batch_id : batch_id},
                    success: function( data ) {
                        $('.lecture').html(data);
                    }
                });
            })

        })
    </script>

@endsection