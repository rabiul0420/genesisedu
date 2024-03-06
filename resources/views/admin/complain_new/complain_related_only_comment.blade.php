<div class="form-group">
    <label class="col-md-3 control-label"> Type Complain : (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
    <div class="col-md-4">
        <div class="input-icon right">
            <textarea style="margin-top: 10px;" name="description" value="" class="form-control"></textarea>
        </div>
    </div>
</div> 
{{-- @if ($complains)
    
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2" style="padding: 5px;">
                <div class="panel_box w-100 bg-white rounded">
                    <div class="text-center"  style="height: 30px">
                        <h2 class="h2 brand_color">{{ 'Previous Conversation' }}</h2>
                    </div>
                </div>
                <div class="panel-body">
                    @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                    @endif

                    <div class="col-md-12 border shadow-sm mt-2" style="background: #d5d7d8">
                        <div class="portlet test" style="height: 200px; overflow:auto;">
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
                    
                </div>
            </div>
        </div>
        
@endif

<script>
    var messageBody = document.querySelector('.test');
     console.log(messageBody)
     messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
     
</script> --}}