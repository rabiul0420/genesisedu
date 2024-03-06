<div style="width: 100%;">
    <label for="allBatchSelectButton">
        <input id="allBatchSelectButton" type="checkbox">
        All
    </label>
    <select id="batch_id" name="batch_id[]" class="form-control" required multiple>
        @foreach($batches as $id => $name)
        <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
</div>