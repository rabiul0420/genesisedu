@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">

                <div class="panel panel-default pt-2">
                    <div class="panel_box w-100 bg-white rounded shadow-sm">
                        <div class="header text-center pt-3">
                            <h2 class="h2 brand_color">Payment</h2>
                        </div>
                    </div>

                    <div class="row col-md-12">
                        @if($amount || true)
                        <form onsubmit="return formValidation(event)" action="{{ route('manual-payments.subscription-order', $subscription_order->id, $amount) }}" method="POST" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                                <div class="offset-md-1 pt-4">
                                    <div class="my-1">                                    
                                        Dear <b>{{ $doctor->name ?? '' }}</b> ,<br><br>
                                        Thank you for your <b>Subscription</b> payment effort. We are accepting Bkash payment. <br><br> Please make "<b>PAYMENT</b>" (not "send money") to <b>01404432553</b> .<br><br><b>NB :</b> You don't need to add extra money for cashout charge.
                                    </div>
                                </div>
                                @if($subscription_order->manual_payments->count())
                                <div class="bg-success text-white p-2 my-3">
                                    <div class="offset-md-1">
                                        Thank you for your payment request. 
                                        <br />
                                        We will confirm your payment within <br> <b>3 hours</b> with confirmation SMS.
                                    </div>
                                </div>
                                @endif
                                <div class="offset-md-1 pb-4">
                                    <div class="my-1">
                                        <div class="py-2">
                                            Order No :
                                            <span style="font-size:15px;font-weight:700;">{{ $subscription_order->id }}</span>
                                        </div>

                                        <div style="border: 1px solid #ccc; width: 180px; padding: 8px 16px; border-radius: 16px; text-align: center; margin: 10px 0; background: #abc012a2;">
                                            Amount :
                                            <span style="font-size:15px;font-weight:700;">{{ $amount }} TK</span>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-6 control-label my-2">Your BKash Account Number (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <input type="number" id="account_no" name="account_no" class="form-control" placeholder="BKash Number" value="{{ $subscription_order->manual_payments[0]->account_no ?? '' }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-6 control-label my-2">Trx ID (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <input type="text" id="trans_id" name="trans_id" class="form-control" placeholder="TrxID" value="{{ $subscription_order->manual_payments[0]->trx_id ?? '' }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-danger pt-2" id="messageBox"></div>
                                    </div>
                                </div>

                                <div class="form-action">
                                    <div class="row mx-0 mb-4">
                                        <div class="offset-md-1 col-3 mr-5">
                                            <a class="btn btn-danger" href="{{ url('/my/subscriptions/orders/' . $subscription_order->id) }}">Cancel</a>                                        
                                        </div>
                                        <div class="col-3 ml-5">
                                            <button id="submit" type="submit" class="btn btn-success" >Submit</button>
                                        </div>
                                    </div>
                                </div>      
                                    
                            </div>
                    
                        </form>
                        @else
                        <div>
                            <div class="panel-body mt-3 rounded shadow-sm border bg-white p-4">
                                <div>
                                    Thank you for your payment request. 
                                    <br />
                                    <br />
                                    We will confirm your payment within <b>3 hours</b> with confirmation SMS.
                                </div>
                                <br />
                                <br />
                                <a class="btn btn-primary" href="{{ url('/my/subscriptions/orders/' . $subscription_order->id) }}">Back</a>                                        
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>    
    </div>

    <script>
        function formValidation(event) {
            let status = true;

            const trxId = document.getElementById('trans_id');
            const accountNo = document.getElementById('account_no');
            const messageBox = document.getElementById('messageBox');

            if(trxId.value.trim() === accountNo.value.trim()) {
                status = false;
                messageBox.innerHTML = 'Please type TrxId, not phone number.';
            }

            return status;
        }
    </script>

@endsection
