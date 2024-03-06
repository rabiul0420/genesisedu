@extends('admin.layouts.app')

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>admin</li><i class="fa fa-angle-right"></i>
            <li>permission</li><i class="fa fa-angle-right"></i>
           <li>create</li>
        </ul>

    </div>
 
    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Permission Create
                    </div>
                </div>
                <div class="widget-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['permissions.store']]) !!}
                    {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                @if($errors->has('name'))
                    <p class="help-block">
                        {{ $errors->first('name') }}
                    </p>
                @endif
                <div class="form-group">
                    <label for="name">Parent</label>
                    @php $parent->prepend('Select Parent','0') @endphp
                    {!! Form::select('parent_id', $parent, old('parent_id'),['class'=>'form-control']) !!}
                </div>

                    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-primary']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
  
        </div>
    </div>
    <!-- END PAGE CONTENT-->

@endsection

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i> 
            </li>
            <li>permission Create</li>
        </ul>
    </div> 
    <div id="main" role="main">   
        <div id="content">
            @if(Session::has('message'))
                <div class="allert-message alert-success-message pgray  alert-lg" role="alert">
                    <p class=""> {{ Session::get('message') }}</p>
                </div>
            @endif
        <!-- widget grid -->
            <section id="widget-grid" class="">
                <article class="">
                    <div class="jarviswidget" id="wid-id-1" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false">
                        <header>                       
                             <h3> <i class="fa fa-reorder"></i>Permission Create </h3> 
                        </header>
                        <div>
                            <div class="widget-body">
                                    {!! Form::open(['method' => 'POST', 'route' => ['permissions.store']]) !!}
                                    {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                                    <p class="help-block"></p>
                                @if($errors->has('name'))
                                    <p class="help-block">
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif
                                <div class="form-group">
                                    <label for="name">Parent</label>
                                    @php $parent->prepend('Select Parent','0') @endphp
                                    {!! Form::select('parent_id', $parent, old('parent_id'),['class'=>'form-control']) !!}
                                </div>

                                    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-primary']) !!}
                                    {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/jasny-bootstrap.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('[name="role_id"]').change(function(){
                var id = $(this).val();
                if(id!=1){
                    $('.warehouse').removeClass('hidden');
                    $('[name="warehouse_id"]').attr('required', 'required');
                }else{
                    $('[name="warehouse_id"]').val('');
                    $('[name="warehouse_id"]').removeAttr('required');
                    $('.warehouse').addClass('hidden');
                }
            })
        })
    </script>
@endsection