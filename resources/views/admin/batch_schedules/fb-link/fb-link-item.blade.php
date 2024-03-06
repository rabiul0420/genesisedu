<div class="form-group fb-link-item">
    <label class="col-md-2 control-label">Title (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </span> </label>
    <div class="col-md-3">
        <div class="input-icon right">
            <input  required type="text" name="fb_links[{{$index}}][title]"
                    value="{{$item['title'] ?? ''}}"
                    placeholder="Title"
                    class="form-control"/>
        </div>
    </div>

    <label class="col-md-1 control-label">Link (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </span> </label>
    <div class="col-md-3">
        <div class="input-icon right">
            <input  required type="url" name="fb_links[{{$index}}][link]"
                    value="{{$item['link'] ?? ''}}"
                    placeholder="Link"
                    class="form-control"/>
        </div>
    </div>
    <div class="col-md-1">
        <button class="btn btn-danger remove-item-btn">&times;</button>
    </div>
</div>