@extends('layouts.app')


@section('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <style>
        .class-details {
            color: gray;
            border: none;
            background: none;
            outline: none;
        }

        .class-details:hover {
            color: green;
        }

    </style>

@endsection
@section('content')

    <div class="container">
        <div class="row">
            @include('side_bar')
            <div class="col-md-9 col-md-offset-0">

                <div class="row panel panel-default">
                    <div class="row col-md-12">
                    {!! Form::open(['url' => 'payment-manual-save', 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) !!}

                        <div class="panel-body mt-3 rounded shadow-sm border bg-white ">

                            <div class="offset-md-1 py-4">
                                <div class="my-1">                                    
                                    Dear Dr. {{ $doctor_course->doctor->name ?? '' }} ,<br><br>
                                    Thank you for your batch fee payment effort. We are accepting Bkash payment. <br><br> Please make "<b>PAYMENT</b>" (not "send money") to <b>{{ $doctor_course->course->bkash_marchent_number ?? '' }}</b> .<br><br><b>NB :</b> You don't need to add extra money for cashout charge.

                                    
                                </div>
                            </div>
                            <div class="offset-md-1 py-4">
                                <div class="my-1">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Doctor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-6">
                                            <div class="input-icon right">
                                                <input type="hidden" name="doctor_course_id" value="{{ $doctor_course->id }}" >
                                                <span style="font-size:15px;font-weight:700;">{{ $doctor_course->doctor->name.'-'.$doctor_course->reg_no }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Amount (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-6">
                                            <div class="input-icon right">
                                                <input type="hidden" name="amount" value="{{ $doctor_course->installment_payable_amount() }}" readonly min="0" max="{{ $doctor_course->installment_payable_amount() }}">
                                                <span style="font-size:15px;font-weight:700;">{{ $doctor_course->installment_payable_amount() }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Trx ID (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-6">
                                            <div class="input-icon right">
                                                <input type="text" name="trans_id" class="form-control" placeholder="Enter BKash TrxID" value="" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-action">
                                <div class="form-group offset-md-1">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-3">
                                        <button id="submit" type="submit" class="btn btn-info" >Submit</button>
                                        <a class="btn btn-primary" href="{{ url('payment-details') }}">Cancel</a>                                        
                                    </div>
                                    
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
 
@endsection
