<script type="text/javascript">

    jQuery.prototype.change_labels = function( name ){
        $( this ).find( '.number-label' ).html( 'Number of ' + name + ' (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)' );
        $( this ).find( '.mark-label' ).html( 'Mark of ' + name + ' (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)');
        $( this ).find( '.ng-mark-label' ).html( name + ' Negative Mark/stamp (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)' );
        $( this ).find( '.ng-mark-range-label' ).html( name + ' Negative Mark Range (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)' );
    }


    const batch_type = "{{ old( 'batch_type', ($type_info->batch_type ?? '') )  }}";

    if( batch_type == 'combined' ){
        $( '#mcq2_data,#sba2_data' ).show( ).input_hidden( false );
    }else {
        $( '#mcq2_data' ).hide( ).input_hidden( true );
    }


    $(document).ready(function() {

        $( '#batch-type' ).change( () => {
            if( $('#batch-type').val() == 'combined' ) {

                $( '#mcq2_data' ).show().input_hidden( false );

                $( '#mcq_data' ).change_labels( 'MCQ-R' );
                $( '#mcq2_data' ).change_labels( 'MCQ-F' );

            } else {
                //alert('sdf');
                $( '#mcq2_data' ).hide().input_hidden( true );

                $( '#mcq_data' ).change_labels( 'MCQ' );
                $( '#mcq2_data' ).change_labels( 'MCQ' );

            }
        });


        $( '.nagetive_mark_range' ).each(function (index, item) {
            console.log('each jQuery: ', item);
            $(item).find('input').on('keyup', function () {
                console.log('ok..');
                const val = $(this).val( );

                if( val.length && !val.match(/^([0-9]+)|([0-9]+\-[0-9]+)$\,/g)) {
                    console.log( 'Invalid' )
                }

            });
        })

    })
</script>
