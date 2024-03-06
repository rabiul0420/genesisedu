@php $extend = request()->segment(1) == 'admin' ? 'admin.layouts.app':'layouts.app' @endphp

@extends( $extend )

@section('content')
    //oo

    <div class="container">
        <div class="row">

            @Auth
                @include( 'side_bar' )
            @endauth

            <div class="@Auth col-md-9 @elseauth col-md-12 @endauth col-md-offset-0" style="height: 650px; display: flex; align-items: center; justify-content: center">
                    <div class="panel panel-default pt-2">
                        <div class="panel_box w-100 bg-white rounded shadow-sm px-4">
                            <div class="header text-center py-3">
                                <h2 class="h2 brand_color" style="{{'admin.layouts.app' == $extend ? 'color:white':''}}">@yield('message')</h2>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection
