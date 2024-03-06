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
        <label class="col-md-3 control-label">Slider Image</label>
        <div class="col-md-3">
            <div class="input-icon right">
                <input type="file" name="image" value="{{ old('image') ?? $bannerSlider->image }}" accept="image/*" class="form-control" onchange="document.getElementById('image-preview').src = window.URL.createObjectURL(this.files[0])">
                <p class="text-danger" style="font-size: 16px">Image size must be (width) <b>1000px</b> X <b>700px</b> (height)</p>
            </div>
        </div>
    </div>
    
    <div class="form-group"> 
        <label class="col-md-3 control-label">Status</label>
        <div class="col-md-3">
            <select name="status" class="form-control" required>
                <option {{ (old('status') ?? $bannerSlider->status) === 1 ? 'selected' : ''}} value="1">Active</option>
                <option {{ (old('status') ?? $bannerSlider->status) === 0 ? 'selected' : ''}} value="0">InActive</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Priority</label>
        <div class="col-md-3">
            <div class="input-icon right">
                <input type="number" name="priority" class="form-control" value="{{ old('priority') ?? $bannerSlider->priority ?? 0 }}">
            </div>
        </div>
    </div>

</div>