<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script src="{{ asset('js/select2.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/moment-with-locales.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('js/react.production.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/react-dom.production.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/babel.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('js/select2-configs.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/content-selector.js') }}" type="text/javascript"></script>


<script>


    (function($){

        CKEDITOR.replace( 'terms_and_condition' );

        $.prototype.enableClassOrExamSelect2  = function ( ){
            $(this).find( ' .class-or-exam' ).each( function (){

                const contentType =  $(this).parents('.main,.child')
                    .find('.type-selection,.child-content-type').val();

                const videoType = () =>  $(this).parents('.main,.child')
                    .find('.class-type').val();

                $(this).select2({
                    required: true,
                    minimumInputLength: 0,
                    placeholder: `Type to search ${contentType}`,
                    escapeMarkup: function (markup) { return markup; },
                    ajax: {
                        url: '/admin/batch-schedules/lecture-exams',
                        dataType: 'json',
                        type: "GET",
                        delay: 600,
                        quietMillis: 50,
                        data: function (term) {
                            const input = getBatchDisFacData( );
                            input.content_type = contentType;
                            input.class_type = videoType();

                            return {
                                ...{term}, ...input
                            };
                        },
                        processResults: function (data) {

                            const items = data.topics || [ ];
                            console.log( 'contentType: ', contentType );

                            return {
                                results: $.map( items, function ( topicItem ) {

                                    const itemsKey = contentType == 'Exam' ? 'exams' : ( contentType == 'Class' ? 'lectures': '')

                                    const children = Array.isArray( topicItem[itemsKey] )
                                        ? topicItem[itemsKey]
                                        : [ ];

                                    return { id:topicItem.id, text: topicItem.name, children };
                                })
                            };
                        }
                    }
                });

            })
            return $(this);
        }

        $.prototype.setLabelsAndTexts = function ( {parentType, isChild} ) {

            const contentType = $(this).find('.type-selection,.child-content-type').val();

            $(this).find('.content-type-label').html( contentType );
            $(this).find('.child_class_add_btn_text').html( contentType == 'Exam' ? 'Add Solve Class':'Add Feedback Class' );

            $(this).find( '.class_type_text' ).html( parentType == 'Exam' ? 'Solve':'Feedback' );


            if( $(this).find( '.class-type.child-class-type' ) ) {
                $(this).find('.class-type.child-class-type').val( parentType == 'Exam' ? 2: 3 );
            }

        }

        $.prototype.enableMentorSelect2 = function( ){
            $(this).find('.form-control.mentor-list').select2();
            return $(this);
        }

        $.prototype.renderContent = function ( { changeClassOrExam, changeMentor, changeLabelsAndTexts, isChild, parentType } ){



            if( changeLabelsAndTexts) {
                $( this ).setLabelsAndTexts( {parentType, isChild} );
            }

            if( changeClassOrExam ) {
                if( isChild ) {
                    $( this ).enableClassOrExamSelect2( );
                }else {
                    $( this ).find( `.main.row` ).enableClassOrExamSelect2( );
                }
            }

            if( changeMentor ) {
                $( this ).enableMentorSelect2( );
            }

        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });

        let  batchDisFacData = { }; // Batch, discipline/faculty data
        const data = { };

        function getBatchDisFacData( batchData, b  ){
            return batchDisFacData;
        }

        function setBatchDisFacData( batchData, b  ){

            batchDisFacData = {};

            batchDisFacData.batch_id =  batchData.batch_id || '';
            batchDisFacData.year =  batchData.year || '';
            batchDisFacData.session_id =  batchData.session_id || '';
            batchDisFacData.institute_id =  batchData.institute_id || '';
            batchDisFacData.course_id =  batchData.course_id || '';
            batchDisFacData.faculty_id =  batchData.faculty_id || '';
            batchDisFacData.bcps_subject_id = batchData.bcps_subject_id || ''

            console.log( 'Changed ', b, batchDisFacData );

            if( data.slots ) {
                setTimeout( () => data.slots.enableClassOrExamSelect2(), 0 )
            }
        }

        (function ($) {

            const linkItems = $('#fb-links .fb-link-items');
            const emptyLinkItem = $('#empty-fb-link').html();

            function eachItem( ){
                return linkItems.find('.fb-link-item');
            }

            $.prototype.indexing = function ( index ){
                $(this).find('input').each( function (){
                    $(this).attr( 'name', $(this).attr('name').replace( /^fb_links\[[0-9a-z\{\}]*\]/i, `fb_links[${index}]` ) );
                });
            }

            $.prototype.manage_item = function ( index ){
                $( this ).indexing( index );

                $( this ).find( '.remove-item-btn' ).on( 'click', (e) =>  {
                    e.preventDefault();
                    if( confirm( "Are you sure?" ) )  $(this).remove( );
                    eachItem().each( (i,item) => $( item ).indexing( i ) )
                });
            };

            function fbLink(){

                function onFBLinkAddBtn( e ){
                    e.preventDefault();
                    const index  = eachItem().length;
                    linkItems.append( emptyLinkItem );
                    eachItem().last().manage_item( index );
                }

                eachItem().each( (i,item) => $( item ).manage_item( i ) );

                $('#fb-links .fb-link-actions').find('.add-fb-link-btn').on('click', onFBLinkAddBtn )
            }

            fbLink();

        }( jQuery));

        ( function( $ ) {

            const eventList = [];

            data.emptyScheduleContent = $('#empty-schedule-content').html();
            data.emptyScheduleChildContent = $('#empty-schedule-child-content').html();
            data.emptyScheduleSlot = $('#empty-schedule-slot').html();
            data.slots = $( "#exam-class .panel-body #schedule-slots" );
            data.slotAddBtn = $("#slot-add-btn");
            data.slot = slot( data );

            addEvent( data.slotAddBtn, 'click', data.slot.append );
            addEvent( () => data.slots.find( '.remove-slot' ), 'click', data.slot.remove );
            addEvent( () => data.slots.find( '.type-selection' ), 'click', data.slot.contentTypeSelection );
            addEvent( () => data.slots.find( '.add-schedule-content' ), 'click', data.slot.add_content );
            addEvent( () => data.slots.find( '.remove-content' ), 'click', data.slot.remove_content );
            addEvent( () => data.slots.find( '.add-child-content' ), 'click', data.slot.add_child_content );
            addEvent( () => data.slots.find( '.remove-child-content' ), 'click', data.slot.remove_child_content );


            const class_exam = async () => {
                return new Promise( ( resolve, reject ) => {
                    resolve( data );
                });
            }

            function enableTimeAndDate( ){
                if( $.prototype.datetimepicker ) {
                    $(this).find( '.timepicker' ).datetimepicker({
                        format: 'LT'
                    });
                }

                if( $.prototype.datepicker ) {
                    $(this).find( '.item-date' ).datepicker({
                        format: 'yyyy-mm-dd',
                        todayHighlight: true,
                        startDate: '1900-01-01',
                        endDate: '2035-01-01',
                    }).on('changeDate', function(e){
                        $(this).datepicker('hide');
                    });
                }
            }


            function reset_items( ){

                data.slots.find( '.schedule-slots' ).each( (  slotIndex, slotItem ) => {

                    $(slotItem).find( "[name]" ).each( function (){
                        const inputName = $(this).attr('name');
                        const changedInputName =  inputName.replace( /^details(\[[0-9a-z\-_]+\]|\[\])/ig, function() {
                            return `details[${slotIndex}]`;
                        });

                        $(this).attr( 'name', changedInputName )
                    });


                    $(slotItem).find('.schedule-contents .content').each( function ( contentIndex, contentItem ) {
                        $( contentItem ).find("[name]").each( function (){
                            const inputName = $(this).attr('name');
                            const changedInputName =  inputName.replace( /^details(\[[0-9a-z\-_]+\]|\[\])\[contents\](\[[0-9a-z\-_]+\]|\[\])/ig, function( m,g1,g2 ) {
                                g1 = g1.replace( /[\[\]]/g, '' );
                                return `details[${g1}][contents][${contentIndex}]`;
                            });

                            $(this).attr( 'name', changedInputName );
                        });
                    });

                });

            }

            function addEvent( selector, type , action ){
                eventList.push( { selector, type , action });
            }

            function events( ){

                eventList.forEach( ev => {
                    const selector = typeof ev.selector == 'function' ? ev.selector(): ev.selector;
                    $( selector ).off( ev.type  ).on( ev.type, ev.action )
                });

                reset_items( );
            }

            function slot(  data ){

                function container(){ return $(this).parents( '.schedule-slots' ); }

                function content_container( ){ return $(this).parents( '.content' ); }

                function remove( e ){
                    e.preventDefault( );
                    if( confirm( 'Are you sure?' )) {

                        const slot_id = container.call(this).find( '.slot_id' ).val();

                        if( slot_id ) {

                            $.ajax({
                                url: `/admin/batch-schedules/slot/${slot_id}/delete`,
                                type: 'DELETE',
                                success: (result) => {
                                    // console.log( result );

                                    if( result.success ) {
                                        container.call( this ).remove( );
                                        class_exam().then( events );
                                        alert( 'Slot Deleted!' );
                                    }

                                }
                            });
                        } else {
                            container.call( this ).remove( );
                            class_exam().then( events );
                        }

                    }
                }



                function add_content( e ){
                    e.preventDefault( );

                    container.call(this).find( '.schedule-contents').append( `${data.emptyScheduleContent}` );
                    container.call(this).find( '.schedule-contents .content' )
                        .last()
                        .renderContent({
                            changeClassOrExam: true,
                            changeMentor: true,
                            changeLabelsAndTexts: true,
                            parentType: container.call(this).find('.schedule-contents .content').find('.type-selection').val()
                        });

                    class_exam().then( events );
                }



                function add_child_content( e ){
                    e.preventDefault( );

                    const parentType = $(this).parents( '.main' ).find( '.type-selection' ).val();

                    content_container.call( this )
                        .find( '.children' )
                        .append( `${data.emptyScheduleChildContent}` );

                    content_container.call( this )
                        .find( '.children .child' ).last()
                        .renderContent({
                            changeClassOrExam: true,
                            changeMentor: true,
                            changeLabelsAndTexts: true,
                            isChild: true,
                            parentType
                        });

                    class_exam( ).then( events );
                }



                function remove_content_from_server(id, success, error){
                    $.ajax({
                        url: `/admin/batch-schedules/class-or-exam/${id}/delete`,
                        type: 'DELETE',
                        success,
                        error
                    });
                }

                function remove_content( e ){
                    e.preventDefault( );

                    if( confirm( 'Are you sure?\nfeedback or solve class \nwill be deleted' ) ) {

                        const class_or_exam_id = content_container.call( this ).find('.detail_id').val();

                        if( class_or_exam_id ) {
                            remove_content_from_server( class_or_exam_id, (result) => {
                                if( result.success ) {
                                    content_container.call( this ).remove();
                                    class_exam().then(events);
                                    alert( 'Content Deleted!' );
                                }
                            }, () => { })
                        } else {
                            content_container.call( this ).remove();
                            class_exam().then(events);

                        }

                    }
                }

                function remove_child_content( e ){
                    e.preventDefault( );

                    if( confirm( 'Are you sure?' ) ) {

                        const class_or_exam_id = content_container.call( this ).find('.detail_id').val( );

                        if( class_or_exam_id ) {
                            remove_content_from_server( class_or_exam_id, (result) => {
                                if( result.success ) {
                                    $(this).parents('.child').remove();
                                    class_exam( ).then( events );
                                    alert( 'Class Deleted!' );
                                }
                            }, () => { })

                        } else {
                            $(this).parents('.child').remove();
                            class_exam( ).then( events );
                        }
                    }
                }

                function append( e ){
                    e.preventDefault();
                    data.slots.append( `${data.emptyScheduleSlot}` );

                    class_exam( ).then( ( ) => {
                        events( );
                        if( lastSlot.find( '.schedule-contents .content' ).length == 0 ) {
                            lastSlot.find('.add-schedule-content').trigger('click', { });
                        }
                    });

                    enableTimeAndDate.call( data.slots.find('.schedule-slots').last() );
                    const lastSlot = data.slots.find('.schedule-slots').last( );
                    lastSlot.enableClassOrExamSelect2( );
                }

                function  contentTypeSelection( ){
                    $(this).parents( '.content' )
                        .renderContent( { changeClassOrExam: true, changeMentor: false, changeLabelsAndTexts: true, parentType: $(this).val() })
                }

                return { container, remove, append, add_content, add_child_content, remove_content, remove_child_content, contentTypeSelection }
            }

            function initiate( ){
                events( );
                enableTimeAndDate.call( data.slots );

                if( data.slots.find('.content').length == 0 ) {
                    data.slotAddBtn.trigger('click', { } );
                }

                data.slots.find('.content').each( function (){
                    $(this).renderContent( { changeClassOrExam: false, changeMentor: true, changeLabelsAndTexts: true, parentType: $(this).find('.type-selection').val() });
                });
            }

            class_exam().then( initiate );

        } ( jQuery ) );

        ( function( $, onChange ) {

            content_manager({
                onInit: onChange,
                year: { onChange },
                course: {
                    url: '/admin/batch-schedules/courses',
                    onChange
                },
                session: {
                    url: '/admin/batch-schedules/sessions',
                    onChange
                },
                batch: {
                    url: '/admin/batch-schedules/batches',
                    onChange
                },
                faculty: {
                    url: '/admin/batch-schedules/faculties',
                    onChange
                },
                bcps_discipline: {
                    url: '/admin/batch-schedules/bcps_disciplines',
                    onChange,
                    target: '#disciplines'
                },
            });


        }(jQuery, setBatchDisFacData));

    }(jQuery));



</script>
