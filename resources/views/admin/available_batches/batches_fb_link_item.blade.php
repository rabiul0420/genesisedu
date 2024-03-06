
<div class="link-item" style="margin-bottom: 10px">
    <input type='text' name="{{ $headline_name ?? '__headline_name__' }}" 
        placeholder='Head Line' value="{{$headline ?? ''}}" />

    <a href="javascript:void(0)" class="remove">&times;</a>

    <div class="links" style="margin-bottom: 10px">

        @php $link_contents = $link_contents ?? [ [ 'url' => '', 'title' => '' ] ]; @endphp

        @foreach( $link_contents as $content_index => $content )

            {{-- {{ print_r( $content ) }} --}}

            @include('admin.available_batches.batches_fb_link_content', [

                '_title'        => $content['title'] ?? '', 
                'url'           => $content['url'] ?? '',

                'title_name'    =>  ( $data_key ?? '__link_items__' ) .'['.($index ?? '__link_index__').'][link_contents][' . $content_index . '][title]',
                'url_name'      =>  ( $data_key ?? '__link_items__' ) .'['.($index?? '__link_index__').'][link_contents][' . $content_index . '][url]',
            ])

        @endforeach

    </div>
</div>