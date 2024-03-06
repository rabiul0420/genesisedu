@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            <div class="col-md-9 col-md-offset-0">
            
            @if(Session::has('message'))
                <div  style="margin-top: 25px;" class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                    <p> {{ Session::get('message') }}</p>
                </div>
            @endif
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet" style="margin: 40px">
                <div class="portlet-title">
                    <div class="caption">
                        <!-- <i class="fa fa-reorder"></i>Doctor Course Create -->
                    </div>
                </div>
                <div class="portlet-body form">
                    <form action="{{ url('/lecture-sheet') }}" method="POST" class="form">
                        {{ csrf_field() }}
                        <div class="form-group">
                                <label class="col-md-3 control-label">Admit With Lecture Sheet/Books ?(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                                <div class="col-md-3" id="include_lecture_sheet">
                                    <label class="radio-inline">
                                        <input type="radio" name="include_lecture_sheet" required value="1"   > Yes
                                    </label>
                                </div>
                                <div class="col-md-3" id="include_lecture_sheet">
                                    <label class="radio-inline">
                                        <input type="radio" name="include_lecture_sheet" required  value="0"  > No
                                    </label>
                                </div>
                          
                            <input type="hidden" name="hidden_mobile_number"value="{{ $mobile_number }}">
                            <input type="hidden" name="hidden_schedule_id" value="{{ $schedule_id }}">
                        </div>
                        
                        <div class="delivery_status my-3">

                        </div>

                        <div class="courier_division my-3">

                        </div>

                        <div class="courier_district my-3">

                        </div>

                        <div class="courier_upazila my-3">

                        </div>

                        <div class="courier_address my-3" style="border:white;background-color:inherit;">

                        </div>
                          <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
            
    
    <!-- Modal -->
    


@endsection

@section('js')


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
        
        $(document).ready(function() {                
            
            $("body").on("change", "[name='include_lecture_sheet']", function () {
                var include_lecture_sheet = $(this).val();
                
                if(include_lecture_sheet == '1')
                {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/change-include-lecture-sheet',
                        dataType: 'HTML',
                        data: {include_lecture_sheet: include_lecture_sheet},
                        success: function( data ) {

                            $('.delivery_status').html(data);
                        }
                    });

                }
                else
                {
                    $('.delivery_status').html('');
                    $('.courier_division').html('');
                    $('.courier_district').html('');
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                }

            });

            $("body").on("change", "[name='delivery_status']", function () {
                var delivery_status = $(this).val();
                if(delivery_status == '1')
                {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/change-lecture-sheet-collection',
                        dataType: 'HTML',
                        data: {delivery_status: delivery_status},
                        success: function( data ) {
                            $('.courier_division').html(data);
                            $('.courier_district').html('');
                            $('.courier_upazila').html('');
                            $('.courier_address').html('');
                        }
                    });

                }
                else
                {
                    $('.courier_division').html('');
                    $('.courier_district').html('');
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                }

            });

            $("body").on( "change", "[name='courier_division_id']", function() {
                var courier_division_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/courier-division-district',
                    dataType: 'HTML',
                    data: {courier_division_id: courier_division_id},
                    success: function( data ) {
                        $('.courier_district').html(data);
                        $('.courier_upazila').html('');
                        $('.courier_address').html('');
                    }
                });
            });

            $("body").on( "change", "[name='courier_district_id']", function() {
                var courier_district_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/courier-district-upazila',
                    dataType: 'HTML',
                    data: {courier_district_id: courier_district_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.courier_upazila').html(data['upazilas']);
                        $('.courier_address').html(data['courier_address']);
                    }
                });
            });
        });

    </script>


@endsection
