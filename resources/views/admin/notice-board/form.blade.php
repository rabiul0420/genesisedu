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
        <label class="col-md-3 control-label">Notice Title (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="text" name="title" required value="{{ old('title') ?? $noticeBoard->title }}" placeholder="Tile : max 100 characters" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Notice Description (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <textarea id="description" name="description" cols="30" rows="10" class="form-control" required>{{ old('description') ?? $noticeBoard->description }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Status  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
        <div class="col-md-6">
            <select name="status" class="form-control" required>
                <option {{ (old('status') ?? $noticeBoard->status) === 1 ? 'selected' : ''}} value="1">Active</option>
                <option {{ (old('status') ?? $noticeBoard->status) === 0 ? 'selected' : ''}} value="0">InActive</option>
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