@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Conversation List</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ Session::get('class') ?? 'alert-success' }} " role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Conversation List Create
                    </div>
                </div>
                
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\ConversationSmsController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Doctor Name(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <select name="mobile_number"  required class="form-control doctor2"></select>
                                </div>
                            </div>
                        </div>    

                        <div class="form-group">
                            <label class="col-md-3 control-label">Question Tittle</label>
                            <div class="col-md-4">                                
                                <div class="controls">
                                    @php  $question_tittles->prepend('Select Tittle', ''); @endphp
                                    {!! Form::select('title_id',$question_tittles, old('title_id'),['class'=>'form-control select2','id'=>'title_id']) !!}<i></i>
                                 </div>                           
                            </div>
                        </div>
                        <div class="question_link">
                            
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">SMS</label>
                            <div class="col-md-4">                                
                                <div class="controls">
                                    <textarea class="form-control" name="short_sms" id="short_sms" cols="30" rows="4"></textarea>
                                </div>                           
                            </div>
                        </div>
                        
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('') }}" class="btn btn-default">Cancel</a>
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

    $(document).ready(function(){
        $('.doctor2').select2({
            minimumInputLength: 3,
            tags: true,
            placeholder: "Please type doctor's name or phone number",
            escapeMarkup: function (markup) { return markup; },
            ajax: {
                url: '/admin/search-doctors-phone',
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
                            return { id:item.mobile_number , text: item.name_phone };
                        })
                    };
                }
            }
        });

        $("body").on( "change", "[name='title_id']", function() {
            var title_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/question-link',
                dataType: 'HTML',
                data: {title_id : title_id},
                success: function( data ) {
                    $('.question_link').html(data);
                }
            });
        })

        $('#title_id').select2();

    });
       
    </script>

@endsection
