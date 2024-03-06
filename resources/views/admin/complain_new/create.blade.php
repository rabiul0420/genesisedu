@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                Complain Create
            </li>
        </ul>
    </div>
    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))? Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Complain Create
                    </div>
                </div>
                
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <a href="{{ url('admin/complain/quick-register') }}" class="btn btn-primary btn-xl">Quick Registration</a>
                    {!! Form::open(['url'=>'admin/complain/store','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Doctor Name(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <select name="doctor_id"  class="form-control doctor2 doctor_id"></select>
                                </div>
                            </div>
                        </div> 
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Complain related to (Please select one) :(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">
                                <select name="complain_related_id" id="" class="form-control">
                                    <option value="">---Select Complain---</option>
                                    <option value="1">Lecture Video/Exam Solve Video</option>
                                    <option value="2">Exam Link</option>
                                    <option value="3">Publications (Lecture Sheet/Books)</option>
                                    <option value="4">Technical & Payment Issue</option>
                                    <option value="5">Others</option>
                                </select>
                                
                            </div>
                        </div>
                        <div class="other_option">

                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/discount') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->

@endsection

@section('js')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    
    

    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
            })

            
            $('.doctor2').select2({
                minimumInputLength: 3,
                placeholder: "Please type doctor's mobile number",
                escapeMarkup: function (markup) { return markup; },
                ajax: {
                    url: '/admin/search-doctors-complain',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: function (term) {
                        return {
                            term: term
                        };
                    },

                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return { id:item.id , text: item.name_bmdc };
                            })
                        };
                    }
                }
            });

            $('[name ="complain_related_id"]').on('change',function(){
            var complain_related_id = $(this).val();
            var doctor_id = $('.doctor_id').val(); 

            $.ajax({
                type:"POST",
                url: '/admin/complain-related-topics',
                dataType: 'HTML',
                data: {complain_related_id,doctor_id},
                success:function(data){
                    $('.other_option').html('');
                    $('.other_option').html(data);

                    // $('.batch_id_select').on('change',function(){
                    //     var batch_id = $(this).val();
                        
                    //     $.ajax({
                    //         type:"GET",
                    //         url: '/admin/all-comment',
                    //         dataType: 'HTML',
                    //         data: {complain_related_id,doctor_id,batch_id},
                    //         success:function(data){
                    //             $('.all_complain').html(data);
                    //         }
                    //     })

                    // })
                }
            })

        })
                      
            
    });





    </script>

@endsection