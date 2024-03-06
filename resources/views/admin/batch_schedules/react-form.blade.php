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
                Batches Schedules Create
            </li>
        </ul>
    </div>

    @if( Session::has( 'message' ) )
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {!! Session::get('message') !!}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12" id="application"></div>
    </div>

@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/datepicker.css') }}" />
    <link ref="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet">
@endsection
@section('js')

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script src="{{ asset('js/select2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/moment-with-locales.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/react.production.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/react-dom.production.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/babel.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>

    <script>

        let errors = JSON.parse( '{!! json_encode( Session::get('validation_errors') ?? [] )  !!}' );

        let editing_data = {};

        @if( isset( $batch_schedule ) && ( is_array( $batch_schedule ) || is_object( $batch_schedule )  ) )
            editing_data =  {!! json_encode( $batch_schedule ) !!} ;
        @endif



        @if( old() && ( is_array( old() ) || is_object( old() )  ) )
            const old = {!! json_encode( old() ) !!};
        @else
            const old = null;
        @endif
    </script>

    <script> const url = '{{url('')}}'; </script>
    <script> const schedule_id = '{{ $id }}'; </script>
    <script> const action = '{{ $action }}'; </script>


{{--    <script type="text/babel" src="{{asset('apps/schedule-manager.js')}}"></script>--}}
    <script type="text/javascript" src="{{asset('apps/schedule-manager/core.js')}}"></script>
    <script type="text/javascript" src="{{asset('apps/schedule-manager/app.js')}}"></script>

@endsection