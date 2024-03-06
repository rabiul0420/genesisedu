@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" >
        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                {{-- <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'Conversation' }}</h2>
                    </div>
                </div> --}}
                <div class="panel-body">
                    @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                    @endif

                    <div class="col-md-12 border shadow-sm mt-2" style="background: #d5d7d8;">
                        <div class="portlet" style="height: 200px; overflow:auto;">
                            <div class="portlet-body p-3 complain-details">
                                @foreach($complain_details as $key => $complain)
                                <div class="w-100 rounded-lg my-2 {{ $complain->user_id!=0?'pl-5 text-right':'pr-5' }}">
                                    <span class="border image"
                                        style="font-size: 14px; display: inline-block; border-radius: 15px; padding:15px; margin-top: 5px;
                                        background-color:{{($complain->user_id!=0)?'#FFFFFF':'#024dbcbc'}}; color:{{($complain->user_id!=0)?'#444':'#fff'}};">
                                        {!! $complain->message !!}
                                    </span><br>
                                    <span
                                        style="font-size: 10px; padding: 0 8px;">{{ date('d M Y h:m a',strtotime($complain->created_at)) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <!-- <h4>Type Complain</h4> -->
                        {!!
                        Form::open(['url'=>['complain-again-new'],'method'=>'post','files'=>true,'class'=>'form-horizontal'])
                        !!}
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="input-icon right">
                                        <textarea class="form-control shadow-none" placeholder="Write your complain ..."
                                            name="description" required style="height:150px"></textarea>
                                        <input type="hidden" name="complain_id" value="{{$complain_id}}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row mx-0">
                                    <div class="text-right">
                                        <button type="submit" class="btn my-2 btn-info">Submit</button>
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

            var messageBody = document.querySelector('.portlet');
            messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
            // DO NOT REMOVE : GLOBAL FUNCTIONS!
            $(document).ready(function () {
                CKEDITOR.replace('description');
                // $('.select2').select2();
            })
        </script>
        <!-- data -->
        @endsection