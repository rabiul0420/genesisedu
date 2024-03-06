let selected_exam_ids = Array.isArray( editing_data.seleted_exam_ids ) ? editing_data.seleted_exam_ids : [ ];
let seleted_video_ids = Array.isArray( editing_data.seleted_video_ids ) ? editing_data.seleted_video_ids : [ ];

console.log( 'selected_class_ids:prev ', seleted_video_ids );

function getSelectedVideoIds( ){
    return seleted_video_ids;
}
function getExamVideoIds( ){
    return seleted_exam_ids;
}

jQuery.prototype.content_list_select2 = function ( option ){
    if( typeof jQuery.prototype.select2 == 'function' ) {
        const content_type = option && option.type;
        const elemID = $( this ).attr( 'id' );
        let contentID = $( '#' + elemID ).val();

        const opt = {
            minimumInputLength: 3,
            escapeMarkup: function (markup) { return markup; },
            ajax: {

                url: '/admin/batch-schedules/lecture-exams-mentors',
                dataType: 'json',
                type: "GET",
                quietMillis: 50,

                data: function ( term ) {
                    term.content_type = option && option.type;

                    if( term.content_type === 'Class') {
                        term.video_type = option && ( option.video_type || 1 );
                        //a
                        //term.selected_content_ids = getSelectedVideoIds();
                    } else {
                        //a
                        //term.selected_content_ids = getExamVideoIds();
                    }

                    return term;
                },
                beforeSend: function () {
                    //removeAndReplaceContent( content_type, contentID, $(this).val( ) );
                    // contentID =  $(this).val( );
                    //
                    // if(  content_type == 'Exam') {
                    //     console.log( 'selected_class_ids:after>exam ', selected_exam_ids );
                    // }else {
                    //     console.log( 'selected_class_ids:after>class ', seleted_video_ids );
                    // }
                },
                processResults: function ( data ) {

                    return {
                        results: $.map( data.topics, function ( item ) {

                            const dt = {  "text": 'Topic(s): '+ item.name, "children" : []  }

                            const contents = ( option.type == 'Class' ) && Array.isArray( item.lectures )
                                ? item.lectures :
                                ( ( option.type == 'Exam' ) && Array.isArray( item.exams ) ? item.exams:[] );

                            contents.map( content => dt.children.push({ id: content.id, text: content.name }) );

                            return  dt;
                        })
                    };
                }
            }
        };

        // return $(this).select2( $.extend( option, opt ) );
        return $(this).select2( option );
    }
    return $(this);
}

function removeAndReplaceContent( content_type, contentId, replaceContentId ) {
    let _content_ids = content_type === 'Class' ? seleted_video_ids : selected_exam_ids;

    console.log( 'selected_class_ids:--- ', _content_ids, contentId );

    const ind = _content_ids.indexOf( Number(contentId) );

    if( replaceContentId ) {
        if( ind > -1 ) {
            _content_ids.splice( ind, 1,  Number( replaceContentId ) )
        }else {
            _content_ids.push( Number(replaceContentId) );
        }
    } else {
        _content_ids.splice( ind, 1 )
    }
}

function scheduleId(){
    return schedule_id ? schedule_id : 0;
}

function isEdit(){
    let act = action ? action : 'create';
    return act == 'edit';
}

function isDuplicating(){
    let act = action ? action : 'create';
    return act == 'duplicate';
}

function formAttributes(){
    return {
        action: (url || '/') + '/admin/batch-schedules/' + (  isEdit() ? 'update/' + scheduleId() : 'store' ),
        method: 'POST',
        className: 'form-horizontal',
        encType: 'multipart/form-data',
    }
}

function enableTimeAndDate( ){


    if( $.prototype.datetimepicker ) {
        $( '.timepicker' ).datetimepicker({
            format: 'LT'
        });
    }

    if( $.prototype.datepicker ) {
        $( '.item-date' ).datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            startDate: '1900-01-01',
            endDate: '2035-01-01',
        }).on('changeDate', function(e){
            $(this).datepicker('hide');
        });
    }
}