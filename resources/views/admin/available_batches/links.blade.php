<!-- Admission Links  -->
<div class="panel panel-primary" style="border-color: rgb(238, 238, 238);">
<div class="panel-heading" style="background-color: rgb(238, 238, 238); color: black; border-color: rgb(238, 238, 238);">
    Admission Links
</div>
<div class="panel-body">
    <div class="container-fluid">
        <div class="row" id="facebook-links">

            <div id="default" style="height: 0; overflow:hidden">@include( 'admin.available_batches.batches_fb_link_item' )</div>
            <div id="default-content" style="height: 0; overflow:hidden">@include('admin.available_batches.batches_fb_link_content' )</div>

            <div class="items">


                @if( is_array( $links_array ) )

                    

                    @foreach( $links_array as $index => $link ) 
                        

                        @include( 'admin.available_batches.batches_fb_link_item', [
                            'index' => $index,
                            'data_key' => 'links',
                            'headline' => $link['headline'] ?? '',
                            'headline_name'     => 'links[' . $index . '][headline]',
                            'link_contents'  => $link[ 'link_contents' ]
                                ??
                                    [
                                        [ 'url' => ( $link['url'] ?? '' ), 'title' => ( $link['title'] ?? '' ) ] 
                                    ],
                        ])

                    @endforeach
                @endif

            </div>
            
            <a href="javascript:void(0)" class="btn btn-sm btn-primary" onClick="insertLink()" style="margin: 10px 0">+Add More</a>
        </div>
    </div>
</div>