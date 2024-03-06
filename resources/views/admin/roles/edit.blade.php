@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Role Edit</li>
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
                        <i class="fa fa-reorder"></i>Role Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\RolesController@update',$role->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <input type="text" class="form-control" required value="{{$role->name}}" name="name">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Permissions</label>
                            <div class="col-md-9 permission-left">
                            @foreach($permissions as $row)
                                <div class="checkbox">
                                    <label><input name="permission[]" @php echo in_array($row->id,$exist_permission)?'checked':'' @endphp value="{{$row->id}}" type="checkbox">
                                        {{$row->name}}
                                    </label>
                                </div>
                                <div style="padding-left: 20px" class="row">
                                    <div class="">
                                        @foreach($row->child as $child)
                                            <div class="col-md-3">
                                                <div class="checkbox">
                                                    <label><input class="child-permission parent-{{$row->id}}" data-parent_id="{{$row->id}}" name="permission[]" @php echo in_array($child->id,$exist_permission)?'checked':'' @endphp value="{{$child->id}}" type="checkbox">
                                                        {{$child->name}}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @endforeach
                            </div>
                        </div>


                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Update</button>
                                <a  href="{{ url('admin/roles') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>

        </div>
    </div>

@endsection



@section('js')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
<script>

    jQuery(document).ready(function() {



        jQuery('[name="permission[]"]').on('click', function() {
            var parent_id = jQuery(this).val();
            jQuery(".parent-"+parent_id).prop('checked', jQuery(this).prop("checked"));
        })

        /*jQuery('.child-permission').on('click', function() {

            var child_parent_id = jQuery(this).attr("data-parent_id");

            if(jQuery('.parent-'+child_parent_id+':checked').length){
                jQuery("input[value='" + child_parent_id + "']").prop('checked', true);
            }else{
                jQuery("input[value='" + child_parent_id + "']").prop('checked', false);
            }
        })*/
    })
</script>
@endsection

