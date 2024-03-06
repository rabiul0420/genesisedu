<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />

<div class="form-body">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-group">
        <label class="col-md-3 control-label">Title (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="text" name="title" value="{{ old('title') ?? $menu->title }}
" required placeholder="title" class="form-control">
                <input type="hidden" name="id" value="{{ $menu->id }}"
                        placeholder="title" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Parent (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
{{--                {!! Form::select( 'course_ids[]', $courses, old( 'course_ids', $selected_courses ),--}}
{{--                    [  'class' => 'form-control select2' , 'multiple' => 'multiple' , 'required' => 'required', 'data-placeholder' => '--select--'  ] ) !!}--}}

{{--                {!! Form::select( 'parent_id', $menus, old( 'parent_id'),--}}
{{--                    [ 'class' => 'form-control select2' , 'multiple' => 'multiple' , 'data-placeholder' => '---select---'  ] ) !!}--}}
                <select class="form-control select2" name="parent_id">
                    <option value="0">---Select---</option>
                    @foreach($menus as $index => $value)
                      <option value="{{$index}}" @if($menu->parent_id == $index ) selected @endif>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Permission (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
{{--                <input type="text" name="permission" value="{{ old('permission') ?? $menu->permission }}" required placeholder="permission" class="form-control">--}}

                <select class="form-control select3" name="permission">
                    <option value="0">---Select---</option>
                    @foreach($permissions as $id => $value)
                        <option value="{{$value->name}}" @if( $value->name == $menu->permission ) selected @endif>{{$value->name}} </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">URL (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="text" name="url" value="{{ old('url') ?? $menu->url }}" required placeholder="url" class="form-control">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Priority (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="number" name="priority" value="{{ old('priority') ?? $menu->priority }}" required placeholder="priority" class="form-control">
            </div>
        </div>
    </div>

    {{-- <div class="form-group">
        <label class="col-md-3 control-label">Icon (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <select class="form-control select-icon" name="icon">
                    <option value="0">---Select---</option>
                        <option value="fa fa-user-md">user</option>
                        <option value="fas fa-book">book</option>
                        <option value="fas fa-calendar">calendar</option>
                        <option value="fas fa-institution">institution</option>
                        <option value="fas fa-cogs">cogs</option>
                        <option value="fa fa-question-circle">circle</option>
                        <option value="fa fa-list-alt">list-alt</option>
                        <option value="fa fa-video-camera">video-camera</option>
                        <option value="fa fa-file-text">file-text</option>
                        <option value="fa fa-envelope">envelope</option>
                        <option value="fa fa-ticket">ticket</option>
                        <option value="fa fa-comments-o">comments</option>
                        <option value="fa fa-flag-o">flag</option>
                        <option value="fa fa-bullhorn">bullhorn</option>
                        <option value="fa fa-star">star</option>
                        <option value="fa fa-image">image</option>
                        <option value="fas fa-cog">Setting</option>
                </select>
            </div>
        </div>
    </div> --}}

    <div class="form-group">
        <label class="col-md-3 control-label">Icon Class Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="text" name="icon" value="{{ old('icon') ?? $menu->icon }}" required placeholder="Enter Icon Class" class="form-control">
            </div>
        </div>
    </div>

</div>

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js"></script>
    <script>
        $(document).ready( function() {
            $('.select2').select2();
            $('.select3').select2();
        });
    </script>
@endsection