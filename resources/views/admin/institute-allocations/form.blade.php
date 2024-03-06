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
        <label class="col-md-3 control-label">Institute Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="text" name="name" value="{{ old('name') ?? $instituteAllocation->name }}" required placeholder="Institute Name" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Courses Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                {!! Form::select( 'course_ids[]', $courses, old( 'course_ids', $selected_courses ),
                    [  'class' => 'form-control select2' , 'multiple' => 'multiple' , 'required' => 'required', 'data-placeholder' => '--select--'  ] ) !!}
            </div>
        </div>
    </div>

</div>

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js"></script>
    <script>
        $(document).ready( function() {
            $('.select2').select2();
        });
    </script>
@endsection