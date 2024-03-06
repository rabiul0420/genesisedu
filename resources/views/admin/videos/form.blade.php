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
        <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="text" name="name" value="{{ old('name') ?? $video->name }}" required placeholder="Name" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Url (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="text" name="url" value="{{ old('url') ?? $video->url }}" required placeholder="Url" class="form-control">
            </div>
        </div>
    </div>

</div>