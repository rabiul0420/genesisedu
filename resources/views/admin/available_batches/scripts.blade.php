<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script src="{{ asset('js/content-selector.js') }}"></script>
<script>
    CKEDITOR.replace( 'details' );

    function enableLinks(){
        $( ".items .link-item" ).each( function( link_index, link_item ){

            $(this).find( ".links .link-content" ).each(function( index, content ){

                if( index == 0 ) {

                    $(this).find('.link-action').html('<a href="" class="link-content-add">+</a>');

                

                    $(this).find('.link-action .link-content-add').on('click', function(e){
                        e.preventDefault();

                        const i = $( ".items .link-item" ).length - 1; //main index
                        const ci = $(link_item).find( ".links .link-content" ).length; //content index

                        let item = $( "#facebook-links #default-content" ).html( );

                        item = item.replace('__link_index__', link_index);
                        item = item.replace( '__title_name__', 'links['+link_index+'][link_contents]['+ci+'][title]' );
                        item = item.replace( '__url_name__', 'links['+link_index+'][link_contents]['+ci+'][url]' );

                        $(link_item).find( ".links" ).append( item );

                        $(link_item).find( ".links" ).find('.link-action').eq(ci).html('<a href="" class="link-content-remove">&times;</a>');
                    
                        $(link_item).find( '.link-action .link-content-remove' ).off( 'click' );
                        $(link_item).find( '.link-action .link-content-remove' ).on( 'click', remove_content );

                        //alert( 'Ready to add' );

                    });
                }else {
                    $(this).find('.link-action').html( '<a href="" class="link-content-remove">&times;</a>' );
                }

                function remove_content(e){
                    e.preventDefault();
                    if( confirm('Are you sure?') ) {
                        $(this).parents('.link-content').remove();
                    }
                }

                $(link_item).find( '.link-action .link-content-remove' ).off( 'click' );
                $(link_item).find( '.link-action .link-content-remove' ).on( 'click', remove_content );

            });
            
            console.log( $(this).find( '.remove' ) );
            $(this).find( '.remove' ).off( 'click');
            $(this).find( '.remove' ).on( 'click', ( ) => confirm("Are you sure?")? $( this ).remove():null );
        });
    }

    function insertLink( ){
        const i = $( ".items .link-item" ).length; //main index
        let item = $( "#facebook-links #default" ).html( );
        
        item = item.replace( '__title_name__', 'links[ '+i+' ][link_contents][0][title]' );
        item = item.replace( '__url_name__', 'links[ '+i+' ][link_contents][0][url]' );
        item = item.replace( '__headline_name__', 'links['+i+'][headline]' );
        item = item.replace( /__link_items__/g, 'links' );

        item = item.replace( /__link_index__/g, i );

        $( "#facebook-links .items" ).append( item );


        enableLinks();      
    }

    enableLinks();

    content_manager({
        institute: {
            onChange( data ){
                console.log( data );
            }
        },
        batch: {
            url: '/admin/available-batches/batches'
        },
        course: {
            url: '/admin/available-batches/courses'
        },
        session: {
            url: '/admin/available-batches/sessions'
        }
    });

</script>