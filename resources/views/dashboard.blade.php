@extends('layouts.app')

@section('content')

    <section id="profile_section" >
        <div class="container" >
            <div class="row">

                @include('side_bar')
                <div class="col-lg-7 col-sm-9 py-4">
                    <div class="panel_box w-100 bg-white rounded shadow-sm">
                        <div class="header text-center py-3">
                            <h2 class="h2 brand_color">Quick Links</h2>
                        </div>
                    </div>
                    <div class="mt-3">
                        @include('components.subscription_add')
                    </div>
                    <div class="quick_link_box w-100 msi-my-1">
                        <div class="header text-center">
                            <div class="row w-100 msi-my-1 mx-0 pb-4 px-0 gx-lg-4 gx-2 gy-lg-3 gy-2">
                                <a href="{{ url('schedule/master-schedule') }}" class="col-6 pl-0">
                                    <div class="img box-1"
                                        style="background: url({{ asset('images/image1.png') }});">
                                        <img src="{{ asset('images/schedule.png') }}" alt="">
                                        <h3>Schedule</h3>
                                        <h3>Lecture,Exam</h3>
                                    </div>
                                </a>
                                
                                <a href="{{ url('batch') }}" class="col-6 pl-0">
                                    <div class="img box-2"
                                        style="background: url({{ asset('images/image2.png') }});">
                                        <img src="{{ asset('images/available-batch.svg') }}" alt="">
                                        <h3>Available Batch</h3>
                                    </div>
                                </a>
                                
                                <a href="{{ url('complain-related') }}" class="col-6 pl-0">
                                    <div class="img box-4"
                                        style="background: url({{ asset('images/image6.png') }});">
                                        <img class="img-fluid" src="{{ asset('images/complain_box.png') }}" alt="">
                                        <h3>Complain Box</h3>
                                    </div>
                                </a>

                                <a href="{{ url('doctor-admissions') }}" class="col-6 pl-0">
                                    <div class="img box-6"
                                        style="background: url({{ asset('images/image3.png') }});">
                                        <img class="img-fluid" src="{{ asset('images/admission_form.png') }}"
                                            alt="">
                                        <h3>Admission Form</h3>
                                    </div>
                                </a>

                                <a href="{{ url('notice') }}" class="col-6 pl-0">
                                    <div class="img box-5"
                                        style="background: url({{ asset('images/image6.png') }}); position: relative">
                                        <img class="img-fluid" src="{{ asset('images/notice.png') }}" alt="">
                                        <h3>Notice</h3>
                                        <span class="text-white text-center bg-danger py-0 px-1 notice_count"
                                            style="{{ $count > 0 ? '' : 'display:none;' }}">{{ $count }}</span>
                                    </div>
                                </a>

                                <a target="_blank" href="http://medicalbooksonline.net" class="col-6 pl-0">
                                    <div class="img box-8"
                                        style="background: url({{ asset('images/image2.png') }})">
                                        <img class="img-fluid" src="{{ asset('images/genesis_publication.png') }}"
                                            alt="">
                                        <h3>Publication</h3>
                                    </div>
                                </a>
                              
                                <a href="{{ url('payment-details') }}"
                                    class="col-6 pl-0 {{ Request::segment(1) == 'payment-details' || Request::segment(1) == 'doctor-admission-submit' ? 'active' : '' }}">
                                    <div class="img box-7"
                                        style="background: url({{ asset('images/image1.png') }})">
                                        <img class="img-fluid zindex-fixed" src="{{ asset('images/monay2.png') }}"
                                            style="height: 70px;z-index:1111; ">
                                        <h3>Pay Now</h3>
                                    </div>
                                </a>

                                <a href="{{ url('my-profile') }}" class="col-6 pl-0">
                                    <div class="img box-3"
                                        style="background: url({{ asset('images/image3.png') }});">
                                        <img class="img-fluid" src="{{ asset('images/my-acc.svg') }}" alt="">
                                        <h3>My Account</h3>
                                    </div> 
                                </a>

                            </div>
                            
                            @if($doc_info != null)
                                @if (($doc_info->shipment == 1) ? $first_shipment->feedback == null : (($second_shipment == null) ? $second_shipment == null : $first_shipment->feedback == null || $second_shipment->feedback == null ))

                                <div class="modal" tabindex="-1" id="myModal">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order FeedBack</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            
                                            <div class="modal-body p-0">
                                                @include( 'components.my_order',compact('doc_info','first_shipment') )
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @endif

                            @if($doctor) 
                            <div class="modal" tabindex="-1" id="myModal2" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Enter Your Information</h5>
                                        </div>
                                        <div class="modal-body p-3">
                                            <form id = "docto_info" >
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-sm-4 col-form-label"></label>
                                                    <div class="col-sm-8">
                                                        <p id="message" style="color: red;font-size:15px;margin-bottom: 5px;"></p>
                                                    </div>
                                                  </div>
                                                <div class="form-group row mb-2">
                                                  <label for="inputPassword" class="col-sm-4 col-form-label text-left">BMDC NO</label>
                                                    <div class="col-sm-8 position-relative prefix">
                                                        <span class="position-absolute border-bottom text-white border-info bg-info"
                                                            style="padding: 9px 5px;left: 0px;">A
                                                        </span>
                                                        <input type="number" class="form-control" required id="bmdc_no" placeholder="BMDC NO">
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-2">
                                                    <label for="inputPassword" class="col-sm-4 col-form-label text-left">Medical College</label>
                                                    <div class="col-sm-8">
                                                        <select name="medicale_college_id" class="form-control medical2"  id="medicale_college_id">
                                                            <option class="form-control"  value="">Select Medical College</option>
                                                            @foreach ($medical_colleges as $key=>$medical_college)
                                                                <option  required class="form-control " value="{{  $key }}">{{ $medical_college }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                  <div class="form-group row mb-2">
                                                    <label for="inputPassword" class="col-sm-4 col-form-label text-left">Email Address</label>
                                                    <div class="col-sm-8">
                                                      <input type="email" class="form-control" required id="email" >
                                                    </div>
                                                  </div>

                                                  <div class="form-group row mb-2">
                                                    <label for="inputPassword" class="col-sm-4 col-form-label"></label>
                                                    <div class="col-sm-0">
                                                      <input type="submit" class="btn btn-primary" value="submit" name="submit"  id="submit">
                                                    </div>
                                                  </div>
                                            </form>                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($modal_add_image)
    <div hidden id="modal_add" style="position: fixed; top: 0; left: 0; z-index: 99999; width: 100%; height: 100%; background: #cccc; display: flex; justify-content: center; align-items: center;">
        <div style="max-width: 500px;">
            <div style="text-align: center; margin-bottom: 10px;">
                <b onclick="closeModalAdd()" style="background: #000; color: #fff; padding: 0px 16px 4px; cursor: pointer; font-size: 40px;">x</b>
            </div>
            <img style="width: 100%; height: auto;" src="{{ $modal_add_image }}"/>
        </div>
    </div>

    <script>
        const modalAddContainer = document.getElementById('modal_add');

        if(localStorage.getItem('read_modal_add') != `{{ $modal_add_image }}`) {
            modalAddContainer.hidden = false;
        }

        function closeModalAdd() {
            localStorage.setItem('read_modal_add', `{{ $modal_add_image }}`);
            modalAddContainer.hidden = true;
        }
    </script>
    @endif
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>  
    <script>
        $('#myModal').modal('show');

    </script>
    <script>
        $('#myModal2').modal('show');
        $('#myModal2').modal({backdrop: 'static', keyboard: false}) 
    </script>
    <script type="text/javascript">
            $(document).ready(function() {
                // $("#docto_info").on("submit","[id='docto_info']",function(){
                $("#docto_info").on("submit",function(e){
                    e.preventDefault();
                 var bmdc_no = $('#bmdc_no').val();
                 var medical_college = $('#medicale_college_id').val();
                 var email = $('#email').val();
                 const base_url = "{{ url('') }}";
                 $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/bmdc-email-medical',
                    dataType: 'JSON',
                    data: {bmdc_no : bmdc_no, medical_college: medical_college,email: email},
                    success: function( data ) {
                        console.log(data);
                        if(data.success){
                            window.location.reload(base_url + "dashboard");
                        }else{
                            $('#message').html(data.message);
                        }
                    }
                });
                    
                });

                $('.medical2').select2({
                    dropdownParent: $('#myModal2')
            });

            })

    </script>

@endsection
