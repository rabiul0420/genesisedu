@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">


            <div class="col-md-offset-0">
                @if(Session::has('message'))
                    <div  style="margin-top: 25px;" class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                        <p> {{ Session::get('message') }}</p>
                    </div>
                @endif
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet">
                
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::open(['url'=>['complain-submit'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}


                        <div class="form-body">

                            <div class="panel panel-default" style="margin-top:0px">

                                <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                                    <div class="offset-md-1 py-1">
                                        <div class="institutes my-1">
                                            <div class="form-group">
                                                <?php $doctor_id = Auth::guard('doctor')->id();?>
                                                <label class="col-md-4 control-label mb-2"><a class="btn btn-warning">Create Issue</a></label>
                                                <label class="col-md-4 control-label mb-2"><a href="{{ url('view-reply') }}" class="btn btn-success">View Reply</a></label>
                                                <label class="col-md-4 control-label mb-2" style="font-weight:bold;"><p>Complain related to (Please select one) :</p></label>
                                                <div class="col-md-4">
                                                    <div class="input-icon right">

                                                        @foreach ($complain_relateds as $complain_related)
                                                            <div class="form-check">
                                                                <input class="form-check-input" style="font-size: 13px;" type="radio" name="complain_related_id" id="flexRadioDefault2"  value="{{ $complain_related->id }}" data-id= {{ $complain_related->id }}>
                                                                <label class="form-check-label" for="flexRadioDefault2">
                                                                    {{ $complain_related->name }}
                                                                </label>
                                                            </div>
                                                        @endforeach

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="other_option">

                                            </div>
                                            
                                            <div class="form-actions">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label"></label>
                                                    <div class="col-md-3">
                                                        <button id="submit" type="submit" class="btn btn-info" >Submit</button>
                                                        <a href="{{ url('my-profile') }}" class="btn btn-outline-info btn-default">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="all_complain">

                                            </div>

                                            

                                        </div>    
                                    </div>
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
    </div>
    <!-- END PAGE CONTENT-->

@endsection

@section('js')

    <script type="text/javascript">

    $(document).ready(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });

        $('[name ="complain_related_id"]').on('change',function(){
            var complain_related_id = $(this).val();
            var doctor_id = {{ $doctor_id }}; 

            $.ajax({
                type:"POST",
                url: '/complain-related-topics',
                dataType: 'HTML',
                data: {complain_related_id,doctor_id},
                success:function(data){
                    $('.other_option').html(data);
                    $('.all_complain').html(' ');

                    $('.batch_id_select').on('change',function(){
                        var batch_id = $(this).val();
                        var doctor_id = {{ $doctor_id }};
                        all_comment_show(complain_related_id, doctor_id , batch_id);
                    })
                }
            })

        })

        

        function all_comment_show(complain_related_id, doctor_id , batch_id=null){

            $.ajax({
                type:"GET",
                url: '/all-comment',
                dataType: 'HTML',
                data: {complain_related_id,doctor_id,batch_id},
                success:function(data){
                    $('.test').html(' ');
                    $('.all_complain').html(data);
                }
            })
        }

        
    })
    </script>




@endsection
