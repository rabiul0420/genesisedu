@foreach ($contents as $content)
<div class="px-4 py-4 rounded border shadow flex items-center gap-4">
    <div class="text-sky-600" id="contentId{{ $content->id }}"></div>
    <label class="grow-0 shrink-0">
        <input type="checkbox" class="cursor-pointer" {{ in_array($content->id, $selected_content_ids) ? 'checked' : '' }} value="{{ $content->id }}" onclick="selectContent(this)">
    </label>
    <div class="grow shrink line-clamp-1 break-all">
        ID: {{ $content->id }} | {{ $content->name ?? '' }}
    </div>
</div>
@endforeach

<div class="col-span-full mt-6">
    {{ $contents->links('tailwind.components.search-method-paginator') }}
</div>