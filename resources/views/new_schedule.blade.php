
@extends('layouts.app')
@section('content')

    <style>
        .class-or-exam-contents .btn {
            width: 100%;
        }



    </style>
<div class="container card-top">
  <div class="card mt-5 mb-5">

    <div class="navbar">
      <div class="container-fluid">
        <div>
            <p class="navbar-brand pt-4">Class & Exam Schedule</p>

            <a href="{{ route('full-schedule', [$schedule_id]) }}" class="btn btn-outline-info">Full Schedule at a glance</a>

            <button id="print" data-schedule-id="{{ $schedule_id }}" data-query-string="{{ request()->getQueryString() }}">
                <img src="{{asset('images/print-icon.jpeg')}}" style="width: 35px">
            </button>

            <button id="print_table" data-schedule-id="{{ $schedule_id }}" data-query-string="{{ request()->getQueryString() }}">
                <img src="{{asset('images/print-icon-table.png')}}" style="width: 35px">
            </button>
            
            <a href=""  class="{{ $schedule_links == null ? 'disabled' : ''}}" type="submit" data-bs-toggle="modal"  data-bs-target="#fb_modal" aria-labelledby="example">
                @include('batch_schedule.fb-icon')
            </a>


        </div>

        <form class="top-form" id="search-form">

          <input class="form-control " name="date" style="margin-right: 10px"
                 onchange="document.getElementById('search-form').submit()"
                 placeholder="Date"
                 value="{{ $_GET['date'] ?? '' }}" type="search" aria-label="Search" id="schedule-date">

          <input class="form-control " name="text"  style="margin-right: 10px"
                 placeholder="Class/Exam/Mentor"
                 value="{{ $_GET['text'] ?? '' }}" type="search" aria-label="Search">

          <button class="btn btn-top" type="submit">Search</button>
        </form>



      </div>
    </div>

    <hr>


    @foreach ( $scheduleTimeSlots as $time_slot )

        @if( isset( $time_slot->schedule_details ) && $time_slot->schedule_details->count()  )
    
            <div class="container-fluid class-or-exam-contents">

                <div class="row" style="margin-bottom: 25px;" >

                    <div class="col-10 col-md-8 col-lg-5">
                        <span class="col-12 col-md-5 col-lg-3"> <span class="badge bg-secondary">Date</span> {{  $time_slot->datetime->format('l, d-M-Y') }}</span> {{-- 'l, d-M-Y' --}}
                        <span class="col-12 col-md-3 col-lg-3 float-right"> <span class="badge bg-secondary">Time</span> {{  $time_slot->datetime->format('h:i A') }}</span> {{-- 'l, d-M-Y' --}}
                    </div>

                    <div class="col-2 col-md-3 col-lg-1">
                        <button class="reload-time-slot float-right" style="outline: none; border: none"
                            id="{{ $time_slot->id }}_time_slot_reload_btn"
                            data-slot-id="{{ $time_slot->id }}"
                            data-target="#{{ $time_slot->id }}_time_slot_container"
                            data-doctor-course-id="{{ $doctor_course_id }}">
                            <img src="{{asset('images/reload-icon.png')}}" style="height: 25px ">
                        </button>
                    </div>

                </div>

                @include( 'batch_schedule.time-slot', compact( 'time_slot', 'doctor_course_id' ) )
                <hr class="mt-4 bottom">
            </div>

        @endif
    @endforeach

{{--<pre>{{ print_r( $scheduleTimeSlots->toArray() ) }}</pre>--}}

<div class="container-fluid">  
    <div class="mt-3 next pb-3">
        @if( $scheduleTimeSlots->previousPageUrl() )
            <a href="{{ $scheduleTimeSlots->previousPageUrl() }}" class="btn next-btn">Previous Page</a>
        @endif
        @if( $scheduleTimeSlots->nextPageUrl() )
            <a href="{{ $scheduleTimeSlots->nextPageUrl(['a' => 8]) }}" class="btn next-btn">Next Page</a>
        @endif
    </div>
</div>

<div class="modal fade" id="rate-feedback-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Ratings on lecture video</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>

          <div class="modal-body" style="background-color: #f0fdf8">
              <form id="ratting-submit-form" method="post" action="" style="min-height: 180px;position: relative">

                <div style="position: absolute; left: 0;right: 0; top:0; bottom: 0; display: flex; justify-content: center; align-items: center">
                  <span>Loading...</span>
                </div>

              </form>
              <div style=" font-size: 12px; color: green" id="modal-message"></div>
          </div>

          <div class="modal-footer">
              <a href="javascript:void(0)" class="btn btn-secondary" id="close-button" data-bs-dismiss="modal">Close</a>
              <button type="button" class="btn btn-primary" id="submit-button">Submit</button>
          </div>
      </div>
  </div>
</div>


<div class="modal fade" id="fb_modal" tabindex="-1" aria-labelledby="example" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Social Media Links</h5>
          <button type="button" class=" btn btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <table class="table table-bordered">
              <thead>
                <tr>
                    <th>Title</th>
                    <th>Link</th>
                </tr>
              </thead>
              <tbody>
                @if ($schedule_links != null)
                  @foreach ($schedule_links as $schedule_link)
                    <tr>
                        <td>{{ $schedule_link->title }}</td>
                        <td><a target="_blank" href="{{ $schedule_link->link }}" style="background-color:#ff9900; color:#404040;" class="btn  btn-sm" role="button">Go To Link</a></td>
                    </tr>
                  @endforeach
                @else
                    <tr>
                        <td colspan="10">No Links Found</td>
                    </tr>
                @endif
              </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
    
    
@endsection


@section( "css" )
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

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


        #print {
            margin-left: 15px;
            border: none;
            outline: none;
            background: none;
            opacity: 0.8;
        }

        #print:hover {
            opacity: 1;
        }

        #print:focus {
            outline: none;
        }
        #print_table {
            margin-left: 5px;
            border: none;
            outline: none;
            background: none;
            opacity: 0.8;
        }

        #print_table:hover {
            opacity: 1;
        }

        #print_table:focus {
            outline: none;
        }

    </style>

@endsection

@section("js")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script>

        $('#schedule-date').datepicker({
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            zIndexOffset: 500000000
        });

 


        let loading = '<div style="position: absolute; left: 0;right: 0; top:0; bottom: 0; display: flex; justify-content: center; align-items: center">';
            loading += '<span>Loading...</span>';
        loading += '</div>';

        let thankyou = '<div style="position: absolute; left: 0;right: 0; top:0; bottom: 0; display: flex; justify-content: center; align-items: center">';
        thankyou += '<span style="color: green; font-size: 18px">Thank you for your feedback</span>';
        thankyou += '</div>';

        let watchFullClass = '<div style="position: absolute; left: 0;right: 0; top:0; bottom: 0; display: flex; justify-content: center; align-items: center">';
        watchFullClass += '<span style="color: green; font-size: 18px">Did you watch the full class?</span>';
        watchFullClass += '</div>';





        $(document).ready( function (){

            function reload_slot( btn ){

                $( btn ).attr( 'disabled', true );
                $( btn ).css( 'opacity', 0.5 );

                const target= $(btn).data('target');
                const slot_id = $(btn).data( 'slot-id' );
                const doctor_course_id= $(btn).data( 'doctor-course-id' );


                if( $(target).length ) {
                    $(target).css( 'opacity', 0.5 );

                    $(target).load( '/new-schedule-single/'+slot_id+'/'+doctor_course_id, function () {

                        $(btn).attr( 'disabled', false );
                        $(target).find( '.rate-feedback' ).each( each_rate_feedback );
                        $( btn ).css( 'opacity', 1 );
                        $( target ).css('opacity', 1 );
                    });

                }
            }

            $('.reload-time-slot').each( function (i, btn) {
                $(this).on('click', function (e){
                    e.preventDefault();
                    reload_slot( btn );
                });
            });

            function focusUnselectedCriteria(){
                console.log('OK');

                $( '.criteria-list' ).each( function (){
                    const checked_input = $(this).find("input[type='radio']:checked");
                    const criteria_label = $(this).find( ".title" );

                    if( checked_input.length == 0 ) {
                        criteria_label.css( 'color', 'magenta' );
                    }else {
                        criteria_label.css( 'color', '#212529' );
                    }
                });
            }


            var rate_feedback_modal = new bootstrap.Modal( document.getElementById('rate-feedback-modal'), { keyboard: false} )

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            let slot_id, details_id, batch_id = "{{ $batch_id }}";

            document.getElementById('rate-feedback-modal').addEventListener('hide.bs.modal', function (){
                $( '#submit-button' ).attr( 'disabled', false ).off('click').on( 'click',handleFullClassWatch );
            })

            function handleSubmit(e){
                e.preventDefault();
                let num_of_criteria = 0;
                let num_of_defined_criteria = 0;
                let validation_passed = true;

                $FORM.find( '.criteria-list' ).each(function (){
                    const criteria = $(this).data('criteria');
                    const value = $(this).find( 'input[type="radio"]:checked' ).val();
                    // console.log( criteria, value );
                    if( value ) num_of_defined_criteria++;
                    num_of_criteria++;
                })

                focusUnselectedCriteria( );

                if( num_of_criteria != num_of_defined_criteria ) {
                    validation_passed = false;
                    $('#modal-message').css('color','magenta').html( 'Please check all criteria' );
                }

                // if( $( '#feedback-text' ).val( ).trim( ) == '' ) {
                //     validation_passed = false;
                //
                //     $( '#feedback-text' ).focus();
                //     $( '#feedback-text' ).css('border-color', 'red');
                //
                //     $('#modal-message').css('color','magenta').html( 'Please write a feedback about the class!' );
                // }

                if( validation_passed ){
                    console.log( 'validation_passed', validation_passed );
                    $( '#feedback-text').css('border-color', 'magenta');
                    $FORM.submit( );
                }
            }

            function handleFullClassWatch(){

                $( '#close-button' ).click( function (){
                    $( '#submit-button' ).attr( 'disabled', false ).off('click').on( 'click',handleFullClassWatch );
                    $( '#modal-message' ).html('' );
                });

                $( '#submit-button' ).attr( 'disabled', true );
                $FORM.html( loading );

                $FORM.load( '/doctor-ratting-modal?details_id=' + details_id + '&batch_id=' + batch_id, function ( ){
                    $( '#modal-message' ).css('color','green').html('Check all criteria and hit submit' );
                    $( '#close-button' ).html('Close').attr( 'disabled', true );
                    $( '#submit-button' ).html('Submit').attr( 'disabled', false ).off( 'click').on( 'click', handleSubmit );
                });
            }


            $( '#submit-button' ).on( 'click', handleFullClassWatch );


            let $FORM  = $( '#ratting-submit-form' );

            $FORM.submit( function( e ){
                e.preventDefault() ;
                $( '#submit-button' ).attr( 'disabled', true );
                if( details_id ) {
                    const formData = new FormData( this );
                    formData.append( 'details_id', details_id );
                    formData.append( 'batch_id', batch_id );

                    $.ajax({
                        enctype : 'multipart/form-data',
                        type : "POST",
                        url : '/submit-doctor-ratting',
                        data : formData,
                        processData : false,
                        contentType : false,
                        dataType : "json",
                        success : function( data ) {

                            $( '#submit-button' ).attr( 'disabled', false ).off('click').on( 'click',handleFullClassWatch );
                            $FORM.html( thankyou );

                            $( '#modal-message' ).html('' );

                            if( slot_id && $('#' + slot_id + "_time_slot_reload_btn" ).length > 0 ) {

                                reload_slot( $( "#" + slot_id + "_time_slot_reload_btn" ) );

                            }else {
                                window.location.href = window.location.href;
                            }

                            setTimeout( () => {
                                rate_feedback_modal.hide( );
                            }, 1000 )
                        },
                        error: function (){
                            $( '#submit-button' ).attr( 'disabled', false ).on( 'click',handleFullClassWatch );
                        }
                    });
                }
            });


            function each_rate_feedback(){
                $( this ).on( 'click', function ( e ){
                    e.preventDefault( );

                    details_id = $( this ).data( 'detail-id' );
                    slot_id = $( this ).data( 'slot-id' );

                    rate_feedback_modal.show(  );

                    $FORM.html( watchFullClass );
                    $( '#close-button' ).html( 'No' ).attr( 'disabled', false );
                    $( '#submit-button' ).html( 'Yes' ).attr( 'disabled', false );

                    // End of form

                }); // End of onclick
            }

            $( '.rate-feedback' ).each( each_rate_feedback ); // End of Each
        });


        $('#print').click(function(){
            var schedule_id = $(this).data( 'schedule-id' );
            var params = '';
            if( $(this).data( 'query-string' ) ) {
                params = '?' + $(this).data( 'query-string' );
            }

            var pw = window.open( "/schedule-print/" + schedule_id + '' + params
                , "_blank"
                , "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1000,height=800" );
            pw.print( );
        });

        $('#print_table').click(function(){
            var schedule_id = $(this).data( 'schedule-id' );
            var params = '';
            if( $(this).data( 'query-string' ) ) {
                params = '?' + $(this).data( 'query-string' );
            }

            var pw = window.open( "/schedule-print-table/" + schedule_id + '' + params
                , "_blank"
                , "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1000,height=800" );
            pw.print( );
        });

    </script>
{{--      <script src="{{ asset('js/popper.min.js') }}"></script>--}}
{{--      <script src="{{asset('js/bootstrap.min.js')}}"></script>--}}

@endsection

