@if ($batches)
<div class="form-group">
    <label class="col-md-4 control-label">Your Complain Related To Which Batch</label>
    <div class="col-md-4">
        <div class="input-icon right">
            <select class="form-control batch_id_select" name="batch_id" {{ ($complain_related_id == '1' ||   $complain_related_id == '2') ? 'required' : '' }} >
                <option value="">---Select Batch---</option>
                @foreach ($batches as $batch)
                    <option value="{{ $batch->id ?? ''  }}">{{ $batch->name ?? '' }}</option>
                @endforeach
                
            </select>
        </div>
    </div>
</div>
@endif

<div class="form-body">
    <div class="form-group">
        <div class="col-md-12">
            <div class="input-icon right">
                Type Complain : <br>
                <textarea style="margin-top: 10px;" name="description" value="" class="form-control" required></textarea>
            </div>
        </div>
    </div>
</div>

@if ($complains)
{{-- <h2 class="text-center brand_color my-2">Previous Conversation</h2> --}}
<div class="col-md-12 border shadow-sm mt-2 test" style="background: #d5d7d8">
    <div class="portlet custom" style="height: 200px; overflow:auto;">
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

<script>

    // var messageBody = document.querySelector('.custom');
    // console.log(messageBody)
    // messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;

</script>
