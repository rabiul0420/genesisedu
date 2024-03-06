@extends('admin.layouts.app')






@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>admin</li><i class="fa fa-angle-right"></i>
            <li>permission</li><i class="fa fa-angle-right"></i>
            <li>edit</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Permission Edit
                    </div>
                </div>
                <div>
                            <div class="widget-body">
                {!! Form::model($permission, ['method' => 'PUT', 'route' => ['permissions.update', $permission->id]]) !!}




                                {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                                {!! Form::text('name',  $permission->name, ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('name'))
                                    <p class="help-block">
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif

                                <div class="form-group">
                                    <label for="name">Parent</label>
                                    @php $parent->prepend('Select Parent','0') @endphp
                                    {!! Form::select('parent_id', $parent, $permission->parent_id,['class'=>'form-control']) !!}
                                </div>




                {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
                </div>
            </div>
            </div>
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