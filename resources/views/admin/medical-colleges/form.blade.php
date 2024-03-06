@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Medical College Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="text" name="name" required value="{{ old('name') ?? $medicalCollege->name }}" placeholder="Medical College Name" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Select Type (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <select name="type" class="form-control" required>
                    <option value="">-- Select Medical Type --</option>
                    <option {{ (old('type') ?? $medicalCollege->type) === 'Govt' ? 'selected' : ''}} value="Govt">Govt</option>
                    <option {{ (old('type') ?? $medicalCollege->type) === 'Private' ? 'selected' : ''}} value="Private">Private</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Status  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
        <div class="col-md-6">
            <select name="status" class="form-control" required>
                <option {{ (old('status') ?? $medicalCollege->status) === 1 ? 'selected' : ''}} value="1">Active</option>
                <option {{ (old('status') ?? $medicalCollege->status) === 0 ? 'selected' : ''}} value="0">InActive</option>
            </select>
        </div>
    </div>

</div>

@section('js')
    <script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
    <script>
        // CKEDITOR.replace( 'title' );
        CKEDITOR.replace( 'description' );
    </script>
@endsection