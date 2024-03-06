{{-- 

<div class="row">
    <div class="col-md-9">
        <div class="panel-default pt-2" style="padding: 5px;">
            <div class="panel_box w-100 bg-white rounded">
                <div class="text-center" style="height: 30px">
                    <h2 class="h2 brand_color">{{ 'Previous Conversation' }}</h2>
                </div>
            </div>
            <div class="panel-body">
                @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
                @endif --}}

                
                @if ($complains)
                <h2 class="text-center brand_color my-2">Previous Conversation</h2>
                <div class="col border shadow-sm" style="background: #d5d7d8;">
                    <div class="portlet custom" style="height:200px; overflow:auto; ">
                        <div class="portlet-body p-3 complain-details">
                            @foreach($complains as $complain_single)

                                @foreach ($complain_single->complain_reply as $key=>$complain)
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
                                
                            @endforeach
                            
                        </div>
                    </div>
                </div>
            @endif
                
{{--                 
            </div>
        </div>
    </div>
</div> --}}

<script>

    // var messageBody = document.querySelector('.custom');
    // console.log(messageBody)
    // messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;

</script>



        