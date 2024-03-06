@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">

            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ $ask_info->videoname->name }}</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                        <div class="col-md-12 col-md-offset-0" style="">

                            <div class="portlet">
                                <div class="portlet-body">

                                    @foreach($answer_info as $key => $answer)
                                        <div class="col-md-12" style=" padding:10px; margin: 2px;
                                        background-color:{{($answer->user_id!=0)?'#FFFFFF':'#E9E9E9'}};">
                                            {!! ($answer->user_id!=0)?'Replied'.'<br>( '.date('d M Y h:m a',strtotime($answer->created_at)).' )':'My Question'.'<br>( '.date('d M Y h:m a',strtotime($answer->created_at)).' )'   !!} :
                                            {!! $answer->message !!}
                                        </div>
                                    @endforeach

                                </div>
                            </div>

                        </div>

                        <div class="col-md-12" style="margin-top: 20px;">
                            <hr>
                            <div style="margin-bottom: 20px">
                                <h4 class="d-inline-block">Type Question</h4>
                                @if( \App\BatchesSchedules::back_url() )
                                    <a href="{{ \App\BatchesSchedules::back_url() }}" class="btn btn-success float-right">Back To Schedule</a>
                                @endif

                            </div>
                            {!! Form::open(['url'=>['question-again'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                                <div class="form-body">

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="input-icon right">
                                                <textarea name="description" required, placeholder="Type a question..." class = 'form-control 'style="margin-top: 10px; margin-bottom: 20px; height: 120px;"></textarea>
                                                <input type="hidden" name="ask_id" value="{{$ask_id}}">
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
