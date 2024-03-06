@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">
                <div class="panel panel-default pt-2">
                    <div class="panel_box w-100 bg-white rounded shadow-sm">
                        <div class="header text-center py-3">
                            <h2 class="h2 brand_color">{{ 'Payment Details' }}</h2>
                        </div>
                    </div>
                    @if(Session::has('message'))
                        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                            <p> {{ Session::get('message') }}</p>
                        </div>
                    @endif
                    <div class="span12" style="text-align:center; padding: 10px;">
                        <span>Please Click </span><a class="btn btn-success btn-sm span12" href="{{ url('doctor-admissions') }}">Admission form</a><span> if not filled yet. </span>
                    </div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                   <!-- Modal -->
                                    <div class="modal fade" id="myModal" role="dialog">
                                        <div class="modal-dialog">
                                        
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            
                                        </div>
                                        
                                        </div>
                                    </div>

                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Actions</th>
                                            <th>Reg. No.</th>
                                            <th>Year</th>
                                            <th>Session</th>
                                            <th>Course</th>
                                            <th>Batch</th>
                                            {{--<th>Admission Fee</th>--}}
                                            <th>Payable Amount</th>
                                            <th>Discount</th>
                                            <th>Due</th>                                         
                                        </tr>
                                        </thead>
                                        <tbody>
                                                
                                            @foreach ($course_info as $k=> $course)
                                                @if($course->is_trash=='0')
                                                @php $batch_status = $course->batch->status ?? null; @endphp 

                                                @if( $course->payment_status=='Completed' )
                                                    @php $due_amount = 0; @endphp
                                                @else
                                                    @php
                                                            $temp_name = \App\DoctorCoursePayment::select('*')

                                                            ->where('doctor_course_id', $course->id)->get();

                                                            $paid_amount=0;
                                                            $total_row=0;
                                                            foreach ($temp_name as $key => $paid){
                                                                $paid_amount=$paid_amount+$paid->amount;
                                                                $total_row=$total_row+1;
                                                            }
                                                            $paid_amount;
                                                            $due_amount = $course->course_price-$paid_amount;(int)$course->discount;

                                                    @endphp

                                                @endif

                                                <tr>  
                                                    <td>{{ $k+1 }}</td>
                                                    <td>
                                                        @if(isset($course->batch->id))
                                                            @if($course->payment_status == 'Completed')
                                                                {{-- <span class="btn btn-sm btn-primary" style="background-color:Green;">Paid</span> --}}
                                                            
                                                                <a href="{{ 'payment-details-pdf/' . ($course['id']) }}" class="btn btn-success btn-sm {{ $course->batch->status == 0 ? 'disabled':'' }}">Download Invoice</a>
                                                            @else
                                                                @if($course->course_price!=$paid_amount)
                                                                    @if($course->batch->payment_times > 1)
                                                                    <a id="pay_now" class="btn btn-sm btn-primary {{ $batch_status == 0 ? 'disabled':'' }} " 
                                                                    <?php echo ($course->paid_amount() <= 0 || $course->paid_amount() == null)?'data-toggle="modal" data-target="#myModal"':'href="'.$course->payment_href().'"'; ?>
                                                                     
                                                                    data-doctor-course-id="{{$course->id}}" data-doctor-course-payment-option="{{ $course->payment_option }}" data-batch-payment-times="{{$course->batch->payment_times}}" data-doctor-course-paid-amount="{{ $course->paid_amount() }}">Pay Now </a>
                                                                    @else
                                                                    <a id="pay_now" class="btn btn-sm btn-primary {{ $batch_status == 0 ? 'disabled':'' }} "  href="{{url('payment/'.$course['id'])}}" data-doctor-course-id="{{$course->id}}" data-batch-payment-times="{{$course->batch->payment_times}}">Pay Now</a>
                                                                    @endif
                                                                    <div class='modal fade' id='myModal_{{$course->id}}' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
                                                                        <div class='modal-dialog' role='document'>
                                                                            <div class='modal-content'>
                                                                                <div class='modal-header'>
                                                                                    <h4 class='modal-title' id='myModalLabel'>Pay Now</h4>
                                                                            </div>
                                                                                <div class='modal-body'>
                                                                                    {!! Form::open(['url'=>['https://banglamedexam.com/user-login-sif-payment'],'method'=>'get','files'=>true,'class'=>'form-horizontal']) !!}
                                                                                    <h4>Payable Amount : {{$course->course_price}}</h4>
                                                                                    <h5>Paid Amount : {{$paid_amount}}</h5>
                                                                                    <h5>Due Amount : {{$course->course_price-$paid_amount}}</h5>
                                                                                <!-- <h5>Minimum Payable : {{$course->course_price/100*$course->batch->minimum_payment}} ({{$course->batch->minimum_payment}}%)</h5><br> -->
                                                                                    <input type="hidden" name="name" required value="{{$doc_info->name}}">
                                                                                    <input type="hidden" name="password" required value="123456">
                                                                                    <input type="hidden" name="email" required value="{{$doc_info->email}}">
                                                                                    <input type="hidden" name="bmdc" required value="{{$doc_info->bmdc_no}}">
                                                                                    <input type="hidden" name="phone" required value="{{$doc_info->mobile_number}}">
                                                                                    <input type="hidden" name="doctor_id" required value="{{$doc_info->id}}">
                                                                                    <input type="hidden" name="regi_no" required value="{{$course->reg_no}}">
                                                                                    <input type="hidden" name="doctor_course_id" required value="{{$course->id}}" class="form-control">
                                                                                    @if ($total_row==0)
                                                                                        <input type="number" name="amount" required value="{{$course->course_price-$paid_amount}}" class="form-control"
                                                                                            min="{{$course->course_price/100*$course->batch->minimum_payment}}" max="{{$course->course_price-$paid_amount}}"><br>
                                                                                        <input type="hidden" name="payment_serial" required value="1">
                                                                                    @elseif ($course->batch->payment_times == $total_row+1)
                                                                                        <input type="number" name="amount" required value="{{$course->course_price-$paid_amount}}" class="form-control"
                                                                                            min="{{$course->course_price-$paid_amount}}" max="{{$course->course_price-$paid_amount}}"><br>
                                                                                        <input type="hidden" name="payment_serial" required value="{{$total_row+1}}">
                                                                                    @else
                                                                                        <input type="number" name="amount" required value="{{$course->course_price-$paid_amount}}" class="form-control"
                                                                                            min="10" max="{{$course->course_price-$paid_amount}}"><br>
                                                                                        <input type="hidden" name="payment_serial" required value="{{$total_row+1}}">
                                                                                    @endif
                                                                                    <input type="submit" value="Submit" class="btn btn-xm btn-primary">
                                                                                    {!! Form::close() !!}
                                                                                </div>
                                                                                <div class='modal-footer'>
                                                                                    <button type='button' class='btn btn-sm bg-red' data-dismiss='modal'>Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <span class="btn btn-sm btn-primary" style="background-color:Green;">Paid</span>
                                                                @endif

                                                            @endif

                                                        @else
                                                            No full Data
                                                        @endif
                                                        
                                                    </td>
                                                    <td>{{ $course->reg_no }}</td>
                                                    <td>{{ $course->year }}</td>
                                                    <td>{{ (isset($course->session->name))?$course->session->name:'' }}</td>
                                                    <td>{{ (isset($course->course->name))?$course->course->name:'' }}</td>
                                                    <td>
                                                        {{ ( isset( $course->batch->name))?$course->batch->name: '' }}

                                                        @if ( isset( $course->batch->status ) && $course->batch->status == 0 )
                                                            <span class="badge bg-danger">Batch Inactive</span>
                                                        @endif
                                                    
                                                    </td>
                                                    {{--<td>{{ (isset($course->actual_course_price))?$course->actual_course_price:'' }}</td>--}}
                                                    <td>{{ (isset($course->course_price))?$course->course_price:'' }}</td>
                                                    <td>{{ $course->total_discount()??"" }}</td>
                                                    <td>
                                                        {{ $due_amount }}
                                                    </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    
                                    <?php
                                        if(isset($last_reg->payment_status)){

                                            if (($last_reg->payment_status!='Completed') && (isset($last_reg_pay->minimum_payment)) && (substr($last_reg->created_at,0,10)==date('Y-m-d'))) {
                                                echo "<div class='alert alert-danger'>You must pay your total course fee within 24 hours. Otherwise your submitted form will be deleted automatically and you need to submit admission form again. You may not get admitted in the batch you selected previously during form fill up if the batch capacity become full by this time.</div>";
                                            }
                                        }
                                    ?>
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

<script type="text/javascript">

                                        
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $("body").on("click", "#pay_now", function(e) {
                var batch_payment_times = $(this).data('batch-payment-times');
                if(batch_payment_times > 1)
                {   
                    var doctor_course_id = $(this).data('doctor-course-id');
                    var paid_amount = $(this).data('doctor-course-paid-amount');
                    var payment_option = $(this).data('doctor-course-payment-option'); 
                    if(paid_amount<=0)
                    {
                        $('#myModal .modal-content').load('/get-full-payment-waiver',{doctor_course_id : doctor_course_id, _token: '{{ csrf_token() }}'},function(){
                            $('#myModal').modal({show:true});
                        });
                        e.preventDefault();
                    }
                    

                }
               
            });

            $("body").on("change", "[name='payment_option']", function() {
                var doctor_course_id = $('#id_submit').data('doctor-course-id');
                var payment_option = $(this).val();               

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/set-payment-option',
                    dataType: 'HTML',
                    data: {payment_option : payment_option, doctor_course_id : doctor_course_id },
                    success: function( data ) { 
                        if(data == success)
                        {
                            console.log('payment option success');
                        }
                        
                    }
                });
                
                if (this.value == 'single') {
                    $("#id_submit").attr("href", window.location.origin+'/payment/'+doctor_course_id);
                    $('#id_submit').removeClass("disabled").css("background-color",'green');
                }
                else if (this.value == 'default' ||  this.value == 'custom') {
                    $("#id_submit").attr("href",  window.location.origin+'/installment-payment/'+doctor_course_id);
                    $('#id_submit').removeClass("disabled").css("background-color",'green');
                }
            });

        });

</script>

@endsection
